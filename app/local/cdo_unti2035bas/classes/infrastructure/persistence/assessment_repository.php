<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\assessment_entity;


class assessment_repository {
    public function save(assessment_entity $entity): assessment_entity {
        $persistent = assessment::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $assessmentid): ?assessment_entity {
        $persistent = assessment::get_record(['id' => $assessmentid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?assessment_entity {
        $persistent = assessment::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @param int $streamid
     * @return array<assessment_entity>
     */
    public function read_by_streamid(int $streamid): array {
        $persistents = assessment::get_records(['parentobjectid' => $streamid, 'parentobject' => 'stream_entity']);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * @param int $streamid
     * @return array<assessment_entity>
     */
    public function read_all_by_streamid(int $streamid): array {
        global $DB;
        $fields = assessment::get_sql_fields('a', 'ass_');
        $sql = "SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                JOIN {cdo_unti2035bas_theme} AS t ON t.id = a.parentobjectid
                JOIN {cdo_unti2035bas_module} AS m ON m.id = t.moduleid
                JOIN {cdo_unti2035bas_block} AS b ON b.id = m.blockid
                WHERE b.streamid = :streamid1 AND a.parentobject = 'theme_entity'
                UNION
                SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                JOIN {cdo_unti2035bas_module} AS m ON m.id = a.parentobjectid
                JOIN {cdo_unti2035bas_block} AS b ON b.id = m.blockid
                WHERE b.streamid = :streamid2 AND a.parentobject = 'module_entity'
                UNION
                SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                JOIN {cdo_unti2035bas_block} AS b ON b.id = a.parentobjectid
                WHERE b.streamid = :streamid3 AND a.parentobject = 'block_entity'
                UNION
                SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                WHERE a.parentobjectid = :streamid4 AND a.parentobject = 'stream_entity'
        ";
        $rows = $DB->get_records_sql(
            $sql,
            ['streamid1' => $streamid, 'streamid2' => $streamid, 'streamid3' => $streamid, 'streamid4' => $streamid],
        );
        $persistents = array_map(
            fn($row) => new assessment(0, assessment::extract_record($row, 'ass_')),
            $rows,
        );
        return array_values(array_map(fn($p) => $p->to_domain(), $persistents));
    }


    /**
     * @param int $blockid
     * @return array<assessment_entity>
     */
    public function read_by_blockid(int $blockid): array {
        $persistents = assessment::get_records(['parentobjectid' => $blockid, 'parentobject' => 'block_entity']);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * @param int $blockid
     * @return array<assessment_entity>
     */
    public function read_all_by_blockid(int $blockid): array {
        global $DB;
        $fields = assessment::get_sql_fields('a', 'ass_');
        $sql = "SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                JOIN {cdo_unti2035bas_theme} AS t ON t.id = a.parentobjectid
                JOIN {cdo_unti2035bas_module} AS m ON m.id = t.moduleid
                WHERE m.blockid = :blockid1 AND a.parentobject = 'theme_entity'
                UNION
                SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                JOIN {cdo_unti2035bas_module} AS m ON m.id = a.parentobjectid
                WHERE m.blockid = :blockid2 AND a.parentobject = 'module_entity'
                UNION
                SELECT {$fields}
                FROM {cdo_unti2035bas_assessment} AS a
                WHERE a.parentobjectid = :blockid3 AND a.parentobject = 'block_entity'
        ";
        $rows = $DB->get_records_sql(
            $sql,
            ['blockid1' => $blockid, 'blockid2' => $blockid, 'blockid3' => $blockid],
        );
        $persistents = array_map(
            fn($row) => new assessment(0, assessment::extract_record($row, 'ass_')),
            $rows,
        );
        return array_values(array_map(fn($p) => $p->to_domain(), $persistents));
    }

    /**
     * @param int $moduleid
     * @return array<assessment_entity>
     */
    public function read_by_moduleid(int $moduleid): array {
        $persistents = assessment::get_records(['parentobjectid' => $moduleid, 'parentobject' => 'module_entity']);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * @param int $themeid
     * @return array<assessment_entity>
     */
    public function read_by_themeid(int $themeid): array {
        $persistents = assessment::get_records(['parentobjectid' => $themeid, 'parentobject' => 'theme_entity']);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }
}
