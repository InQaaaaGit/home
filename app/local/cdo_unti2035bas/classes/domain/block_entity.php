<?php
namespace local_cdo_unti2035bas\domain;


function block_validate_type(string $type): void {
    if (!in_array($type, ['theoretical', 'practical'])) {
        throw new \InvalidArgumentException();
    }
}


class block_entity {
    /** @readonly */
    public ?int $id;
    /** @readonly */
    public int $streamid;
    /** @readonly */
    public string $type;
    public ?string $lrid;
    public int $timestamp;
    public block_moodle_vo $moodle;
    public override_vo $override;
    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;

    public function __construct(
        ?int $id,
        int $streamid,
        string $type,
        ?string $lrid,
        int $timestamp,
        block_moodle_vo $moodle,
        ?override_vo $override = null,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->streamid = $streamid;
        block_validate_type($type);
        $this->type = $type;
        $this->lrid = $lrid;
        $this->timestamp = $timestamp;
        $this->moodle = $moodle;
        $this->override = $override ?: new override_vo();
        $this->deleted = $deleted;
        $this->changed = $changed;
        if ($version < 1) {
            throw new \InvalidArgumentException();
        }
        $this->version = $version;
        $this->timesent = $timesent;
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
}
