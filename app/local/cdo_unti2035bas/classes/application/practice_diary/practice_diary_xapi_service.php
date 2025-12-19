<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use DateTime;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\practice_diary_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_practice_diary;
use local_cdo_unti2035bas\infrastructure\xapi\dtos\s3_file_dto;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class practice_diary_xapi_service {
    private timedate_service $timedateservice;

    public function __construct(timedate_service $timedateservice) {
        $this->timedateservice = $timedateservice;
    }

    public function execute(
        stream_entity $stream,
        block_entity $block,
        practice_diary_entity $diary
    ): statement_schema {
        assert(!is_null($block->lrid));
        $builder = new passed_practice_diary();
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $timestamp = $this->timedateservice->now();
        $builder->with_timestamp(new DateTime("@{$timestamp}"));
        $builder->with_actorname((string)$diary->actoruntiid);
        $builder->with_practiceblocklrid($block->lrid);
        $builder->with_diaryfile(
            new s3_file_dto(
                $diary->diaryfile->s3url,
                $diary->diaryfile->mimetype,
                $diary->diaryfile->filesize,
                $diary->diaryfile->sha256,
            )
        );
        return $builder->build();
    }
}
