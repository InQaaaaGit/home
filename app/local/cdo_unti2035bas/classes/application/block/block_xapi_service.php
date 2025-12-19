<?php
namespace local_cdo_unti2035bas\application\block;

use DateTime;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_block;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class block_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(moodle_service $moodleservice) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(stream_entity $stream, block_entity $block): statement_schema {
        $builder = new author_block();
        if ($block->lrid) {
            $builder->with_lrid($block->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_blocktype($block->type);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        /** @var array<array<string, mixed>> $sections */
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        $section = $sections[$block->moodle->sectionid];
        $sectionname = $block->override->name;
        $sectiondesc = $block->override->description;
        if (!$block->override->ismanual) {
            $sectionname = $sectionname ?: $section['name'];
            $sectiondesc = $sectiondesc ?: $section['summary'];
        }
        $builder->with_blockname($sectionname);
        $builder->with_blockdescription($sectiondesc);
        return $builder->build();
    }
}
