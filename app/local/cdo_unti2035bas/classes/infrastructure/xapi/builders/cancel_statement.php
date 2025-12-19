<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\attachment_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;

class cancel_statement extends base {
    private string $statementlrid;
    private string $objectid;

    public function with_statementlrid(string $value): self {
        $this->statementlrid = $value;
        return $this;
    }

    public function with_objectid(string $value): self {
        $this->objectid = $value;
        return $this;
    }


    public function build(): statement_schema {
        $ctxexts = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
            'https://api.2035.university/block_num' => 0,
            'https://api.2035.university/module_num' => 1,
            'https://api.2035.university/el_id' => $this->objectid,
        ];
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/cancel'),
            new object_schema($this->statementlrid, 'StatementRef', null),
            new context_schema(null, $ctxexts),
            [
                new attachment_schema(
                    'https://id.2035.university/xapi/attachments/official-letter',
                    ['ru-RU' => 'Письмо Провайдера'],
                    ['ru-RU' => 'Скан официального письма с описанием причин отмены'],
                    'image/jpeg',
                    76471,
                    'https://s3.objstor.cloud4u.com/kadastr/photo_2025-06-18_01-28-07.jpg',
                    '0809892759e88ef618f032c760fa0bd600f13562b76e6f0e8befa12c85546986',
                ),
            ],
        );
    }
}
