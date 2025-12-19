<?php
namespace local_cdo_unti2035bas\domain;


class activity_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public int $themeid;
    public string $type;
    public activity_moodle_vo $moodle;
    public activity_config_vo $config;
    public override_vo $override;
    public int $timestamp;

    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;

    public function __construct(
        ?int $id,
        ?string $lrid,
        int $themeid,
        string $type,
        activity_moodle_vo $moodle,
        activity_config_vo $config,
        int $timestamp,
        ?override_vo $override = null,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->themeid = $themeid;
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->moodle = $moodle;
        $this->override = $override ?: new override_vo();
        $this->config = $config;
        $this->deleted = $deleted;
        $this->changed = $changed;
        $this->version = $version;
        $this->timesent = $timesent;
        $this->validate();
    }

    private function validate(): void {
        if (!validator::validate_activity_type($this->type)) {
            throw new \InvalidArgumentException();
        }
        if ($this->type == 'practice' && !$this->config->admittanceform) {
            throw new \InvalidArgumentException();
        }
        if ($this->type != 'practice' && $this->config->admittanceform) {
            throw new \InvalidArgumentException();
        }
        if ($this->version < 1) {
            throw new \InvalidArgumentException();
        }
    }

    public function set_changed(int $now): void {
        $this->timestamp = $now;
        if (!$this->changed) {
            $this->version++;
        }
        $this->changed = true;
    }

    public function set_moodledata(activity_moodle_vo $value, int $now): bool {
        if ($this->moodle == $value) {
            return false;
        }
        $this->moodle = $value;
        $this->set_changed($now);
        return true;
    }

    public function set_configdata(activity_config_vo $value, int $now): bool {
        if ($this->config == $value) {
            return false;
        }
        $this->config = $value;
        $this->set_changed($now);
        $this->validate();
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
