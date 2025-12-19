<?php
namespace local_cdo_unti2035bas\application\activity;

use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;


class activity_read_use_case {
    private activity_repository $activityrepo;

    public function __construct(
        activity_repository $activityrepo
    ) {
        $this->activityrepo = $activityrepo;
    }

    public function execute(int $activityid): activity_entity {
        $activity = $this->activityrepo->read($activityid);
        if (!$activity) {
            throw new \InvalidArgumentException();
        }
        return $activity;
    }
}
