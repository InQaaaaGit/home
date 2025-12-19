<?php
namespace local_cdo_unti2035bas\application\theme;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class theme_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private theme_xapi_service $themexapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        theme_xapi_service $themexapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->themexapiservice = $themexapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $themeid): void {
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
        [$lrid] = $this->xapiclient->send([$statement]);
        $theme->set_sentdata($lrid, $this->timedateservice->now());
        $this->themerepo->save($theme);
        $this->logger->info(
            "Theme sent, lrid: {$lrid}",
            'theme_entity',
            $theme->id,
            $theme->version,
            (string)json_encode($statement->dump()),
        );
    }
}
