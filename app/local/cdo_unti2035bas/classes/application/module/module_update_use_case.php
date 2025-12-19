<?php
namespace local_cdo_unti2035bas\application\module;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\module_unti_vo;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class module_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private module_repository $modulerepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        module_repository $modulerepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->modulerepo = $modulerepo;
    }

    public function execute(int $moduleid, int $moduleuntiid): void {
        $module = $this->modulerepo->read($moduleid);
        if (!$module) {
            throw new \InvalidArgumentException();
        }
        $updated = $module->set_untidata(
            new module_unti_vo($moduleuntiid),
            $this->timedateservice->now()
        );
        if ($updated) {
            $this->modulerepo->save($module);
            $this->logger->info(
                "Module udated, moduleuntiid: {$moduleuntiid}",
                'module_entity',
                $module->id,
                $module->version,
            );
        }
    }
}
