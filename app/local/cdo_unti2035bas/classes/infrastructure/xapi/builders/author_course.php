<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class author_course extends base {
    private string $coursename;
    private string $coursedescription;
    private bool $courseonline;

    public function with_coursename(string $value): self {
        $this->coursename = $value;
        return $this;
    }

    public function with_coursedescription(string $value): self {
        $this->coursedescription = $value;
        return $this;
    }

    public function with_courseonline(bool $value): self {
        $this->courseonline = $value;
        return $this;
    }

    public function build(): statement_schema {
        $extensions = [
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/project' => 'БАС',
        ];
        if ($this->courseonline) {
            $extensions['https://id.2035.university/xapi/extension/is_online'] = true;
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/author'),
            new object_schema(
                "{$this->prefix}/courses/{$this->uniqid}",
                'Activity',
                new object_definition_schema(
                    $this->coursename,
                    $this->coursedescription,
                    'http://adlnet.gov/expapi/activities/course',
                    [],
                ),
            ),
            new context_schema(null, $extensions),
        );
    }
}
