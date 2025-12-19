<?php
namespace local_cdo_unti2035bas\domain;

function validate_academichourminutes(int $value): void {
    if ($value < 10 || $value > 100) {
        throw new \InvalidArgumentException();
    }
}

class stream_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    public int $timestamp;
    /** @readonly */
    public stream_moodle_vo $moodle;
    /** @readonly */
    public stream_unti_vo $unti;
    public override_vo $override;
    public int $academichourminutes;
    public bool $isonline;
    public string $comment;
    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;
    /** @var array<string> */
    public array $fdextensions;

    /**
     * @param array<string> $fdextensions
     */
    public function __construct(
        ?int $id,
        ?string $lrid,
        int $timestamp,
        stream_moodle_vo $moodle,
        stream_unti_vo $unti,
        int $academichourminutes,
        bool $isonline,
        string $comment,
        ?override_vo $override = null,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null,
        array $fdextensions = []
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->timestamp = $timestamp;
        $this->moodle = $moodle;
        $this->unti = $unti;
        $this->override = $override ?: new override_vo();
        validate_academichourminutes($academichourminutes);
        $this->academichourminutes = $academichourminutes;
        $this->isonline = $isonline;
        $this->comment = $comment;
        $this->deleted = $deleted;
        $this->changed = $changed;
        if ($version < 1) {
            throw new \InvalidArgumentException();
        }
        $this->version = $version;
        $this->timesent = $timesent;
        $this->fdextensions = $fdextensions;
    }

    public function set_comment(string $value): void {
        $this->comment = $value;
    }

    public function set_changed(int $now): void {
        $this->timestamp = $now;
        if (!$this->changed) {
            $this->version++;
        }
        $this->changed = true;
    }

    public function set_deleted(int $now): void {
        $this->deleted = true;
        $this->set_changed($now);
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

    public function set_academichourminutes(int $value, int $now): void {
        validate_academichourminutes($value);
        if ($this->academichourminutes != $value) {
            $this->academichourminutes = $value;
            $this->set_changed($now);
        }
    }

    public function set_isonline(bool $value, int $now): void {
        if ($this->isonline != $value) {
            $this->isonline = $value;
            $this->set_changed($now);
        }
    }

    public function add_fd_extension(string $extensionname, fd_schema_vo $fdschema): void {
        if (!isset($fdschema->resultexts[$extensionname]) and !isset($fdschema->contextexts[$extensionname])) {
            throw new \InvalidArgumentException();
        }
        if (!in_array($extensionname, $this->fdextensions)) {
            $this->fdextensions[] = $extensionname;
            sort($this->fdextensions);
        }
    }

    public function del_fd_extension(string $extensionname): void {
        $this->fdextensions = array_values(array_diff($this->fdextensions, [$extensionname]));
    }
}
