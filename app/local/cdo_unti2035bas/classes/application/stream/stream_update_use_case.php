<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class stream_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
    }

    public function execute(
        int $streamid,
        int $academichourminutes,
        bool $isonline,
        string $comment
    ): void {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $updateddata = [];
        $updateactivites = false;
        if ($stream->academichourminutes != $academichourminutes) {
            $stream->set_academichourminutes($academichourminutes, $this->timedateservice->now());
            $updateactivites = true;
            $updateddata[] = "academichourminutes={$academichourminutes}";
        }
        if ($stream->isonline != $isonline) {
            $stream->set_isonline($isonline, $this->timedateservice->now());
            $updateddata[] = "isonline={$isonline}";
        }
        $stream->set_comment($comment);
        $stream = $this->streamrepo->save($stream);
        if ($updateddata) {
            $this->logger->info(
                'Stream updated: ' . join(",", $updateddata),
                'stream_entity',
                $stream->id,
                $stream->version,
            );
        }
        if ($updateactivites) {
            $activities = $this->activityrepo->read_by_streamid($streamid);
            foreach ($activities as $activity) {
                $activity->set_changed($this->timedateservice->now());
                $this->activityrepo->save($activity);
                $this->logger->info(
                    'Activity set changed by stream changed',
                    'activity_entity',
                    $activity->id,
                    $activity->version,
                );
            }
            $assessments = $this->assessmentrepo->read_all_by_streamid($streamid);
            foreach ($assessments as $assessment) {
                $assessment->set_changed($this->timedateservice->now());
                $this->assessmentrepo->save($assessment);
                $this->logger->info(
                    'Assessment set changed by stream changed',
                    'assessment_entity',
                    $assessment->id,
                    $assessment->version,
                );
            }
        }
    }
}
