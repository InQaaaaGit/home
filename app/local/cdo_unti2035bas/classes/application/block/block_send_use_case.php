<?php
namespace local_cdo_unti2035bas\application\block;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class block_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private block_xapi_service $blockxapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        block_xapi_service $blockxapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->blockxapiservice = $blockxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $blockid): void {
        $block = $this->blockrepo->read($blockid);
        if (!$block) {
            throw new \InvalidArgumentException();
        }
        $stream = $this->streamrepo->read($block->streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->blockxapiservice->execute($stream, $block);
        [$lrid] = $this->xapiclient->send([$statement]);
        $block->set_sentdata($lrid, $this->timedateservice->now());
        $this->blockrepo->save($block);
        $this->logger->info(
            "Block sent, lrid: {$lrid}",
            'block_entity',
            $block->id,
            $block->version,
            (string)json_encode($statement->dump()),
        );
    }
}
