<?php
namespace local_cdo_unti2035bas\application\assessment;

use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;


class assessment_read_use_case {
    private assessment_repository $assessmentrepo;

    public function __construct(
        assessment_repository $assessmentrepo
    ) {
        $this->assessmentrepo = $assessmentrepo;
    }

    public function execute(int $assessmentid): assessment_entity {
        $assessment = $this->assessmentrepo->read($assessmentid);
        if (!$assessment) {
            throw new \InvalidArgumentException();
        }
        return $assessment;
    }
}
