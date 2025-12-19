<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\log_record_vo;


class log_record_repository {
    public function save(log_record_vo $vo): void {
        $persistent = log_record::from_domain($vo);
        $persistent->save();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array<string, string|int> $filter
     * @return array<log_record_vo>
     */
    public function read(
        int $limit = 1000,
        int $offset = 0,
        array $filter = []
    ): array {
        $persistents = log_record::get_records($filter, 'id', 'DESC', $offset, $limit);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * @param array<string, int|string> $filter
     * @return int
     */
    public function count(array $filter): int {
        return log_record::count_records($filter);
    }
}
