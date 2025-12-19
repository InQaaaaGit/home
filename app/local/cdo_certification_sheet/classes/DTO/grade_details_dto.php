<?php

namespace local_cdo_certification_sheet\DTO;

class grade_details_dto {
    public rating_dto $adr;
    public rating_dto $ricd;
    public rating_dto $frd;
    public rating_dto $ysc;
    public rating_dto $grade;
    public teacher_dto $teacher;

    public function __construct(?object $data = null) {
        $this->adr = new rating_dto($data->adr ?? null);
        $this->ricd = new rating_dto($data->ricd ?? null);
        $this->frd = new rating_dto($data->frd ?? null);
        $this->ysc = new rating_dto($data->ysc ?? null);
        $this->grade = new rating_dto($data->grade ?? null);
        $this->teacher = new teacher_dto($data->teacher ?? null);
    }
} 