<?php
namespace local_cdo_unti2035bas\application\activity;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\activity_config_vo;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class activity_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private activity_repository $activityrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        activity_repository $activityrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->activityrepo = $activityrepo;
    }

    public function execute(
        int $activityid,
        bool $required,
        bool $collaborative,
        float $lectureshours,
        float $workshopshours,
        float $independentworkhours,
        int $resultcomparability,
        ?string $admittanceform
    ): void {
        $activity = $this->activityrepo->read($activityid);
        if (!$activity) {
            throw new \InvalidArgumentException();
        }
        $updated = $activity->set_configdata(
            new activity_config_vo(
                $required,
                $collaborative,
                $lectureshours,
                $workshopshours,
                $independentworkhours,
                $resultcomparability,
                $admittanceform,
            ),
            $this->timedateservice->now(),
        );
        if ($updated) {
            $this->activityrepo->save($activity);
            $this->logger->info(
                'Activity updated',
                'activity_entity',
                $activity->id,
                $activity->version,
            );
        }
    }
}
