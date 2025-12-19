<?php

namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\result_schema2;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\utils;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;

class host_assessment_factdef extends base
{
    private string $blocktype;
    private string $assessmentlevel;
    private int $moodlemoduleid;
    private int $moodlethemeid;
    private int $moodleactivityid;
    private int $untimoduleid;
    private int $untithemeid;
    private bool $resultcomparability;
    /** @var array<factdef_context_extension_vo> */
    private array $fdcontextextensions;
    /** @var array<factdef_result_extension_vo> */
    private array $fdresultextensions;

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

    public function with_untimoduleid(int $value): self {
        $this->untimoduleid = $value;
        return $this;
    }

    public function with_untithemeid(int $value): self {
        $this->untithemeid = $value;
        return $this;
    }

    public function with_resultcomparability(bool $value): self {
        $this->resultcomparability = $value;
        return $this;
    }

    /**
     * @param array<factdef_context_extension_vo> $value
     */
    public function with_fdcontextextensions(array $value): self {
        $this->fdcontextextensions = $value;
        return $this;
    }

    /**
     * @param array<factdef_result_extension_vo> $value
     */
    public function with_fdresultextensions(array $value): self {
        $this->fdresultextensions = $value;
        return $this;
    }

    public function build(): statement_schema {
        $exts = [];
        $ctxexts = [
            'https://api.2035.university/FD' => true,
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
        ];
        $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}";
        if ($this->assessmentlevel == 'final') {
            $exts['https://id.2035.university/xapi/extension/assessment_type'] = 'final';
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
        $exts['https://api.2035.university/comparability_of_result'] = (int)$this->resultcomparability;
        foreach ($this->fdcontextextensions as $ext) {
            $ctxexts[$ext->name] = !is_null($ext->value) ? $ext->value : 'Нет данных';
        }
        $result = null;
        $resultexts = [];
        foreach ($this->fdresultextensions as $ext) {
            if (!is_null($ext->value)) {
                $resultexts[$ext->name] = array_filter((array)$ext->value, fn($v) => !is_null($v));
            } else {
                $resultexts[$ext->name] = 'Нет данных';
            }
        }
        if ($resultexts) {
            $result = new result_schema2(
                null,
                null,
                null,
                $resultexts,
            );
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://activitystrea.ms/schema/1.0/host'),
            new object_schema(
                "{$parentobjectid}/assessments/{$this->moodleactivityid}",
                "Activity",
                new object_definition_schema(null, null, null, $exts),
            ),
            new context_schema(
                new context_activities_schema([
                    new object_schema(
                        $parentobjectid,
                        'Activity',
                        new object_definition_schema(null, null, $parenttype, []),
                    ),
                ]),
                $ctxexts,
                null,
            ),
            null,
            $result,
        );
    }
}
