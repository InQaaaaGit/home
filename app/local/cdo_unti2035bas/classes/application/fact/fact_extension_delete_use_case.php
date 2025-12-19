<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class fact_extension_delete_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private fact_repository $factrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        fact_repository $factrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->factrepo = $factrepo;
    }

    public function execute(int $factid, string $extensionname): void {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        if (isset($fact->resultextensions[$extensionname])) {
            $fact->del_resultextension($extensionname, $this->timedateservice->now());
        } else if (isset($fact->contextextensions[$extensionname])) {
            $fact->del_contextextension($extensionname, $this->timedateservice->now());
        } else {
            throw new \InvalidArgumentException();
        }
        $this->factrepo->save($fact);
        $this->logger->info(
            "Fact Extension deleted, factid: {$factid}, extension: {$extensionname}",
            'fact_entity',
            $fact->id,
            null,
        );
    }
}
