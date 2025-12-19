<?php

namespace local_cdo_ok\DTO;

use tool_cdo_config\request\DTO\base_dto;

/**
 * DTO для информации о студенте из системы 1С
 *
 * @package    local_cdo_ok
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class student_info_dto extends base_dto
{
    public string $fio;
    public string $group;
    public string $edu_structure;
    public string $edu_spec;
    public string $edu_year;
    public string $edu_level;
    public string $edu_form;
    public string $user_id;

    protected function get_object_name(): string
    {
        return "student_info";
    }

    public function build(object $data): base_dto
    {
        $this->fio = (string)($data->fio ?? '');
        $this->group = (string)($data->group ?? '');
        $this->edu_structure = (string)($data->edu_structure ?? '');
        $this->edu_spec = (string)($data->edu_spec ?? '');
        $this->edu_year = (string)($data->edu_year ?? '');
        $this->edu_level = (string)($data->edu_level ?? '');
        $this->edu_form = (string)($data->edu_form ?? '');
        $this->user_id = (string)($data->user_id ?? '');

        return $this;
    }
}

