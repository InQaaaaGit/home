<?php
namespace local_cdo_unti2035bas\domain;


class factdef_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public int $streamid;
    /** @readonly */
    public string $baseobject;
    /** @readonly */
    public int $baseobjectid;
    public int $timestamp;
    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;
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
        string $baseobject,
        int $baseobjectid,
        int $timestamp,
        array $resultextensions,
        array $contextextensions,
        ?int $instructoruntiid,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->streamid = $streamid;
        $this->baseobject = $baseobject;
        $this->baseobjectid = $baseobjectid;
        $this->timestamp = $timestamp;
        $this->deleted = $deleted;
        $this->changed = $changed;
        $this->version = $version;
        $this->timesent = $timesent;
        $this->resultextensions = $resultextensions;
        $this->contextextensions = $contextextensions;
        $this->instructoruntiid = $instructoruntiid;
        $this->validate();
    }

    private function validate(): void {
        if ($this->version < 1) {
            throw new \InvalidArgumentException();
        }
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

    public function set_changed(int $now): void {
        $this->timestamp = $now;
        if (!$this->changed) {
            $this->version++;
        }
        $this->changed = true;
    }

    public function set_deleted(int $now): void {
        if (!$this->deleted) {
            $this->deleted = true;
            $this->set_changed($now);
        }
    }

    public function set_sentdata(string $lrid, int $now): void {
        $this->lrid = $lrid;
        $this->timesent = $now;
        $this->changed = false;
    }

    public function set_canceled(): void {
        $this->lrid = null;
        $this->timesent = null;
        $this->changed = true;
    }

    public function add_resultextension(
        fd_schema_vo $fdschema,
        stream_entity $stream,
        factdef_result_extension_vo $resultextetsion,
        int $now
    ): void {
        if (!isset($fdschema->resultexts[$resultextetsion->name])) {
            throw new \InvalidArgumentException();
        }
        if (!in_array($resultextetsion->name, $stream->fdextensions)) {
            throw new \InvalidArgumentException('Not found in stream FDs');
        }
        if ($resultextetsion != ($this->resultextensions[$resultextetsion->name] ?? null)) {
            $this->resultextensions[$resultextetsion->name] = $resultextetsion;
            $this->set_changed($now);
        }
    }

    public function del_resultextension(
        string $extensionname,
        int $now
    ): void {
        if (isset($this->resultextensions[$extensionname])) {
            unset($this->resultextensions[$extensionname]);
            $this->set_changed($now);
        }
    }
    public function add_contextextension(
        fd_schema_vo $fdschema,
        stream_entity $stream,
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
        if ($contextextetsion != ($this->contextextensions[$contextextetsion->name] ?? null)) {
            $this->contextextensions[$contextextetsion->name] = $contextextetsion;
            $this->set_changed($now);
        }
    }

    public function del_contextextension(
        string $extensionname,
        int $now
    ): void {
        if (isset($this->contextextensions[$extensionname])) {
            unset($this->contextextensions[$extensionname]);
            $this->set_changed($now);
        }
    }

    public function set_instructor(?int $value): void {
        $this->instructoruntiid = $value;
    }
}
