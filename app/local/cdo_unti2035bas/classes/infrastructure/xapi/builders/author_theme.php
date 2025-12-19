<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class author_theme extends base {
    private string $blocktype;
    private int $moodlemoduleid;
    private string $themename;
    private string $themedescription;
    private int $themeposition;
    private int $moodlethemeid;
    private int $untimoduleid;
    private int $untithemeid;

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
        return $this;
    }

    public function with_moodlemoduleid(int $value): self {
        $this->moodlemoduleid = $value;
        return $this;
    }

    public function with_themename(string $value): self {
        $this->themename = $value;
        return $this;
    }

    public function with_themedescription(string $value): self {
        $this->themedescription = $value;
        return $this;
    }

    public function with_themeposition(int $value): self {
        $this->themeposition = $value;
        return $this;
    }

    public function with_moodlethemeid(int $value): self {
        $this->moodlethemeid = $value;
        return $this;
    }

    public function with_untimoduleid(int $value): self {
        $this->untimoduleid = $value;
        return $this;
    }

    public function with_untithemeid(int $value): self {
        $this->untithemeid = $value;
        return $this;
    }

    public function build(): statement_schema {
        $contextextensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
            'https://api.2035.university/module_id' => $this->untimoduleid,
            'https://api.2035.university/theme_id' => $this->untithemeid,
        ];
        if ($this->blocktype == author_block::BLOCK_TYPE_THEORETICAL) {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/theory"
                . "/modules/{$this->moodlemoduleid}";
            $contextextensions['https://api.2035.university/block_num'] = 1;
        } else {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/practic"
                . "/modules/{$this->moodlemoduleid}";
            $contextextensions['https://api.2035.university/block_num'] = 2;
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/author'),
            new object_schema(
                "{$parentobjectid}/themes/{$this->moodlethemeid}",
                'Activity',
                new object_definition_schema(
                    $this->themename,
                    $this->themedescription,
                    'http://adlnet.gov/expapi/activities/theme',
                    ['http://id.tincanapi.com/extension/position' => $this->themeposition],
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
                            'https://id.2035.university/xapi/activities/module',
                            [],
                        ),
                    ),
                ]),
                $contextextensions,
            ),
        );
    }
}
