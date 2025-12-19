<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\stream\stream_students_read_service;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class facts_filter_data_read_use_case {
    private stream_repository $streamrepo;
    private factdef_repository $factdefrepo;
    private stream_students_read_service $streamstudentsservice;

    public function __construct(
        stream_repository $streamrepo,
        factdef_repository $factdefrepo,
        stream_students_read_service $streamstudentsservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->factdefrepo = $factdefrepo;
        $this->streamstudentsservice = $streamstudentsservice;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function execute(int $factdefid): array {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        if (!$stream = $this->streamrepo->read($factdef->streamid)) {
            throw new consistency_error();
        }
        $students = $this->streamstudentsservice->execute($stream);
        return [
            'students' => $students,
        ];
    }
}
