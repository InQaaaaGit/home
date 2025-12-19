<?php
namespace local_cdo_unti2035bas\application\assessment;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\assessment_config_vo;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class assessment_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private assessment_repository $assessmentrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        assessment_repository $assessmentrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->assessmentrepo = $assessmentrepo;
    }

    public function execute(
        int $assessmentid,
        float $lectureshours,
        float $workshopshours,
        float $independentworkhours,
        int $resultcomparability,
        bool $haspractice,
        ?string $documenttype
    ): void {
        $assessment = $this->assessmentrepo->read($assessmentid);
        if (!$assessment) {
            throw new \InvalidArgumentException();
        }
        $updated = $assessment->set_configdata(
            new assessment_config_vo(
                $lectureshours,
                $workshopshours,
                $independentworkhours,
                $resultcomparability,
                $haspractice,
                $documenttype,
            ),
            $this->timedateservice->now(),
        );
        if ($updated) {
            $this->assessmentrepo->save($assessment);
            $this->logger->info(
                'Assessment udated',
                'assessment_entity',
                $assessment->id,
                $assessment->version,
            );
        }
    }
}
