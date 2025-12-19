<?php
namespace local_cdo_unti2035bas\application\module;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class module_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private module_xapi_service $modulexapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        module_xapi_service $modulexapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->modulexapiservice = $modulexapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $moduleid): void {
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
        [$lrid] = $this->xapiclient->send([$statement]);
        $module->set_sentdata($lrid, $this->timedateservice->now());
        $this->modulerepo->save($module);
        $this->logger->info(
            "Module sent, lrid: {$lrid}",
            'module_entity',
            $module->id,
            $module->version,
            (string)json_encode($statement->dump()),
        );
    }
}
