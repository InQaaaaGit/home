<?php
namespace local_cdo_unti2035bas\application\activity;

use DateTime;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\mediainfo\mediainfo_service;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_activity;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class activity_xapi_service {
    private moodle_service $moodleservice;
    private mediainfo_service $mediainfoservice;

    public function __construct(moodle_service $moodleservice, mediainfo_service $mediainfoservice) {
        $this->moodleservice = $moodleservice;
        $this->mediainfoservice = $mediainfoservice;
    }

    public function execute(
        stream_entity $stream,
        block_entity $block,
        module_entity $module,
        theme_entity $theme,
        activity_entity $activity
    ): statement_schema {
        $builder = new author_activity();
        if ($activity->lrid) {
            $builder->with_lrid($activity->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);

        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $builder->with_academichourminutes($stream->academichourminutes);

        $builder->with_blocktype($block->type);
        $builder->with_moodlemoduleid($module->moodle->sectionid);
        $builder->with_moodlethemeid($theme->moodle->sectionid);
        $builder->with_untithemeid($theme->unti->themeid);
        $builder->with_untimoduleid($module->unti->moduleid);

        $activityname = $activity->override->name;
        $activitydesc = $activity->override->description;
        $builder->with_activitytype($activity->type);
        if (!$activity->override->ismanual) {
            $mods = $this->moodleservice->get_activities($stream->moodle->courseid, $theme->moodle->sectionid);
            $mod = $mods[$activity->moodle->modid];

            if ($mod->fileinfo && strpos($mod->fileinfo->mimetype, 'video') !== false) {
                $builder->with_activitytype('video');
                $duration = $this->mediainfoservice->get_duration($mod->fileinfo->filepath);
                $builder->with_videolength($duration);
                $builder->with_isvideo();
            } else {
                $builder->with_activitytype('article');
            }
            $activityname = $activityname ?: $mod->name;
            $activitydesc = $activitydesc ?: $mod->intro;
        }
        if ($activity->type == 'practice') {
            $builder->with_admittanceform($activity->config->admittanceform);
            $builder->with_resultcomparability($activity->config->resultcomparability);
        }
        $builder->with_activityname($activityname);
        $builder->with_activitydescription($activitydesc);
        $builder->with_moodleactivityid($activity->moodle->modid);
        $builder->with_lectureshours($activity->config->lectureshours);
        $builder->with_workshopshours($activity->config->workshopshours);
        $builder->with_independenthours($activity->config->independentworkhours);
        return $builder->build();
    }
}
