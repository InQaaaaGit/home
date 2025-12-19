<?php
namespace local_cdo_unti2035bas\domain;


class assessment_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public string $parentobject;
    /** @readonly */
    public int $parentobjectid;
    /** @readonly */
    public assessment_moodle_vo $moodle;
    public assessment_config_vo $config;
    public override_vo $override;
    public int $timestamp;
    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;

    public function __construct(
        ?int $id,
        ?string $lrid,
        string $parentobject,
        int $parentobjectid,
        assessment_moodle_vo $moodle,
        assessment_config_vo $config,
        int $timestamp,
        ?override_vo $override = null,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->parentobjectid = $parentobjectid;
        $this->parentobject = $parentobject;
        $this->moodle = $moodle;
        $this->config = $config;
        $this->override = $override ?: new override_vo();
        $this->timestamp = $timestamp;
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

    public function set_configdata(assessment_config_vo $value, int $now): bool {
        if ($this->config == $value) {
            return false;
        }
        $this->config = $value;
        $this->set_changed($now);
        return true;
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
}
