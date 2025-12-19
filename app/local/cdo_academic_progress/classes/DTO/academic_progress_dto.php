<?php

namespace local_cdo_academic_progress\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class academic_progress_dto extends base_dto
{
    public int $semester;
    public string $semester_name;
    public int $order;
    public response_dto $academic_year;
    public ?response_dto $attestation;
    public ?float $average;
    public bool $attestation_not_empty;
    public bool $have_practice;
    public bool $have_vkr;
    public ?string $theme;
    public ?string $manager;
    public ?string $type;
    public bool $have_course_works;

    const ORDER_COURSE_WORK = 100;
    const ORDER_PRACTICE = 101;
    const ORDER_VKR = 102;
    public ?string $date_protocol_gak;
    public ?string $number_protocoal_gak;
    public ?string $qualification;
    public ?string $date_diplom_issued;
    public ?string $number_diplom;
    public ?string $serial_diplom;
    public ?string $chairman;
    public ?string $dean;
    public ?string $secretary;


    protected function get_object_name(): string
    {
        return "academic_progress";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {

        $this->semester = $data->semester ?? 0;
        $this->semester_name = $data->semester_name ?? '';
        $this->order = $data->order ?? 0;
        $this->theme = $data->theme ?? "";
        $this->manager = $data->manager ?? "";
        $this->type = $data->type ?? "";
        $this->academic_year = $data->academic_year
            ? response_dto::transform(directory_dto::class, $data->academic_year)
            : null;
        $this->attestation = $data->attestation
            ? response_dto::transform(attestation_dto::class, $data->attestation)
            : null;
        $is_vkr = self::ORDER_VKR === $this->order;
        $this->attestation_not_empty = !empty($this->attestation) || $is_vkr;
        $this->have_practice = $this->order === self::ORDER_PRACTICE; //for change template
        $this->have_vkr = $is_vkr; //for change template
        $this->have_course_works = $this->order === self::ORDER_COURSE_WORK;
        $this->date_protocol_gak = $data->date_protocol_gak ?? "";
        $this->number_protocoal_gak = $data->number_protocoal_gak ?? "";
        $this->qualification = $data->qualification ?? "";
        $this->date_diplom_issued = $data->date_diplom_issued ?? "";
        $this->number_diplom = $data->number_diplom ?? "";
        $this->serial_diplom = $data->serial_diplom ?? "";
        $this->chairman = $data->chairman ?? "";
        $this->dean = $data->dean ?? "";
        $this->secretary = $data->secretary ?? "";

        return $this;
    }
}