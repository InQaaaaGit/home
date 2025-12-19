<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class factdef_extension_delete_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private factdef_repository $factdefrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        factdef_repository $factdefrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->factdefrepo = $factdefrepo;
    }

    public function execute(int $factdefid, string $extensionname): void {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        if (isset($factdef->resultextensions[$extensionname])) {
            $factdef->del_resultextension($extensionname, $this->timedateservice->now());
        } else if (isset($factdef->contextextensions[$extensionname])) {
            $factdef->del_contextextension($extensionname, $this->timedateservice->now());
        } else {
            throw new \InvalidArgumentException();
        }
        $this->factdefrepo->save($factdef);
        $this->logger->info(
            "FD Extension deleted, factdefid: {$factdefid}, extension: {$extensionname}",
            'factdef_entity',
            $factdef->id,
            $factdef->version,
        );
    }
}
