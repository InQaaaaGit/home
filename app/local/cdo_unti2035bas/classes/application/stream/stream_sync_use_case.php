<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_sync_use_case {
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

    public function execute(int $streamid): void {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $this->streamsyncservice->execute($stream);
        $this->streamfdsyncservice->execute($streamid);
    }
}
