<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\dtos\s3_file_dto;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\attachment_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class passed_practice_diary extends base {
    private string $practiceblocklrid;
    private s3_file_dto $diaryfile;

    public function with_practiceblocklrid(string $value): self {
        $this->practiceblocklrid = $value;
        return $this;
    }

    public function with_diaryfile(s3_file_dto $diaryfile): self {
        $this->diaryfile = $diaryfile;
        return $this;
    }

    public function build(): statement_schema {
        $ctxexts = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
        ];
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://adlnet.gov/expapi/verbs/passed'),
            new object_schema(
                $this->practiceblocklrid,
                'StatementRef',
                null,
            ),
            new context_schema(
                null,
                $ctxexts,
            ),
            [
                new attachment_schema(
                    'https://id.2035.university/xapi/attachments/practice-diary',
                    ['ru-RU' => 'Дневник'],
                    ['ru-RU' => 'Скан дневника прохождения блока практической подготовки'],
                    $this->diaryfile->mimetype,
                    $this->diaryfile->filesize,
                    $this->diaryfile->s3url,
                    $this->diaryfile->sha256,
                ),
            ],
        );
    }
}
