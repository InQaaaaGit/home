<?php
namespace local_cdo_unti2035bas\application\theme;

use DateTime;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_theme;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class theme_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(moodle_service $moodleservice) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(
        stream_entity $stream,
        block_entity $block,
        module_entity $module,
        theme_entity $theme
    ): statement_schema {
        $builder = new author_theme();
        if ($theme->lrid) {
            $builder->with_lrid($theme->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_blocktype($block->type);
        $builder->with_moodlemoduleid($module->moodle->sectionid);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        /** @var array<array<string, mixed>> */
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        /** @var array<array<string, mixed>> */
        $blocksections = $sections[$block->moodle->sectionid]['subsections'];
        /** @var array<array<string, mixed>> */
        $modulesections = $blocksections[$module->moodle->sectionid]['subsections'];
        $section = $modulesections[$theme->moodle->sectionid];
        $sectionname = $theme->override->name;
        $sectiondesc = $theme->override->description;
        if (!$theme->override->ismanual) {
            $sectionname = $sectionname ?: $section['name'];
            $sectiondesc = $sectiondesc ?: $section['summary'];
        }
        $builder->with_themename($sectionname);
        $builder->with_themedescription($sectiondesc);
        $builder->with_themeposition($theme->moodle->position);
        $builder->with_moodlethemeid($theme->moodle->sectionid);
        $builder->with_untimoduleid($module->unti->moduleid);
        $builder->with_untithemeid($theme->unti->themeid);
        return $builder->build();
    }
}
