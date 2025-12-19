<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_fd_delete_use_case {
    private stream_repository $streamrepo;

    public function __construct(
        stream_repository $streamrepo
    ) {
        $this->streamrepo = $streamrepo;
    }

    public function execute(int $streamid, string $extensionname): void {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $stream->del_fd_extension($extensionname);
        $this->streamrepo->save($stream);
    }
}
