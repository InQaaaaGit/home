<?php

namespace local_cdo_certification_sheet\DTO;

class teacher_dto {
    public string $FIO;
    public ?int $user_id;

    public function __construct(?object $data = null) {
        $this->FIO = $data->FIO ?? '';
        $this->user_id = $data->user_id ?? null;
    }
} 