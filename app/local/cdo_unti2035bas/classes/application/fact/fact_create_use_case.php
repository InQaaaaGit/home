<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\domain\fact_result_vo;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class fact_create_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private factdef_repository $factdefrepo;
    private fact_repository $factrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        factdef_repository $factdefrepo,
        fact_repository $factrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->factdefrepo = $factdefrepo;
        $this->factrepo = $factrepo;
    }

    public function execute(
        int $factdefid,
        int $actoruntiid,
        int $scoreraw,
        int $scoremin,
        int $scoremax,
        string $scoretarget,
        bool $success,
        string $duration,
        int $attemptsmax,
        int $attemptnum,
        ?int $instructoruntiid
    ): void {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        $fact = new fact_entity(
            null,
            null,
            $factdef->streamid,
            $factdefid,
            $actoruntiid,
            $this->timedateservice->now(),
            new fact_result_vo(
                $scoreraw,
                $scoremin,
                $scoremax,
                $scoretarget,
                $success,
                $duration,
                $attemptsmax,
                $attemptnum,
            ),
            [],
            [],
            $instructoruntiid,
        );
        $fact = $this->factrepo->save($fact);
        $this->logger->info(
            "Fact created, factdefid: {$factdefid}, actoruntiid: {$actoruntiid}",
            'fact_entity',
            $fact->id,
        );
    }
}
