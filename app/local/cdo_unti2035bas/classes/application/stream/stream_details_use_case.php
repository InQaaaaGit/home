<?php
namespace local_cdo_unti2035bas\application\stream;

use core_text;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_activity_dto;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class stream_details_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private factdef_repository $factdefrepo;
    private moodle_service $moodleservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo,
        factdef_repository $factdefrepo,
        moodle_service $moodleservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->factdefrepo = $factdefrepo;
        $this->moodleservice = $moodleservice;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(int $streamid): array {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        [$course] = array_values(
            $this->moodleservice->get_courses([$stream->moodle->courseid])
        );
        [$group] = array_values(
            $this->moodleservice->get_groups([$stream->moodle->groupid])
        );
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        $streamtitle = $stream->override->name;
        if (!$stream->override->ismanual) {
            $streamtitle = $streamtitle ?: "{$course['fullname']} @ {$group}";
        }
        $streamrow = [
            'streamid' => $stream->id,
            'object_' => 'stream_entity',
            'objectid' => $stream->id,
            'objectversion' => $stream->version,
            'title' => $streamtitle,
            'lrid' => $stream->lrid,
            'changed' => $stream->changed,
            'deleted' => $stream->deleted,
            'level' => 'root',
            'lectureshours' => 0,
            'workshopshours' => 0,
            'independentworkhours' => 0,
        ];
        $res = [&$streamrow];
        $blocks = $this->blockrepo->read_by_streamid($streamid);
        $factdefs = $this->factdefrepo->read_all_by_streamid($streamid);
        $factdefsactivity = [];
        $factdefsassessment = [];
        foreach ($factdefs as $factdef) {
            if ($factdef->baseobject == 'activity_entity') {
                $factdefsactivity[$factdef->baseobjectid] = $factdef;
            } elseif ($factdef->baseobject == 'assessment_entity') {
                $factdefsassessment[$factdef->baseobjectid] = $factdef;
            }
        }
        foreach ($blocks as $block) {
            unset($blockrow);
            $blocktitle = $block->override->name;
            if (!$block->override->ismanual) {
                $blocktitle = $blocktitle ?: $sections[$block->moodle->sectionid]['name'] ?? null;
                $blocktitle = $blocktitle ?: get_string('deleted', 'local_cdo_unti2035bas');
            }
            $blockrow = [
                'streamid' => $stream->id,
                'object_' => 'block_entity',
                'objectid' => $block->id,
                'objectversion' => $block->version,
                'title' => $blocktitle,
                'lrid' => $block->lrid,
                'changed' => $block->changed,
                'deleted' => $block->deleted,
                'level' => 'stream',
                'type_' => $block->type,
                'lectureshours' => 0,
                'workshopshours' => 0,
                'independentworkhours' => 0,
            ];
            $res[] = &$blockrow;
            /** @var int */
            $blockid = $block->id;
            $modules = $this->modulerepo->read_by_blockid($blockid);
            /** @var array<int, array<string, mixed>> */
            $blocksections = $sections[$block->moodle->sectionid]['subsections'] ?? [];
            foreach ($modules as $module) {
                unset($modulerow);
                $moduletitle = $module->override->name;
                if (!$module->override->ismanual) {
                    $moduletitle = $moduletitle ?: $blocksections[$module->moodle->sectionid]['name'] ?? null;
                    $moduletitle = $moduletitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                }
                $modulerow = [
                    'streamid' => $stream->id,
                    'object_' => 'module_entity',
                    'objectid' => $module->id,
                    'objectversion' => $module->version,
                    'title' => $moduletitle,
                    'lrid' => $module->lrid,
                    'changed' => $module->changed,
                    'deleted' => $module->deleted,
                    'level' => 'block',
                    'lectureshours' => 0,
                    'workshopshours' => 0,
                    'independentworkhours' => 0,
                ];
                $res[] = &$modulerow;
                /** @var int */
                $moduleid = $module->id;
                /** @var array<int, array<string, mixed>> */
                $modulesections = $blocksections[$module->moodle->sectionid]['subsections'] ?? [];
                $themes = $this->themerepo->read_by_moduleid($moduleid);
                foreach ($themes as $theme) {
                    unset($themerow);
                    $themetitle = $theme->override->name;
                    if (!$theme->override->ismanual) {
                        $themetitle = $themetitle ?: $modulesections[$theme->moodle->sectionid]['name'] ?? null;
                        $themetitle = $themetitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                    }
                    $themerow = [
                        'streamid' => $stream->id,
                        'object_' => 'theme_entity',
                        'objectid' => $theme->id,
                        'objectversion' => $theme->version,
                        'title' => $themetitle,
                        'lrid' => $theme->lrid,
                        'changed' => $theme->changed,
                        'deleted' => $theme->deleted,
                        'level' => 'module',
                        'lectureshours' => 0,
                        'workshopshours' => 0,
                        'independentworkhours' => 0,
                    ];
                    $res[] = &$themerow;
                    /** @var int */
                    $themeid = $theme->id;
                    $mods = null;
                    /** @var array<int, array<string, mixed>> $themesections */
                    $themesections = $modulesections[$theme->moodle->sectionid]['subsections'] ?? [];
                    if (!$theme->deleted && !$theme->override->ismanual) {
                        $mods = $this->get_relevant_mods($stream->moodle->courseid, $theme->moodle->sectionid, $themesections);
                    }
                    $activities = $this->activityrepo->read_by_themeid($themeid);
                    foreach ($activities as $activity) {
                        $activitytitle = $activity->override->name;
                        if (!$activity->override->ismanual) {
                            $activitytitle = $activitytitle ?: $mods[$activity->moodle->modid]->name ?? null;
                            $activitytitle = $activitytitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                        }
                        $res[] = [
                            'streamid' => $stream->id,
                            'object_' => 'activity_entity',
                            'objectid' => $activity->id,
                            'objectversion' => $activity->version,
                            'title' => $activitytitle,
                            'lrid' => $activity->lrid,
                            'changed' => $activity->changed,
                            'deleted' => $activity->deleted,
                            'level' => 'theme',
                            'type_' => $block->type,
                            'lectureshours' => $activity->config->lectureshours,
                            'workshopshours' => $activity->config->workshopshours,
                            'independentworkhours' => $activity->config->independentworkhours,
                        ];
                        if (!$activity->deleted) {
                            $themerow['lectureshours'] += $activity->config->lectureshours;
                            $themerow['workshopshours'] += $activity->config->workshopshours;
                            $themerow['independentworkhours'] += $activity->config->independentworkhours;
                            $modulerow['lectureshours'] += $activity->config->lectureshours;
                            $modulerow['workshopshours'] += $activity->config->workshopshours;
                            $modulerow['independentworkhours'] += $activity->config->independentworkhours;
                            $blockrow['lectureshours'] += $activity->config->lectureshours;
                            $blockrow['workshopshours'] += $activity->config->workshopshours;
                            $blockrow['independentworkhours'] += $activity->config->independentworkhours;
                            $streamrow['lectureshours'] += $activity->config->lectureshours;
                            $streamrow['workshopshours'] += $activity->config->workshopshours;
                            $streamrow['independentworkhours'] += $activity->config->independentworkhours;
                        }
                        if ($factdef = $factdefsactivity[$activity->id] ?? null) {
                            $res[] = [
                                'streamid' => $stream->id,
                                'object_' => 'factdef_entity',
                                'objectid' => $factdef->id,
                                'objectversion' => $factdef->version,
                                'title' => $activitytitle,
                                'lrid' => $factdef->lrid,
                                'changed' => $factdef->changed,
                                'deleted' => $factdef->deleted,
                                'level' => 'theme',
                                'lectureshours' => 0,
                                'workshopshours' => 0,
                                'independentworkhours' => 0,
                                'basetype' => 'assessment',
                            ];
                        }
                    }
                    $assessments = $this->assessmentrepo->read_by_themeid($themeid);
                    foreach ($assessments as $assessment) {
                        $assessmenttitle = $assessment->override->name;
                        if (!$assessment->override->ismanual) {
                            $assessmenttitle = $assessmenttitle ?: $mods[$assessment->moodle->modid]->name ?? null;
                            $assessmenttitle = $assessmenttitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                        }
                        $res[] = [
                            'streamid' => $stream->id,
                            'object_' => 'assessment_entity',
                            'objectid' => $assessment->id,
                            'objectversion' => $assessment->version,
                            'title' => $assessmenttitle,
                            'lrid' => $assessment->lrid,
                            'changed' => $assessment->changed,
                            'deleted' => $assessment->deleted,
                            'level' => 'theme',
                            'lectureshours' => $assessment->config->lectureshours,
                            'workshopshours' => $assessment->config->workshopshours,
                            'independentworkhours' => $assessment->config->independentworkhours,
                        ];
                        if (!$assessment->deleted) {
                            $themerow['lectureshours'] += $assessment->config->lectureshours;
                            $themerow['workshopshours'] += $assessment->config->workshopshours;
                            $themerow['independentworkhours'] += $assessment->config->independentworkhours;
                            $modulerow['lectureshours'] += $assessment->config->lectureshours;
                            $modulerow['workshopshours'] += $assessment->config->workshopshours;
                            $modulerow['independentworkhours'] += $assessment->config->independentworkhours;
                            $blockrow['lectureshours'] += $assessment->config->lectureshours;
                            $blockrow['workshopshours'] += $assessment->config->workshopshours;
                            $blockrow['independentworkhours'] += $assessment->config->independentworkhours;
                            $streamrow['lectureshours'] += $assessment->config->lectureshours;
                            $streamrow['workshopshours'] += $assessment->config->workshopshours;
                            $streamrow['independentworkhours'] += $assessment->config->independentworkhours;
                        }
                        if ($factdef = $factdefsassessment[$assessment->id] ?? null) {
                            $res[] = [
                                'streamid' => $stream->id,
                                'object_' => 'factdef_entity',
                                'objectid' => $factdef->id,
                                'objectversion' => $factdef->version,
                                'title' => $assessmenttitle,
                                'lrid' => $factdef->lrid,
                                'changed' => $factdef->changed,
                                'deleted' => $factdef->deleted,
                                'level' => 'theme',
                                'lectureshours' => 0,
                                'workshopshours' => 0,
                                'independentworkhours' => 0,
                                'basetype' => 'assessment',
                            ];
                        }
                    }
                }
                if ($assessments = $this->assessmentrepo->read_by_moduleid($moduleid)) {
                    $mods = null;
                    if (!$module->deleted && !$module->override->ismanual) {
                        $mods = $this->get_relevant_mods($stream->moodle->courseid, $module->moodle->sectionid, $modulesections);
                    }
                }
                foreach ($assessments as $assessment) {
                    $assessmenttitle = $assessment->override->name;
                    if (!$assessment->override->ismanual) {
                        $assessmenttitle = $assessmenttitle ?: $mods[$assessment->moodle->modid]->name ?? null;
                        $assessmenttitle = $assessmenttitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                    }
                    $res[] = [
                        'streamid' => $stream->id,
                        'object_' => 'assessment_entity',
                        'objectid' => $assessment->id,
                        'objectversion' => $assessment->version,
                        'title' => $assessmenttitle,
                        'lrid' => $assessment->lrid,
                        'changed' => $assessment->changed,
                        'deleted' => $assessment->deleted,
                        'level' => 'module',
                        'lectureshours' => $assessment->config->lectureshours,
                        'workshopshours' => $assessment->config->workshopshours,
                        'independentworkhours' => $assessment->config->independentworkhours,
                    ];
                    if (!$assessment->deleted) {
                        $modulerow['lectureshours'] += $assessment->config->lectureshours;
                        $modulerow['workshopshours'] += $assessment->config->workshopshours;
                        $modulerow['independentworkhours'] += $assessment->config->independentworkhours;
                        $blockrow['lectureshours'] += $assessment->config->lectureshours;
                        $blockrow['workshopshours'] += $assessment->config->workshopshours;
                        $blockrow['independentworkhours'] += $assessment->config->independentworkhours;
                        $streamrow['lectureshours'] += $assessment->config->lectureshours;
                        $streamrow['workshopshours'] += $assessment->config->workshopshours;
                        $streamrow['independentworkhours'] += $assessment->config->independentworkhours;
                    }
                    if ($factdef = $factdefsassessment[$assessment->id] ?? null) {
                        $res[] = [
                            'streamid' => $stream->id,
                            'object_' => 'factdef_entity',
                            'objectid' => $factdef->id,
                            'objectversion' => $factdef->version,
                            'title' => $assessmenttitle,
                            'lrid' => $factdef->lrid,
                            'changed' => $factdef->changed,
                            'deleted' => $factdef->deleted,
                            'level' => 'module',
                            'lectureshours' => 0,
                            'workshopshours' => 0,
                            'independentworkhours' => 0,
                            'basetype' => 'assessment',
                        ];
                    }
                }
            }
            if ($assessments = $this->assessmentrepo->read_by_blockid($blockid)) {
                $mods = null;
                if (!$block->deleted && !$block->override->ismanual) {
                    $mods = $this->get_relevant_mods(
                        $stream->moodle->courseid,
                        $block->moodle->sectionid,
                        $blocksections,
                    );
                }
            }
            foreach ($assessments as $assessment) {
                $assessmenttitle = $assessment->override->name;
                if (!$assessment->override->ismanual) {
                    $assessmenttitle = $assessmenttitle ?: $mods[$assessment->moodle->modid]->name ?? null;
                    $assessmenttitle = $assessmenttitle ?: get_string('deleted', 'local_cdo_unti2035bas');
                }
                $res[] = [
                    'streamid' => $stream->id,
                    'object_' => 'assessment_entity',
                    'objectid' => $assessment->id,
                    'objectversion' => $assessment->version,
                    'title' => $assessmenttitle,
                    'lrid' => $assessment->lrid,
                    'changed' => $assessment->changed,
                    'deleted' => $assessment->deleted,
                    'level' => 'block',
                    'lectureshours' => $assessment->config->lectureshours,
                    'workshopshours' => $assessment->config->workshopshours,
                    'independentworkhours' => $assessment->config->independentworkhours,
                ];
                if (!$assessment->deleted) {
                    $blockrow['lectureshours'] += $assessment->config->lectureshours;
                    $blockrow['workshopshours'] += $assessment->config->workshopshours;
                    $blockrow['independentworkhours'] += $assessment->config->independentworkhours;
                    $streamrow['lectureshours'] += $assessment->config->lectureshours;
                    $streamrow['workshopshours'] += $assessment->config->workshopshours;
                    $streamrow['independentworkhours'] += $assessment->config->independentworkhours;
                }
                if ($factdef = $factdefsassessment[$assessment->id] ?? null) {
                    $res[] = [
                        'streamid' => $stream->id,
                        'object_' => 'factdef_entity',
                        'objectid' => $factdef->id,
                        'objectversion' => $factdef->version,
                        'title' => $assessmenttitle,
                        'lrid' => $factdef->lrid,
                        'changed' => $factdef->changed,
                        'deleted' => $factdef->deleted,
                        'level' => 'block',
                        'lectureshours' => 0,
                        'workshopshours' => 0,
                        'independentworkhours' => 0,
                        'basetype' => 'assessment',
                    ];
                }
            }
        }
        if ($assessments = $this->assessmentrepo->read_by_streamid($streamid)) {
            $mods = null;
            if (!$stream->override->ismanual) {
                $mods = $this->get_relevant_mods(
                    $stream->moodle->courseid,
                    $stream->moodle->sectionid,
                    $sections,
                );
            }
        }
        foreach ($assessments as $assessment) {
            $assessmenttitle = $assessment->override->name;
            if (!$assessment->override->ismanual) {
                $assessmenttitle = $assessmenttitle ?: $mods[$assessment->moodle->modid]->name ?? null;
                $assessmenttitle = $assessmenttitle ?: get_string('deleted', 'local_cdo_unti2035bas');
            }
            $res[] = [
                'streamid' => $stream->id,
                'object_' => 'assessment_entity',
                'objectid' => $assessment->id,
                'objectversion' => $assessment->version,
                'title' => $assessmenttitle,
                'lrid' => $assessment->lrid,
                'changed' => $assessment->changed,
                'deleted' => $assessment->deleted,
                'level' => 'stream',
                'lectureshours' => $assessment->config->lectureshours,
                'workshopshours' => $assessment->config->workshopshours,
                'independentworkhours' => $assessment->config->independentworkhours,
            ];
            if (!$assessment->deleted) {
                $streamrow['lectureshours'] += $assessment->config->lectureshours;
                $streamrow['workshopshours'] += $assessment->config->workshopshours;
                $streamrow['independentworkhours'] += $assessment->config->independentworkhours;
            }
            if ($factdef = $factdefsassessment[$assessment->id] ?? null) {
                $res[] = [
                    'streamid' => $stream->id,
                    'object_' => 'factdef_entity',
                    'objectid' => $factdef->id,
                    'objectversion' => $factdef->version,
                    'title' => $assessmenttitle,
                    'lrid' => $factdef->lrid,
                    'changed' => $factdef->changed,
                    'deleted' => $factdef->deleted,
                    'level' => 'stream',
                    'lectureshours' => 0,
                    'workshopshours' => 0,
                    'independentworkhours' => 0,
                    'basetype' => 'assessment',
                ];
            }
        }
        return $res;
    }

    /**
     * @param int $courseid
     * @param int $sectionid
     * @param array<int, array<string, mixed>> $subsections
     * @return array<int, moodle_activity_dto>
     */
    private function get_relevant_mods(
        int $courseid,
        int $sectionid,
        array $subsections
    ): array {
        $mods = $this->moodleservice->get_activities(
            $courseid,
            $sectionid,
        );
        $assessmentsections = array_filter(
            $subsections,
            fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') !== false,
        );
        foreach ($assessmentsections as $section) {
            $mods += $this->moodleservice->get_activities(
                $courseid,
                $section['sectionid'],
            );
        }
        return $mods;
    }
}
