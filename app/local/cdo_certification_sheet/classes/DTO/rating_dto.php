<?php

namespace local_cdo_certification_sheet\DTO;

class rating_dto {
    public int|string $grade;
    public string $GUIDGrade;

    public function __construct(?object $data = null) {
        $this->grade = $data->grade ?? 0;
        $this->GUIDGrade = $data->GUIDGrade ?? '';
    }
} 