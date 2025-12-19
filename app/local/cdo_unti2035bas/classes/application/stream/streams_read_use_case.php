<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class streams_read_use_case {
    private stream_repository $streamrepo;
    private moodle_service $moodleservice;

    public function __construct(
        stream_repository $streamrepo,
        moodle_service $moodleservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->moodleservice = $moodleservice;
    }

    /**
     * @return list<list<array<string, mixed>>|int>
     */
    public function execute(int $limit, int $offset): array {
        $streams = $this->streamrepo->read_all($limit, $offset);
        $courses = $this->moodleservice->get_courses(
            array_unique(array_map(fn($s) => $s->moodle->courseid, $streams)),
        );
        $groups = $this->moodleservice->get_groups(
            array_unique(array_map(fn($s) => $s->moodle->groupid, $streams)),
        );
        $res = [];
        foreach ($streams as $stream) {
            $res[] = [
                'id' => $stream->id,
                'timestamp' => $stream->timestamp,
                'timestamp_display' => userdate(
                    $stream->timestamp,
                    get_string('strftimedatetime', 'langconfig'),
                ),
                'timesent' => $stream->timesent,
                'course' => $courses[$stream->moodle->courseid]["fullname"],
                'group' => $groups[$stream->moodle->groupid],
                'program' => $stream->unti->programid,
                'methodist' => $stream->unti->methodistid,
                'flow' => $stream->unti->flowid,
                'lrid' => $stream->lrid,
                'isonline' => $stream->isonline,
            ];
        }
        $total = $this->streamrepo->count_all();
        return [$res, $total];
    }
}
