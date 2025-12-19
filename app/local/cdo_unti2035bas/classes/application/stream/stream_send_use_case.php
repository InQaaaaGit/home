<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class stream_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private stream_xapi_service $streamxapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        stream_xapi_service $streamxapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->streamxapiservice = $streamxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $streamid): void {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->streamxapiservice->execute($stream);
        [$lrid] = $this->xapiclient->send([$statement]);
        $stream->set_sentdata($lrid, $this->timedateservice->now());
        $this->streamrepo->save($stream);
        $this->logger->info(
            "Course sent, lrid: {$lrid}",
            'stream_entity',
            $stream->id,
            $stream->version,
            (string)json_encode($statement->dump()),
        );
    }
}
