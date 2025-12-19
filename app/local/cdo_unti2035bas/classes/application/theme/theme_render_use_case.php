<?php
namespace local_cdo_unti2035bas\application\theme;

use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class theme_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private theme_xapi_service $themexapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        theme_xapi_service $themexapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->themexapiservice = $themexapiservice;
    }

    public function execute(int $themeid): string {
        $theme = $this->themerepo->read($themeid);
        if (!$theme) {
            throw new \InvalidArgumentException();
        }
        $module = $this->modulerepo->read($theme->moduleid);
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
        $statement = $this->themexapiservice->execute($stream, $block, $module, $theme);
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
