<?php
namespace local_cdo_unti2035bas\application\module;

use DateTime;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_module;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class module_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(moodle_service $moodleservice) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(
        stream_entity $stream,
        block_entity $block,
        module_entity $module
    ): statement_schema {
        $builder = new author_module();
        if ($module->lrid) {
            $builder->with_lrid($module->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_blocktype($block->type);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        /** @var array<array<string, mixed>> */
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        /** @var array<array<string, mixed>> */
        $blocksections = $sections[$block->moodle->sectionid]['subsections'];
        $section = $blocksections[$module->moodle->sectionid];
        $sectionname = $module->override->name;
        $sectiondesc = $module->override->description;
        if (!$module->override->ismanual) {
            $sectionname = $sectionname ?: $section['name'];
            $sectiondesc = $sectiondesc ?: $section['summary'];
        }
        $builder->with_modulename($sectionname);
        $builder->with_moduledescription($sectiondesc);
        $builder->with_moduleposition($module->moodle->position);
        $builder->with_moodlemoduleid($module->moodle->sectionid);
        return $builder->build();
    }
}
