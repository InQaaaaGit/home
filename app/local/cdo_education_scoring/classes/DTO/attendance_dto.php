<?php

namespace local_cdo_education_scoring\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;

/**
 * DTO для данных о посещаемости студента.
 *
 * @package    local_cdo_education_scoring
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class attendance_dto extends base_dto
{
    public ?float $percent;

    protected function get_object_name(): string
    {
        return "attendance";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
        $this->percent = $data->percent ?? null;

        return $this;
    }
}

