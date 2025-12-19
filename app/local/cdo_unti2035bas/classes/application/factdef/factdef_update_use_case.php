<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;


class factdef_update_use_case {
    private log_service $logger;
    private factdef_repository $factdefrepo;

    public function __construct(
        log_service $logger,
        factdef_repository $factdefrepo
    ) {
        $this->logger = $logger;
        $this->factdefrepo = $factdefrepo;
    }

    public function execute(
        int $factdefid,
        ?int $instructoruntiid
    ): void {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        $factdef->set_instructor($instructoruntiid);
        $this->factdefrepo->save($factdef);
        $this->logger->info(
            'Factdef updated',
            'factdef_entity',
            $factdef->id,
        );
    }
}
