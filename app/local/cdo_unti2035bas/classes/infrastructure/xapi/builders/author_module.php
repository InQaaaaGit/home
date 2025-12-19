<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class author_module extends base {
    private string $blocktype;
    private string $modulename;
    private string $moduledescription;
    private int $moduleposition;
    private int $moodlemoduleid;

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
        return $this;
    }

    public function with_modulename(string $value): self {
        $this->modulename = $value;
        return $this;
    }

    public function with_moduledescription(string $value): self {
        $this->moduledescription = $value;
        return $this;
    }

    public function with_moduleposition(int $value): self {
        $this->moduleposition = $value;
        return $this;
    }

    public function with_moodlemoduleid(int $value): self {
        $this->moodlemoduleid = $value;
        return $this;
    }

    public function build(): statement_schema {
        $contextextensions = [
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/flow_id' => $this->untiflowid,
        ];
        if ($this->blocktype == author_block::BLOCK_TYPE_THEORETICAL) {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/theory";
            $contextextensions['https://api.2035.university/block_num'] = 1;
        } else {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/practic";
            $contextextensions['https://api.2035.university/block_num'] = 2;
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/author'),
            new object_schema(
                "{$parentobjectid}/modules/{$this->moodlemoduleid}",
                'Activity',
                new object_definition_schema(
                    $this->modulename,
                    $this->moduledescription,
                    'http://adlnet.gov/expapi/activities/module',
                    ['http://id.tincanapi.com/extension/position' => $this->moduleposition],
                ),
            ),
            new context_schema(
                new context_activities_schema([
                    new object_schema(
                        $parentobjectid,
                        'Activity',
                        new object_definition_schema(
                            null,
                            null,
                            'https://id.2035.university/xapi/activities/block',
                            [],
                        ),
                    ),
                ]),
                $contextextensions,
            ),
        );
    }
}
