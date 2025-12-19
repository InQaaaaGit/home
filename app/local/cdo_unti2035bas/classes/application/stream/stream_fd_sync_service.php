<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\factdef_entity;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class stream_fd_sync_service {
    private log_service $logger;
    private timedate_service $timedateservice;
    private block_repository $blockrepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private factdef_repository $factdefrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        block_repository $blockrepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo,
        factdef_repository $factdefrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->blockrepo = $blockrepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->factdefrepo = $factdefrepo;
    }

    public function execute(int $streamid): void {
        $blocks = $this->blockrepo->read_by_streamid($streamid);
        $block = array_values(array_filter($blocks, fn($b) => $b->type == 'practical'))[0];
        /** @var int $blockid */
        $blockid = $block->id;
        $activities = $this->activityrepo->read_by_blockid($blockid);
        $activitiesmap = [];
        foreach ($activities as $activity) {
            $activitiesmap[$activity->id] = $activity;
        }
        $assessments = [
            ...$this->assessmentrepo->read_by_streamid($streamid),
            ...$this->assessmentrepo->read_all_by_blockid($blockid),
        ];
        $assessmentsmap = [];
        foreach ($assessments as $assessment) {
            $assessmentsmap[$assessment->id] = $assessment;
        }
        $factdefs = $this->factdefrepo->read_all_by_streamid($streamid);
        foreach ($factdefs as $factdef) {
            if ($factdef->baseobject == 'activity_entity') {
                $activityid = $factdef->baseobjectid;
                $activity = $activitiesmap[$activityid] ?? null;
                if (!$activity) {
                    if (!$factdef->deleted) {
                        $factdef->set_deleted($this->timedateservice->now());
                        $factdef = $this->factdefrepo->save($factdef);
                        $this->logger->error(
                            "Not found activity_entity: {$activityid}",
                            'factdef_entity',
                            $factdef->id,
                            $factdef->version,
                        );
                    }
                } else {
                    if ($activity->deleted && !$factdef->deleted) {
                        $factdef->set_deleted($this->timedateservice->now());
                        $factdef = $this->factdefrepo->save($factdef);
                        $this->logger->info(
                            "Found deleted activity_entity: {$activity->id}",
                            'factdef_entitiy',
                            $factdef->id,
                            $factdef->version,
                        );
                    }
                    unset($activitiesmap[$activityid]);
                }
            }
            if ($factdef->baseobject == 'assessment_entity') {
                $assessmentid = $factdef->baseobjectid;
                $assessment = $assessmentsmap[$assessmentid] ?? null;
                if (!$assessment) {
                    if (!$factdef->deleted) {
                        $factdef->set_deleted($this->timedateservice->now());
                        $factdef = $this->factdefrepo->save($factdef);
                        $this->logger->error(
                            "Not found assessment_entity: {$assessmentid}",
                            'factdef_entity',
                            $factdef->id,
                            $factdef->version,
                        );
                    }
                } else {
                    if ($assessment->deleted && !$factdef->deleted) {
                        $factdef->set_deleted($this->timedateservice->now());
                        $factdef = $this->factdefrepo->save($factdef);
                        $this->logger->info(
                            "Found deleted assessment_entity: {$assessmentid}",
                            'factdef_entitiy',
                            $factdef->id,
                            $factdef->version,
                        );
                    }
                    unset($assessmentsmap[$assessmentid]);
                }
            }
        }
        foreach ($activitiesmap as $activity) {
            /** @var int $activityid */
            $activityid = $activity->id;
            $factdef = new factdef_entity(
                null,
                null,
                $streamid,
                'activity_entity',
                $activityid,
                $this->timedateservice->now(),
                [],
                [],
                null,
            );
            $factdef = $this->factdefrepo->save($factdef);
            $this->logger->info(
                "Found activity_entity: {$activityid}",
                'factdef_entity',
                $factdef->id,
                $factdef->version,
            );
        }
        foreach ($assessmentsmap as $assessment) {
            /** @var int $assessmentid */
            $assessmentid = $assessment->id;
            $factdef = new factdef_entity(
                null,
                null,
                $streamid,
                'assessment_entity',
                $assessmentid,
                $this->timedateservice->now(),
                [],
                [],
                null,
            );
            $factdef = $this->factdefrepo->save($factdef);
            $this->logger->info(
                "Found assessment_entity: {$assessmentid}",
                'factdef_entity',
                $factdef->id,
                $factdef->version,
            );
        }
    }
}
