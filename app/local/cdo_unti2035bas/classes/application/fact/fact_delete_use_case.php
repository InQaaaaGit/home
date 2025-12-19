<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;


class fact_delete_use_case {
    private log_service $logger;
    private fact_repository $factrepo;

    public function __construct(
        log_service $logger,
        fact_repository $factrepo
    ) {
        $this->logger = $logger;
        $this->factrepo = $factrepo;
    }

    public function execute(int $factid): void {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        $fact = $this->factrepo->save($fact);
        if (!$fact->can_delete()) {
            throw new \Exception();
        }
        $this->factrepo->delete($factid);
        $this->logger->info(
            'Fact deleted',
            'fact_entity',
            $fact->id,
        );
    }
}
