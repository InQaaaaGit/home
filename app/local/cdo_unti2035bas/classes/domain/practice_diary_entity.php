<?php
namespace local_cdo_unti2035bas\domain;


class practice_diary_entity {
    /** @readonly */
    public ?int $id;
    public ?string $lrid;
    /** @readonly */
    public int $streamid;
    /** @readonly */
    public int $actoruntiid;
    public int $timestamp;
    public ?int $timesent;
    public s3_file_vo $diaryfile;

    public function __construct(
        ?int $id,
        ?string $lrid,
        int $streamid,
        int $actoruntiid,
        int $timestamp,
        s3_file_vo $diaryfile,
        ?int $timesent = null
    ) {
        $this->id = $id;
        $this->lrid = $lrid;
        $this->streamid = $streamid;
        $this->actoruntiid = $actoruntiid;
        $this->timestamp = $timestamp;
        $this->diaryfile = $diaryfile;
        $this->timesent = $timesent;
    }

    public function set_s3timeupload(int $value): void {
        if ($this->timesent && $value != $this->diaryfile->timeupload) {
            throw new \InvalidArgumentException();
        }
        $this->diaryfile = new s3_file_vo(
            $this->diaryfile->s3url,
            $this->diaryfile->mimetype,
            $this->diaryfile->filesize,
            $this->diaryfile->sha256,
            $value,
        );
    }

    public function set_sentdata(string $lrid, int $now): void {
        $this->lrid = $lrid;
        $this->timesent = $now;
    }

    public function can_delete(): bool {
        return is_null($this->timesent);
    }
}
