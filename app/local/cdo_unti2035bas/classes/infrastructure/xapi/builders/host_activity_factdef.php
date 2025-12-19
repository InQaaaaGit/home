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
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class host_activity_factdef extends base
{
    private string $blocktype;
    private int $moodlemoduleid;
    private int $moodlethemeid;
    private int $moodleactivityid;
    private bool $required = true;
    private ?bool $resultcomparability = null;
    private ?string $instructorname = null;
    private string $admittanceform;
    /** @var array<factdef_context_extension_vo> */
    private array $fdcontextextensions;
    /** @var array<factdef_result_extension_vo> */
    private array $fdresultextensions;

    public const ADMITTANCE_FORM_ONLINE = 'online';
    public const ADMITTANCE_FORM_OFFLINE = 'offline';
    public const ADMITTANCE_FORM_HYBRID = 'hybrid';

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

    public function with_required(bool $value): self {
        $this->required = $value;
        return $this;
    }

    public function with_resultcomparability(bool $value): self {
        $this->resultcomparability = $value;
        return $this;
    }

    public function with_instructorname(?string $value): self {
        $this->instructorname = $value;
        return $this;
    }

    public function with_admittanceform(string $value): self {
        if (!in_array($value, [$this::ADMITTANCE_FORM_ONLINE, $this::ADMITTANCE_FORM_OFFLINE, $this::ADMITTANCE_FORM_HYBRID])) {
            throw new \InvalidArgumentException();
        }
        $this->admittanceform = $value;
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
        $exts = ['https://id.2035.university/xapi/extension/collaborative' => false];
        $ctxexts = [
            'https://api.2035.university/FD' => true,
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
            'https://id.2035.university/xapi/extension/admittance_form' => $this->admittanceform,
        ];
        if ($this->required) {
            $exts['https://id.2035.university/xapi/extension/required'] = true;
        }
        if (!is_null($this->resultcomparability)) {
            $exts['https://api.2035.university/comparability_of_result'] = (int)$this->resultcomparability;
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
                "{$parentobjectid}/activities/{$this->moodleactivityid}",
                "Activity",
                new object_definition_schema(null, null, null, $exts),
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
                null,
                $this->instructorname ? agent_schema::from_agentname($this->instructorname) : null,
            ),
            null,
            $result,
        );
    }
}
