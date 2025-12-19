<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class student_certification_sheet_dto extends base_dto {

    public string $full_name;
    public string $grade_book;
    public string $guid;
    public string $grade;
    public ?string $teacher_code;
    public ?string $short_name;
    public ?string $color;
    public ?string $teacher_full_name;
    public ?string $theme;
    public ?string $adr;
    public ?string $ricd;
    public ?string $frd;
    public ?string $ysc;
    public ?string $note;

    public function build(object $data): base_dto {
        $this->full_name = $data->FIO ?? null;
        $this->grade_book = $data->gradebook ?? null;
        $this->guid = $data->GUID ?? null;
        $this->grade = $data->Grade ?? null;
        $this->teacher_code = $data->teach ?? null;
        $this->short_name = $data->short_name ?? '';
        $this->color = $data->color ?? null;
        $this->teacher_full_name = $data->teach_fio ?? null;
        $this->adr = $data->Average_discipline_rating ?? '';
        $this->ricd = $data->Rating_intermediate_certification_discipline ?? '';
        $this->frd = $data->Final_rating_discipline ?? '';
        $this->ysc = $data->ysc ?? '';
        $this->note = $data->note ?? '';
        $this->theme = $data->theme ?? '';
        return $this;
    }

    protected function get_object_name(): string {
        return "student_certification_sheet";
    }
}