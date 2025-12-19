<?php
namespace local_cdo_unti2035bas\infrastructure\moodle;


class moodle_fileinfo_dto {
    /** @readonly */
    public string $filename;
    /** @readonly */
    public string $mimetype;
    /** @readonly */
    public int $filesize;
    /** @readonly */
    public string $filepath;

    public function __construct(
        string $filename,
        string $mimetype,
        int $filesize,
        string $filepath
    ) {
        $this->filename = $filename;
        $this->mimetype = $mimetype;
        $this->filesize = $filesize;
        $this->filepath = $filepath;
    }
}
