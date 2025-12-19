<?php
namespace local_cdo_unti2035bas\application\moodle;

use local_cdo_unti2035bas\application\stream\stream_fd_sync_service;
use local_cdo_unti2035bas\application\stream\stream_sync_service;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class course_update_use_case {
    private stream_repository $streamrepo;
    private stream_sync_service $streamsyncservice;
    private stream_fd_sync_service $streamfdsyncservice;

    public function __construct(
        stream_repository $streamrepo,
        stream_sync_service $streamsyncservice,
        stream_fd_sync_service $streamfdsyncservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->streamsyncservice = $streamsyncservice;
        $this->streamfdsyncservice = $streamfdsyncservice;
    }

    public function execute(int $courseid): void {
        if (!$this->streamrepo->exists_by_courseid($courseid)) {
            return;
        }
        $streams = $this->streamrepo->read_by_courseid($courseid);
        foreach ($streams as $stream) {
            $this->streamsyncservice->execute($stream);
            /** @var int $streamid */
            $streamid = $stream->id;
            $this->streamfdsyncservice->execute($streamid);
        }
    }
}
