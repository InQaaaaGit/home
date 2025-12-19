<?php
namespace local_cdo_unti2035bas\infrastructure\moodle;

use context_module;
use local_cdo_unti2035bas\utils;
use stdClass;


defined('MOODLE_INTERNAL') || die();

/** @var stdClass $CFG */
require_once("{$CFG->dirroot}/course/lib.php");
require_once("{$CFG->dirroot}/user/profile/lib.php");


class moodle_service {
    public function get_config(): stdClass {
        return (object)get_config('moodle');
    }

    public function get_plugin_config(): stdClass {
        return (object)get_config('local_cdo_unti2035bas');
    }

    /**
     * @param array<int> $ids
     * @return array<int, array<string, string|int>>
     */
    public function get_courses(array $ids): array {
        global $DB;
        $courses = $DB->get_records_list('course', 'id', $ids);
        [$sql, $params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'cid', true, 0);
        $rootsections = array_map(
            fn($row) => $row->id,
            $DB->get_records_select('course_sections', "section = 0 AND course {$sql}", $params, '', 'course, id')
        );
        return array_map(
            fn($c) => [
                'fullname' => format_string($c->fullname),
                'summary' => format_string($c->summary),
                'sectionid' => $rootsections[$c->id],
            ],
            $courses
        );
    }

    /**
     * @param array<int> $ids
     * @return array<int, string>
     */
    public function get_groups(array $ids): array {
        global $DB;
        $groups = $DB->get_records_list('groups', 'id', $ids);
        return array_map(fn($c) => format_string($c->name), $groups);
    }

    /**
     * @return array<moodle_user_dto>
     * TODO: filter only students, or add role in moodle_user_dto
     */
    public function get_group_students(int $groupid, string $customfielduntiid): array {
        global $DB;
        $groupmembers = groups_get_members($groupid, 'u.*', 'lastname ASC, firstname ASC');
        $students = [];
        foreach ($groupmembers as $user) {
            $customfields = profile_get_user_fields_with_data($user->id);
            $untiid = null;
            foreach ($customfields as $field) {
                if ($field->field->shortname == $customfielduntiid) {
                    $untiid = $field->field->data;
                    break;
                }
            }
            $students[] = new moodle_user_dto($user->id, $untiid, fullname($user));
        }
        return $students;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_sections(int $courseid): array {
        global $DB;
        $moodlesections = $DB->get_records('course_sections', ['course' => $courseid]);
        $flexconfig = $DB->get_records(
            'course_format_options',
            ['courseid' => $courseid, 'format' => 'flexsections', 'name' => 'parent'],
        );
        usort($flexconfig, fn($a, $b) => $a->value - $b->value);
        $flexconfiggrp = utils::groupby($flexconfig, fn($c) => $c->value);
        $numbertosectionmap = [0 => []];
        foreach ($flexconfiggrp as $num => $grp) {
            $sections = &$numbertosectionmap[$num];
            foreach ($grp as $config) {
                $moodlesection = $moodlesections[$config->sectionid];
                unset($section);
                $section = [
                    'name' => format_string($moodlesection->name),
                    'summary' => format_string($moodlesection->summary),
                    'sectionid' => $moodlesection->id,
                    'number' => $moodlesection->section,
                    'subsections' => [],
                ];
                $numbertosectionmap[$moodlesection->section] = &$section['subsections'];
                $sections[$moodlesection->id] = $section;
            }
            uasort($sections, fn($a, $b) => $a['number'] - $b['number']);
        }
        return $numbertosectionmap[0];
    }

    /**
     * @return array<int, moodle_activity_dto>
     */
    public function get_activities(int $courseid, int $sectionid): array {
        global $DB;
        $modules = $DB->get_records('modules');
        $section = $DB->get_record(
            'course_sections',
            ['course' => $courseid, 'id' => $sectionid],
            '*',
            MUST_EXIST,
        );
        $sequence = array_map(fn($v) => (int)trim($v), explode(',', $section->sequence));
        [$idinsql, $params] = $DB->get_in_or_equal($sequence, SQL_PARAMS_NAMED, 'id', true, 0);
        $params += [
            'course' => $courseid,
            'section' => $sectionid,
        ];
        $coursemodules = $DB->get_records_select(
            'course_modules',
            "course = :course AND section = :section AND deletioninprogress = False AND id {$idinsql}",
            $params,
        );
        /** @var array<string, array<stdClass>> $coursemodulesgrp */
        $coursemodulesgrp = utils::groupby($coursemodules, fn($m) => $modules[$m->module]->name);
        $activitiesdata = [];
        if (isset($coursemodulesgrp['resource'])) {
            $activitiesdata += $this->get_mods_resource_data($coursemodulesgrp['resource']);
        }
        if (isset($coursemodulesgrp['page'])) {
            $activitiesdata += $this->get_mods_page_data($coursemodulesgrp['page']);
        }
        if (isset($coursemodulesgrp['quiz'])) {
            $activitiesdata += $this->get_mods_quiz_data($coursemodulesgrp['quiz']);
        }
        $activities = [];
        $order = 0;
        foreach ($sequence as $moduleid) {
            if (!isset($activitiesdata[$moduleid])) {
                continue;
            }
            /** @var string $modulename */
            $modulename = $activitiesdata[$moduleid]['modulename'];
            $activities[$moduleid] = moodle_activity_dto::from_array([
                'activitytype' => $modulename,
                'activityid' => $moduleid,
                'name' => $activitiesdata[$moduleid]['name'],
                'intro' => $activitiesdata[$moduleid]['intro'],
                'fileinfo' => $this->get_mod_fileinfo($modulename, $moduleid),
                'order' => ++$order,
            ]);
        }
        return $activities;
    }

    /**
     * @param array<stdClass> $modulesresource
     * @return array<int, array<string, mixed>>
     */
    private function get_mods_resource_data(array $modulesresource): array {
        global $DB;
        $data = [];
        $resourceids = array_map(fn($m) => (int)$m->instance, $modulesresource);
        /** @var array<int, stdClass> $coursemodulesresource */
        $coursemodulesresource = array_combine($resourceids, $modulesresource);
        [$select, $params] = $DB->get_in_or_equal($resourceids);
        $resourceactivities = $DB->get_records_select('resource', "id {$select}", $params);
        foreach ($resourceactivities as $resource) {
            $coursemodule = $coursemodulesresource[$resource->id];
            $data[$coursemodule->id] = [
                'modulename' => 'resource',
                'name' => $resource->name,
                'intro' => strip_tags($resource->intro),
                // 'intro' => format_module_intro(
                // 'resource',
                // $resource,
                // $coursemodule->id,
                // ),
            ];
        }
        return $data;
    }

    /**
     * @param array<stdClass> $modulespage
     * @return array<int, array<string, mixed>>
     */
    private function get_mods_page_data(array $modulespage): array {
        global $DB;
        $data = [];
        $pageids = array_map(fn($m) => (int)$m->instance, $modulespage);
        /** @var array<int, stdClass> $coursemodulespage */
        $coursemodulespage = array_combine($pageids, $modulespage);
        [$select, $params] = $DB->get_in_or_equal($pageids);
        $pageactivities = $DB->get_records_select('page', "id {$select}", $params);
        foreach ($pageactivities as $page) {
            $coursemodule = $coursemodulespage[$page->id];
            $data[$coursemodule->id] = [
                'modulename' => 'page',
                'name' => $page->name,
                'intro' => strip_tags($page->intro),
            ];
        }
        return $data;
    }

    /**
     * @param array<stdClass> $modulesquiz
     * @return array<int, array<string, mixed>>
     */
    private function get_mods_quiz_data(array $modulesquiz): array {
        global $DB;
        $data = [];
        $quizids = array_map(fn($m) => (int)$m->instance, $modulesquiz);
        /** @var array<int, stdClass> $coursemodulesquiz */
        $coursemodulesquiz = array_combine($quizids, $modulesquiz);
        [$select, $params] = $DB->get_in_or_equal($quizids);
        $quizactivities = $DB->get_records_select('quiz', "id {$select}", $params);
        foreach ($quizactivities as $quiz) {
            $coursemodule = $coursemodulesquiz[$quiz->id];
            $data[$coursemodule->id] = [
                'modulename' => 'quiz',
                'name' => $quiz->name,
                'intro' => strip_tags($quiz->intro),
                // 'intro' => format_module_intro(
                // 'quiz',
                // $quiz,
                // $coursemodule->id,
                // ),
            ];
        }
        return $data;
    }

    private function get_mod_fileinfo(string $modulename, int $moduleid): ?moodle_fileinfo_dto {
        global $DB;
        if ($modulename == 'resource') {
            $context = context_module::instance($moduleid);
            $fs = get_file_storage();
            $system = $fs->get_file_system();
            $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0);
            $file = array_values(array_filter($files, fn($f) => $f->get_filename() != '.'))[0];
            $filepath = $system->get_local_path_from_storedfile($file);
            return new moodle_fileinfo_dto(
                $file->get_filename(),
                $file->get_mimetype(),
                $file->get_filesize(),
                $filepath,
            );
        }
        return null;
    }
}
