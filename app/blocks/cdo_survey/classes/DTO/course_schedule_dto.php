<?php

namespace block_cdo_survey\DTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class ScheduleDataDto extends base_dto {
    public ?string $scheduleGUID;
    public ?string $nameSchedule;
    public ?string $dataStartSchedule;
    public ?string $dataEndSchedule;

    public function build(object $data): base_dto {
        $this->scheduleGUID = $data->GUID_Schedule ?? null;
        $this->nameSchedule = $data->Name_Schedule ?? null;
        $this->dataStartSchedule = $data->DataStart_Schedule ?? null;
        $this->dataEndSchedule = $data->DataEnd_Schedule ?? null;
        return $this;
    }

    protected function get_object_name(): string
    {
        return "course_schedule";
    }
}

final class course_schedule_dto extends base_dto {

    public string $courseID;
    public response_dto $scheduleData;

    /**
     * @param  object  $data
     * @return base_dto
     */
    public function build(object $data): base_dto {
        $this->courseID = $data->IDCourse ;
        $this->scheduleData = response_dto::transform(ScheduleDataDto::class, $data->Schedule_Data);
        return $this;
    }

    protected function get_object_name(): string
    {
       return 'courseschedule';
    }
}
