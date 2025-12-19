<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class author_block extends base {
    private string $blockname;
    private string $blockdescription;
    private string $blocktype;

    const BLOCK_TYPE_THEORETICAL = 'theoretical';
    const BLOCK_TYPE_PRACTICAL = 'practical';

    public function with_blockname(string $value): self {
        $this->blockname = $value;
        return $this;
    }

    public function with_blockdescription(string $value): self {
        $this->blockdescription = $value;
        return $this;
    }

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
        return $this;
    }

    public function build(): statement_schema {
        $extensions = [
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/flow_id' => $this->untiflowid,
        ];
        if ($this->blocktype == $this::BLOCK_TYPE_THEORETICAL) {
            $objectid = "{$this->prefix}/courses/{$this->uniqid}/theory";
            $extensions['https://api.2035.university/block_num'] = 1;
            $objecttype = 'https://id.2035.university/xapi/activities/theoretical-block';
        } else {
            $objectid = "{$this->prefix}/courses/{$this->uniqid}/practic";
            $extensions['https://api.2035.university/block_num'] = 2;
            $objecttype = 'https://id.2035.university/xapi/activities/practical-block';
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/author'),
            new object_schema(
                $objectid,
                'Activity',
                new object_definition_schema(
                    $this->blockname,
                    $this->blockdescription,
                    $objecttype,
                    [],
                ),
            ),
            new context_schema(
                new context_activities_schema([
                    new object_schema(
                        "{$this->prefix}/courses/{$this->uniqid}",
                        'Activity',
                        new object_definition_schema(
                            null,
                            null,
                            'http://adlnet.gov/expapi/activities/course',
                            [],
                        ),
                    ),
                ]),
                $extensions
            ),
        );
    }
}
