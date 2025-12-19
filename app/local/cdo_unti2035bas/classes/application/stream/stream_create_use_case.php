<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\stream_moodle_vo;
use local_cdo_unti2035bas\domain\stream_unti_vo;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class stream_create_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private stream_sync_service $streamsyncservice;
    private stream_fd_sync_service $streamfdsyncservice;
    private moodle_service $moodleservice;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        stream_sync_service $streamsyncservice,
        stream_fd_sync_service $streamfdsyncservice,
        moodle_service $moodleservice
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->streamsyncservice = $streamsyncservice;
        $this->streamfdsyncservice = $streamfdsyncservice;
        $this->moodleservice = $moodleservice;
    }

    public function execute(
        int $courseid,
        int $groupid,
        int $untiprogramid,
        int $untiflowid,
        int $untimethodistid,
        int $academichourminutes,
        bool $isonline,
        string $comment
    ): void {
        [$course] = array_values($this->moodleservice->get_courses([$courseid]));
        /** @var int $sectionid */
        $sectionid = $course['sectionid'];
        $stream = new stream_entity(
            null,
            null,
            $this->timedateservice->now(),
            new stream_moodle_vo($courseid, $groupid, $sectionid),
            new stream_unti_vo(null, $untiprogramid, $untiflowid, $untimethodistid),
            $academichourminutes,
            $isonline,
            $comment,
        );
        $stream = $this->streamrepo->save($stream);
        $this->logger->info(
            "Stream created, moodle courseid: {$courseid}, groupid: {$groupid}",
            'stream_entity',
            $stream->id,
            $stream->version,
        );
        $this->streamsyncservice->execute($stream);
        /** @var int $streamid */
        $streamid = $stream->id;
        $this->streamfdsyncservice->execute($streamid);
    }
}
