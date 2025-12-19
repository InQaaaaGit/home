<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_render_use_case {
    private stream_repository $streamrepo;
    private stream_xapi_service $streamxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        stream_xapi_service $streamxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->streamxapiservice = $streamxapiservice;
    }

    public function execute(int $streamid): string {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->streamxapiservice->execute($stream);
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
