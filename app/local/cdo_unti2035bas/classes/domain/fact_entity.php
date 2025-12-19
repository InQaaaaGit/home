<?php
namespace local_cdo_unti2035bas\domain;


class fact_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public int $streamid;
    /** @readonly */
    public int $factdefid;
    /** @readonly */
    public int $actoruntiid;
    public int $timestamp;
    public ?int $timesent;
    public fact_result_vo $result;
    /** @var array<string, factdef_result_extension_vo> */
    public array $resultextensions;
    /** @var array<string, factdef_context_extension_vo> */
    public array $contextextensions;
    public ?int $instructoruntiid;

    /**
     * @param array<string, factdef_result_extension_vo> $resultextensions
     * @param array<string, factdef_context_extension_vo> $contextextensions
     */
    public function __construct(
        ?int $id,
        ?string $lrid,
        int $streamid,
        int $factdefid,
        int $actoruntiid,
        int $timestamp,
        fact_result_vo $result,
        array $resultextensions,
        array $contextextensions,
        ?int $instructoruntiid,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->streamid = $streamid;
        $this->factdefid = $factdefid;
        $this->actoruntiid = $actoruntiid;
        $this->timestamp = $timestamp;
        $this->timesent = $timesent;
        $this->result = $result;
        $this->resultextensions = $resultextensions;
        $this->contextextensions = $contextextensions;
        $this->instructoruntiid = $instructoruntiid;
        $this->validate();
    }

    public function validate(): void {
        foreach ($this->resultextensions as $name => $ext) {
            if ($name != $ext->name) {
                throw new \InvalidArgumentException();
            }
        }
        foreach ($this->contextextensions as $name => $ext) {
            if ($name != $ext->name) {
                throw new \InvalidArgumentException();
            }
        }
    }

    public function set_result(fact_result_vo $result): void {
        if ($this->lrid) {
            throw new \InvalidArgumentException();
        }
        $this->result = $result;
    }

    public function set_sentdata(string $lrid, int $now): void {
        $this->lrid = $lrid;
        $this->timesent = $now;
    }

    public function can_edit(): bool {
        return is_null($this->lrid);
    }

    public function can_delete(): bool {
        return is_null($this->lrid);
    }

    public function can_send(): bool {
        return is_null($this->lrid);
    }

    public function add_resultextension(
        fd_schema_vo $fdschema,
        stream_entity $stream,
        factdef_entity $factdef,
        factdef_result_extension_vo $resultextetsion,
        int $now
    ): void {
        if (!isset($fdschema->resultexts[$resultextetsion->name])) {
            throw new \InvalidArgumentException();
        }
        if (!in_array($resultextetsion->name, $stream->fdextensions)) {
            throw new \InvalidArgumentException('Not found in stream FDs');
        }
        if (in_array($resultextetsion->name, $factdef->resultextensions)) {
            throw new \InvalidArgumentException('Found in Fact Definition');
        }
        if ($resultextetsion != ($this->resultextensions[$resultextetsion->name] ?? null)) {
            $this->resultextensions[$resultextetsion->name] = $resultextetsion;
        }
    }

    public function del_resultextension(
        string $extensionname,
        int $now
    ): void {
        if (isset($this->resultextensions[$extensionname])) {
            unset($this->resultextensions[$extensionname]);
        }
    }
    public function add_contextextension(
        fd_schema_vo $fdschema,
        stream_entity $stream,
        factdef_entity $factdef,
        factdef_context_extension_vo $contextextetsion,
        int $now
    ): void {
        if (!($fdctxextschema = $fdschema->contextexts[$contextextetsion->name] ?? null)) {
            throw new \InvalidArgumentException('Not found in FD Schema');
        }
        if (!is_null($contextextetsion->value) && !$fdctxextschema->validate_value($contextextetsion->value)) {
            throw new \InvalidArgumentException('Wrong context ext value');
        }
        if (!in_array($contextextetsion->name, $stream->fdextensions)) {
            throw new \InvalidArgumentException('Not found in stream FDs');
        }
        if (in_array($contextextetsion->name, $factdef->contextextensions)) {
            throw new \InvalidArgumentException('Found in Fact Definition');
        }
        if ($contextextetsion != ($this->contextextensions[$contextextetsion->name] ?? null)) {
            $this->contextextensions[$contextextetsion->name] = $contextextetsion;
        }
    }

    public function del_contextextension(
        string $extensionname,
        int $now
    ): void {
        if (isset($this->contextextensions[$extensionname])) {
            unset($this->contextextensions[$extensionname]);
        }
    }

    public function set_instructor(?int $value): void {
        $this->instructoruntiid = $value;
    }
}
