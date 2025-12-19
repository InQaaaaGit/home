<?php
namespace local_cdo_unti2035bas\application\block;

use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class block_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private block_xapi_service $blockxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        block_xapi_service $blockxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->blockxapiservice = $blockxapiservice;
    }

    public function execute(int $blockid): string {
        $block = $this->blockrepo->read($blockid);
        if (!$block) {
            throw new \InvalidArgumentException();
        }
        $stream = $this->streamrepo->read($block->streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->blockxapiservice->execute($stream, $block);
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
