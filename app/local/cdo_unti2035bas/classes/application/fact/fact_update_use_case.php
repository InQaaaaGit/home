<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\fact_result_vo;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;


class fact_update_use_case {
    private log_service $logger;
    private fact_repository $factrepo;

    public function __construct(
        log_service $logger,
        fact_repository $factrepo
    ) {
        $this->logger = $logger;
        $this->factrepo = $factrepo;
    }

    public function execute(
        int $factid,
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
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        $fact->set_result(new fact_result_vo(
            $scoreraw,
            $scoremin,
            $scoremax,
            $scoretarget,
            $success,
            $duration,
            $attemptsmax,
            $attemptnum,
        ));
        $fact->set_instructor($instructoruntiid);
        $this->factrepo->save($fact);
        $this->logger->info(
            'Fact updated',
            'fact_entity',
            $fact->id,
        );
    }
}
