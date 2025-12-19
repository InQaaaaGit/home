<?php
namespace local_cdo_unti2035bas\application\assessment;

use core_text;
use DateTime;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_assessment;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class assessment_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(moodle_service $moodleservice) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(
        stream_entity $stream,
        ?block_entity $block,
        ?module_entity $module,
        ?theme_entity $theme,
        assessment_entity $assessment
    ): statement_schema {
        $builder = new host_assessment();
        /** @var array<array<string, mixed>> */
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        if ($theme && $module && $block) {
            $sectionid = $theme->moodle->sectionid;
            /** @var array<int, array<string, mixed>> */
            $blocksections = $sections[$block->moodle->sectionid]['subsections'];
            /** @var array<int, array<string, mixed>> */
            $modulesections = $blocksections[$module->moodle->sectionid]['subsections'];
            /** @var array<int, array<string, mixed>> */
            $themesections = $modulesections[$sectionid]['subsections'];
            $currentsections = $themesections;
            $level = 'theme';
        } else if ($module && $block) {
            $sectionid = $module->moodle->sectionid;
            /** @var array<int, array<string, mixed>> */
            $blocksections = $sections[$block->moodle->sectionid]['subsections'];
            /** @var array<int, array<string, mixed>> */
            $modulesections = $blocksections[$sectionid]['subsections'];
            $currentsections = $modulesections;
            $level = 'module';
        } else if ($block) {
            $sectionid = $block->moodle->sectionid;
            /** @var array<int, array<string, mixed>> */
            $blocksections = $sections[$sectionid]['subsections'];
            $currentsections = $blocksections;
            $level = 'block';
        } else {
            $sectionid = $stream->moodle->sectionid;
            $currentsections = $sections;
            $level = 'final';
        }

        if ($assessment->lrid) {
            $builder->with_lrid($assessment->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);

        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_assessmentlevel($level);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $builder->with_academichourminutes($stream->academichourminutes);

        if ($block) {
            $builder->with_blocktype($block->type);
        }
        if ($module) {
            $builder->with_moodlemoduleid($module->moodle->sectionid);
            $builder->with_untimoduleid($module->unti->moduleid);
        }
        if ($theme) {
            $builder->with_moodlethemeid($theme->moodle->sectionid);
            $builder->with_untithemeid($theme->unti->themeid);
        }

        $assessmentname = $assessment->override->name;
        $assessmentdesc = $assessment->override->description;
        if (!$assessment->override->ismanual) {
            $mods = $this->moodleservice->get_activities($stream->moodle->courseid, $sectionid);
            $assessmentsections = array_filter(
                $currentsections,
                fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') !== false,
            );
            foreach ($assessmentsections as $section) {
                $mods += $this->moodleservice->get_activities($stream->moodle->courseid, $section['sectionid']);
            }
            $mod = $mods[$assessment->moodle->modid];
            $assessmentname = $assessmentname ?: $mod->name;
            $assessmentdesc = $assessmentdesc ?: $mod->intro;
        }
        $builder->with_activityname($assessmentname);
        $builder->with_activitydescription($assessmentdesc);
        $builder->with_moodleactivityid($assessment->moodle->modid);
        $builder->with_lectureshours($assessment->config->lectureshours);
        $builder->with_workshopshours($assessment->config->workshopshours);
        $builder->with_independenthours($assessment->config->independentworkhours);
        $builder->with_practice($assessment->config->haspractice);
        $builder->with_resultcomparability($assessment->config->resultcomparability);
        if ($level == 'final') {
            $builder->with_documenttype($assessment->config->documenttype);
        }
        return $builder->build();
    }
}
