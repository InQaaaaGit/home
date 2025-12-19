<?php
namespace local_cdo_unti2035bas\application\log;

use local_cdo_unti2035bas\infrastructure\persistence\log_record_repository;


class log_read_use_case {
    private log_record_repository $logrecordrepo;

    public function __construct(
        log_record_repository $logrecordrepo
    ) {
        $this->logrecordrepo = $logrecordrepo;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array<string, string|int> $filter
     * @return list<list<array<string, mixed>>|int>
     */
    public function execute(
        int $limit,
        int $offset,
        array $filter
    ): array {
        $records = $this->logrecordrepo->read($limit, $offset, $filter);
        $res = [];
        foreach ($records as $record) {
            $res[] = [
                'timestamp' => $record->timestamp,
                'timestamp_display' => userdate(
                    $record->timestamp,
                    get_string('strftimedatetime', 'langconfig'),
                ),
                'level' => $record->level,
                'message' => $record->message,
                'object_' => $record->object,
                'objectid' => $record->objectid,
                'objectversion' => $record->objectversion,
            ];
        }
        $total = $this->logrecordrepo->count($filter);
        return [$res, $total];
    }
}
