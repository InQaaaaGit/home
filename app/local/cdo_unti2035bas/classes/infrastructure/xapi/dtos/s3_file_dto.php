<?php

namespace local_cdo_unti2035bas\infrastructure\xapi\dtos;


class s3_file_dto {
    /** @readonly */
    public string $s3url;
    /** @readonly */
    public string $mimetype;
    /** @readonly */
    public int $filesize;
    /** @readonly */
    public string $sha256;

    public function __construct(
        string $s3url,
        string $mimetype,
        int $filesize,
        string $sha256
    ) {
        $this->s3url = $s3url;
        $this->mimetype = $mimetype;
        $this->filesize = $filesize;
        $this->sha256 = $sha256;
    }
}
