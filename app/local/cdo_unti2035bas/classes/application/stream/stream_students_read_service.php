<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;


class stream_students_read_service {
    private log_service $logger;
    private moodle_service $moodleservice;
    private string $userfielduntiid;

    public function __construct(
        log_service $logger,
        moodle_service $moodleservice,
        string $userfielduntiid
    ) {
        $this->logger = $logger;
        $this->moodleservice = $moodleservice;
        $this->userfielduntiid = $userfielduntiid;
    }

    /**
     * @return array<mixed>
     */
    public function execute(stream_entity $stream): array {
        $moodlestudents = $this->moodleservice->get_group_students($stream->moodle->groupid, $this->userfielduntiid);
        $students = [];
        foreach ($moodlestudents as $moodlestudent) {
            if (is_null($moodlestudent->untiid) || !preg_match('/^\d+$/', $moodlestudent->untiid)) {
                $this->logger->warning(
                    "Wrong untiid on user. Moodle userid: {$moodlestudent->userid}, untiid: {$moodlestudent->untiid}",
                );
                continue;
            }
            $students[] = [
                'fullname' => $moodlestudent->fullname,
                'actoruntiid' => (int)$moodlestudent->untiid,
            ];
        }
        return $students;
    }
}
