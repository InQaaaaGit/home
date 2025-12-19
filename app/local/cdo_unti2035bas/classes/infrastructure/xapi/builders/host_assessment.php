<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\utils;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class host_assessment extends base {
    private string $blocktype;
    private string $assessmentlevel;
    private string $activityname;
    private string $activitydescription;
    private int $moodlemoduleid;
    private int $moodlethemeid;
    private int $moodleactivityid;
    private int $untimoduleid;
    private int $untithemeid;
    private int $academichourminutes;
    private float $lectureshours;
    private float $workshopshours;
    private float $independenthours;
    private bool $haspractice;
    private bool $resultcomparability;
    private string $documenttype;

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
        return $this;
    }

    public function with_assessmentlevel(string $value): self {
        utils::validate_assessment_level($value);
        $this->assessmentlevel = $value;
        return $this;
    }

    public function with_moodlemoduleid(int $value): self {
        $this->moodlemoduleid = $value;
        return $this;
    }

    public function with_moodlethemeid(int $value): self {
        $this->moodlethemeid = $value;
        return $this;
    }

    public function with_moodleactivityid(int $value): self {
        $this->moodleactivityid = $value;
        return $this;
    }

    public function with_activityname(string $value): self {
        $this->activityname = $value;
        return $this;
    }

    public function with_activitydescription(string $value): self {
        $this->activitydescription = $value;
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

    public function with_academichourminutes(int $value): self {
        $this->academichourminutes = $value;
        return $this;
    }

    public function with_lectureshours(float $value): self {
        $this->lectureshours = $value;
        return $this;
    }

    public function with_workshopshours(float $value): self {
        $this->workshopshours = $value;
        return $this;
    }

    public function with_independenthours(float $value): self {
        $this->independenthours = $value;
        return $this;
    }

    public function with_practice(bool $value): self {
        $this->haspractice = $value;
        return $this;
    }

    public function with_resultcomparability(bool $value): self {
        $this->resultcomparability = $value;
        return $this;
    }

    public function with_documenttype(string $value): self {
        $this->documenttype = $value;
        return $this;
    }


    public function build(): statement_schema {
        $exts = [
            'https://id.2035.university/xapi/extension/indication_of_practice' => $this->haspractice,
            'https://api.2035.university/comparability_of_result' => (int)$this->resultcomparability,
        ];
        $ctxexts = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
        ];
        $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}";
        if ($this->assessmentlevel == 'final') {
            $exts['https://id.2035.university/xapi/extension/assessment_type'] = 'final';
            $ctxexts['https://id.2035.university/xapi/extension/final-assessment-doc-type'] = $this->documenttype;
            $parenttype = 'http://adlnet.gov/expapi/activities/course';
        } else {
            $exts['https://id.2035.university/xapi/extension/assessment_type'] = 'intermediate';
            if ($this->blocktype == author_block::BLOCK_TYPE_THEORETICAL) {
                $parentobjectid .= "/theory";
                $ctxexts['https://api.2035.university/block_num'] = 1;
                $parenttype = 'https://id.2035.university/xapi/activities/theoretical-block';
            } else {
                $parentobjectid .= "/practic";
                $ctxexts['https://api.2035.university/block_num'] = 2;
                $parenttype = 'https://id.2035.university/xapi/activities/practical-block';
            }
        }
        if ($this->assessmentlevel == 'module') {
            $parentobjectid .= "/modules/{$this->moodlemoduleid}";
            $parenttype = 'http://adlnet.gov/expapi/activities/module';
            $ctxexts['https://api.2035.university/module_id'] = $this->untimoduleid;
        }
        if ($this->assessmentlevel == 'theme') {
            $parentobjectid .= "/modules/{$this->moodlemoduleid}/themes/{$this->moodlethemeid}";
            $parenttype = 'http://adlnet.gov/expapi/activities/theme';
            $ctxexts['https://api.2035.university/module_id'] = $this->untimoduleid;
            $ctxexts['https://api.2035.university/theme_id'] = $this->untithemeid;
        }
        if ($this->lectureshours) {
            $ach = utils::format_duration($this->lectureshours);
            $asm = utils::format_duration($this->lectureshours * $this->academichourminutes);
            $exts['https://id.2035.university/xapi/extension/lectures_hours_academic'] = $ach;
            $exts['https://id.2035.university/xapi/extension/lectures_minutes_astronomy'] = $asm;
        }
        if ($this->workshopshours) {
            $ach = utils::format_duration($this->workshopshours);
            $asm = utils::format_duration($this->workshopshours * $this->academichourminutes);
            $exts['https://id.2035.university/xapi/extension/workshops_hours_academic'] = $ach;
            $exts['https://id.2035.university/xapi/extension/workshops_minutes_astronomy'] = $asm;
        }
        if ($this->independenthours) {
            $ach = utils::format_duration($this->independenthours);
            $asm = utils::format_duration($this->independenthours * $this->academichourminutes);
            $exts['https://id.2035.university/xapi/extension/independent_work_hours_academic'] = $ach;
            $exts['https://id.2035.university/xapi/extension/independent_work_minutes_astronomy'] = $asm;
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/host'),
            new object_schema(
                "{$parentobjectid}/assessments/{$this->moodleactivityid}",
                'Activity',
                new object_definition_schema(
                    $this->activityname,
                    $this->activitydescription,
                    'http://adlnet.gov/expapi/activities/assessment',
                    $exts,
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
                            $parenttype,
                            [],
                        ),
                    ),
                ]),
                $ctxexts,
            ),
        );
    }
}
