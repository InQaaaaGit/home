<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class practice_diary_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private practice_diary_repository $practicediaryrepo;
    private practice_diary_xapi_service $practicediaryxapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        practice_diary_repository $practicediaryrepo,
        practice_diary_xapi_service $practicediaryxapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->practicediaryrepo = $practicediaryrepo;
        $this->practicediaryxapiservice = $practicediaryxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $diaryid): void {
        if (!$diary = $this->practicediaryrepo->read($diaryid)) {
            throw new \InvalidArgumentException();
        }
        if (!$stream = $this->streamrepo->read($diary->streamid)) {
            throw new consistency_error();
        }
        $blocks = $this->blockrepo->read_by_streamid($diary->streamid);
        $blockspractice = array_values(
            array_filter($blocks, fn($b) => $b->type == 'practical' && $b->deleted == false)
        );
        if (count($blockspractice) != 1) {
            throw new \Exception("practice block not found");
        }
        $block = $blockspractice[0];
        if (!$block->lrid) {
            throw new \Exception("lrid not present in block");
        }
        $statement = $this->practicediaryxapiservice->execute(
            $stream,
            $block,
            $diary,
        );
        [$lrid] = $this->xapiclient->send([$statement]);
        $diary->set_sentdata($lrid, $this->timedateservice->now());
        $this->practicediaryrepo->save($diary);
        $this->logger->info(
            "Practice diary sent, lrid: {$lrid}",
            'practice_diary_entity',
            $diary->id,
            null,
            (string)json_encode($statement->dump()),
        );
    }
}
