<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_fd_add_use_case {
    private stream_repository $streamrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        stream_repository $streamrepo,
        fd_schema_vo $fdschema
    ) {
        $this->streamrepo = $streamrepo;
        $this->fdschema = $fdschema;
    }

    public function execute(int $streamid, string $extensionname): void {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $stream->add_fd_extension($extensionname, $this->fdschema);
        $this->streamrepo->save($stream);
    }
}
