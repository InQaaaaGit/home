<?php
namespace local_cdo_unti2035bas\domain;


class s3_file_vo {
    /** @readonly */
    public string $s3url;
    /** @readonly */
    public string $mimetype;
    /** @readonly */
    public int $filesize;
    /** @readonly */
    public string $sha256;
    /** @readonly */
    public ?int $timeupload;

    public function __construct(
        string $s3url,
        string $mimetype,
        int $filesize,
        string $sha256,
        ?int $timeupload
    ) {
        $this->s3url = $s3url;
        $this->mimetype = $mimetype;
        $this->filesize = $filesize;
        $this->sha256 = $sha256;
        $this->timeupload = $timeupload;
    }
}
