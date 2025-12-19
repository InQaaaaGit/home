<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use DateTime;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\utils;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class author_activity extends base {
    private string $activitytype;
    private string $blocktype;
    private string $activityname;
    private string $activitydescription;
    private int $moodlemoduleid;
    private int $moodlethemeid;
    private int $moodleactivityid;
    private int $untimoduleid;
    private int $untithemeid;
    private ?int $videolength = null;
    private int $academichourminutes;
    private float $lectureshours;
    private float $workshopshours;
    private float $independenthours;
    private bool $required = true;
    private bool $isvideo = false;
    private ?string $webinarlink = null;
    private ?DateTime $webinardatetime = null;
    private ?string $admittanceform = null;
    private ?bool $resultcomparability = null;

    public function with_activitytype(string $value): self {
        utils::validate_activity_type($value);
        $this->activitytype = $value;
        return $this;
    }

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
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

    public function with_required(bool $value): self {
        $this->required = $value;
        return $this;
    }

    public function with_videolength(?int $value): self {
        $this->videolength = $value;
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

    public function with_isvideo(): self {
        $this->isvideo = true;
        return $this;
    }

    public function with_iswebinar(string $link, DateTime $datetime): self {
        $this->webinarlink = $link;
        $this->webinardatetime = $datetime;
        $this->isvideo = true;
        return $this;
    }

    public function with_admittanceform(string $value): self {
        $this->admittanceform = $value;
        return $this;
    }

    public function with_resultcomparability(bool $value): self {
        $this->resultcomparability = $value;
        return $this;
    }

    private function get_xapi_type(): string {
        if ($this->activitytype == 'practice') {
            return 'https://id.2035.university/xapi/activities/practice';
        }
        return "http://activitystrea.ms/schema/1.0/{$this->activitytype}";
    }

    public function build(): statement_schema {
        $exts = [];
        if ($this->required) {
            $exts['https://id.2035.university/xapi/extension/required'] = true;
        }
        if ($this->isvideo) {
            $exts['https://id.2035.university/xapi/extension/video_type'] = 'video';
        }
        if ($this->webinarlink) {
            $exts['https://id.2035.university/xapi/extension/webinar_link'] = $this->webinarlink;
            $exts['https://id.2035.university/xapi/extension/webinar_datetime'] = $this->webinardatetime;
        }
        if (!is_null($this->resultcomparability)) {
            $exts['https://api.2035.university/comparability_of_result'] = (int)$this->resultcomparability;
        }
        $ctxexts = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
            'https://api.2035.university/module_id' => $this->untimoduleid,
            'https://api.2035.university/theme_id' => $this->untithemeid,
        ];
        if ($this->videolength) {
            $intervalstr = timedate_service::iso8061_duration($this->videolength);
            $ctxexts['https://w3id.org/xapi/video/extensions/length'] = $intervalstr;
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
        if ($this->blocktype == author_block::BLOCK_TYPE_THEORETICAL) {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/theory"
                . "/modules/{$this->moodlemoduleid}/themes/{$this->moodlethemeid}";
            $ctxexts['https://api.2035.university/block_num'] = 1;
        } else {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/practic"
                . "/modules/{$this->moodlemoduleid}/themes/{$this->moodlethemeid}";
            $ctxexts['https://api.2035.university/block_num'] = 2;
        }
        if ($this->admittanceform) {
            $ctxexts['https://id.2035.university/xapi/extension/admittance_form'] = $this->admittanceform;
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/author'),
            new object_schema(
                "{$parentobjectid}/activities/{$this->moodleactivityid}",
                'Activity',
                new object_definition_schema(
                    $this->activityname,
                    $this->activitydescription,
                    $this->get_xapi_type(),
                    $exts,
                ),
            ),
            new context_schema(
                new context_activities_schema([
                    new object_schema(
                        $parentobjectid,
                        'Activity',
                        new object_definition_schema(null, null, 'http://adlnet.gov/expapi/activities/theme', []),
                    ),
                ]),
                $ctxexts,
            ),
        );
    }
}
