<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use local_cdo_unti2035bas\ui\dependencies;


class facts_control implements renderable, templatable {
    private int $factdefid;
    private string $uniqid;
    private string $tableid;
    /** @var array<string, array<mixed>> $filterdata */
    public array $filterdata;
    public ?int $initialactoruntiid;

    public function __construct(int $factdefid, ?int $initialactoruntiid, string $tableid, dependencies $depends) {
        $this->factdefid = $factdefid;
        $this->initialactoruntiid = $initialactoruntiid;
        $this->uniqid = uniqid();
        $this->tableid = $tableid;
        $use_case = $depends->get_facts_filter_data_read_use_case();
        $this->filterdata = $use_case->execute($factdefid);
        /** @var array<array<string, mixed>> $students */
        $students = $this->filterdata['students'];
        /** @var array<int> $actualuntiids */
        $actualuntiids = array_values(array_map(fn($s) => $s['actoruntiid'], $students));
        if ($this->initialactoruntiid) {
            if (!in_array($this->initialactoruntiid, $actualuntiids)) {
                throw new \InvalidArgumentException('actoruntiid is out of available');
            }
        } else {
            $this->initialactoruntiid = $actualuntiids[0] ?? null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function export_for_template(renderer_base $output): array {
        $students = [];
        /** @var array<string, mixed> $student */
        foreach (array_values($this->filterdata['students']) as $idx => $student) {
            $student['selected'] = $student['actoruntiid'] == $this->initialactoruntiid;
            $students[] = $student;
        }
        return [
            'uniqid' => $this->uniqid,
            'factdefid' => $this->factdefid,
            'tableid' => $this->tableid,
            'students' => $students,
        ];
    }
}
