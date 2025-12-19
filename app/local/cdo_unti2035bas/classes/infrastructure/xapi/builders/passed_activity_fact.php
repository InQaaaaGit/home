<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\domain\fact_result_vo;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_activities_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\result_schema2;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\result_score_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class passed_activity_fact extends base {
    private string $factdeflrid;
    private string $blocktype;
    private int $moodlethemeid;
    private int $moodlemoduleid;
    private int $moodlemodulepos;
    private int $moodleactivityid;
    private ?string $instructorname = null;
    private ?fact_result_vo $result;
    /** @var array<factdef_context_extension_vo> */
    private array $fdcontextextensions;
    /** @var array<factdef_result_extension_vo> */
    private array $fdresultextensions;

    public function with_factdeflrid(string $value): self {
        $this->factdeflrid = $value;
        return $this;
    }

    public function with_blocktype(string $value): self {
        $this->blocktype = $value;
        return $this;
    }

    public function with_moodlemodulepos(int $value): self {
        $this->moodlemodulepos = $value;
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

    public function with_instructorname(?string $value): self {
        $this->instructorname = $value;
        return $this;
    }

    public function with_result(?fact_result_vo $result): self {
        $this->result = $result;
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
        if ($this->blocktype == author_block::BLOCK_TYPE_THEORETICAL) {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/theory"
                . "/modules/{$this->moodlemoduleid}/themes/{$this->moodlethemeid}";
        } else {
            $parentobjectid = "{$this->prefix}/courses/{$this->uniqid}/practic"
                . "/modules/{$this->moodlemoduleid}/themes/{$this->moodlethemeid}";
        }
        $parentobjectid .= "/activities/{$this->moodleactivityid}";
        $ctxexts = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $this->unticourseid,
            'https://api.2035.university/flow_id' => $this->untiflowid,
            'https://api.2035.university/FD' => true,
            'https://api.2035.university/module_num' => $this->moodlemodulepos,
        ];
        $score = null;
        $resultexts = [];
        if ($this->result) {
            $score = new result_score_schema(
                $this->result->scoreraw,
                $this->result->scoremin,
                $this->result->scoremax,
                $this->result->scorescaled,
            );
            $resultexts += [
                'https://api.2035.university/target' => $this->result->scoretarget,
                'https://api.2035.university/attempts_max' => $this->result->attemptsmax,
                'https://api.2035.university/attempts_index' => $this->result->attemptnum,
            ];
        }
        foreach ($this->fdcontextextensions as $ext) {
            $ctxexts[$ext->name] = !is_null($ext->value) ? $ext->value : 'Нет данных';
        }
        foreach ($this->fdresultextensions as $ext) {
            if (!is_null($ext->value)) {
                $resultexts[$ext->name] = array_filter((array)$ext->value, fn($v) => !is_null($v));
            } else {
                $resultexts[$ext->name] = 'Нет данных';
            }
        }
        $result = null;
        if ($resultexts || $score) {
            $result = new result_schema2(
                $score,
                $this->result->success ?? null,
                $this->result->duration ?? null,
                $resultexts,
            );
        }
        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            verb_schema::from_verbid('http://adlnet.gov/expapi/verbs/passed'),
            new object_schema(
                $this->factdeflrid,
                'StatementRef',
                null,
            ),
            new context_schema(
                new context_activities_schema([new object_schema($parentobjectid, '', null)]),
                $ctxexts,
                null,
                $this->instructorname ? agent_schema::from_agentname($this->instructorname) : null,
            ),
            [],
            $result,
        );
    }
}
