<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class practice_diary_read_due_students_use_case {
    private log_service $logger;
    private moodle_service $moodleservice;
    private stream_repository $streamrepo;
    private practice_diary_repository $practicediaryrepo;
    private string $userfielduntiid;

    public function __construct(
        log_service $logger,
        moodle_service $moodleservice,
        stream_repository $streamrepo,
        practice_diary_repository $practicediaryrepo,
        string $userfielduntiid
    ) {
        $this->logger = $logger;
        $this->moodleservice = $moodleservice;
        $this->streamrepo = $streamrepo;
        $this->practicediaryrepo = $practicediaryrepo;
        $this->userfielduntiid = $userfielduntiid;
    }

    /**
     * @return array<int, string>
     */
    public function execute(int $streamid): array {
        if (!$stream = $this->streamrepo->read($streamid)) {
            throw new \InvalidArgumentException();
        }
        $diaries = $this->practicediaryrepo->read_by_streamid($streamid);
        $diariesstudentsuntiids = array_values(array_map(fn($d) => $d->actoruntiid, $diaries));
        $students = $this->moodleservice->get_group_students($stream->moodle->groupid, $this->userfielduntiid);
        $res = [];
        foreach ($students as $student) {
            if (is_null($student->untiid) || !preg_match('/^\d+$/', $student->untiid)) {
                $this->logger->warning(
                    "Wrong untiid on user. Moodle userid: {$student->userid}, untiid: {$student->untiid}",
                );
                continue;
            }
            $untiid = (int)$student->untiid;
            if (in_array($untiid, $diariesstudentsuntiids)) {
                continue;
            }
            $res[$untiid] = $student->fullname;
        }
        return $res;
    }
}
