<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class main_selected extends base_dto
{

    public string $id;
    public string $book;
    public string $author;
    public string $year;
    public string $publishing;
    public string $count;
    public ?string $approval;
    public ?string $commentary;

    protected function get_object_name(): string
    {
        return 'ms';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id ?? '';
        $this->book = $data->book;
        $this->author = $data->author;
        $this->year = $data->year;
        $this->publishing = $data->publishing;
        $this->count = $data->count;
        $this->approval = $data->approval;
        $this->commentary = $data->approval;

        return $this;
    }
}