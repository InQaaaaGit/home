<?php
namespace local_cdo_unti2035bas\application\module;

use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class module_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private module_xapi_service $modulexapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        module_xapi_service $modulexapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->modulexapiservice = $modulexapiservice;
    }

    public function execute(int $moduleid): string {
        $module = $this->modulerepo->read($moduleid);
        if (!$module) {
            throw new \InvalidArgumentException();
        }
        $block = $this->blockrepo->read($module->blockid);
        if (!$block) {
            throw new \InvalidArgumentException();
        }
        $stream = $this->streamrepo->read($block->streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->modulexapiservice->execute($stream, $block, $module);
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
