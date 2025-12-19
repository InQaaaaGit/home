<?php
namespace local_cdo_unti2035bas\domain;


class module_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public int $blockid;
    public module_moodle_vo $moodle;
    public module_unti_vo $unti;
    public int $timestamp;
    public override_vo $override;
    public bool $deleted;
    public bool $changed;
    public int $version;
    public ?int $timesent;

    public function __construct(
        ?int $id,
        ?string $lrid,
        int $blockid,
        module_moodle_vo $moodle,
        module_unti_vo $unti,
        int $timestamp,
        ?override_vo $override = null,
        bool $deleted = false,
        bool $changed = true,
        int $version = 1,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->blockid = $blockid;
        $this->timestamp = $timestamp;
        $this->moodle = $moodle;
        $this->unti = $unti;
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

    public function set_moodledata(module_moodle_vo $value, int $now): bool {
        if ($this->moodle == $value) {
            return false;
        }
        $this->moodle = $value;
        $this->set_changed($now);
        return true;
    }

    public function set_untidata(module_unti_vo $value, int $now): bool {
        if ($this->unti == $value) {
            return false;
        }
        $this->unti = $value;
        $this->set_changed($now);
        return true;
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
