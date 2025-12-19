<?php

namespace local_cdo_rpd\DTO;

use tool_cdo_config\request\DTO\base_dto;

class literature extends base_dto
{

    public ?string $link;
    public string|null $approval;
    public ?string $author;
    public ?string $book;
    public string|null $commentary;
    public int|string|null $count;
    public ?string $id;
    public ?string $publishing;
    public ?string $year;
    public ?bool $result;

    protected function get_object_name(): string
    {
        return 'literature';
    }

    public function build(object $data): base_dto
    {
        $this->approval = $data->approval ?? null;
        $this->author = $data->author;
        $this->book = $data->book;
        $this->commentary = $data->commentary ?? null;
        $this->count = $data->count;
        $this->id = $data->id;
        $this->link = $data->link;
        $this->publishing = $data->publishing;
        $this->year = $data->year;
        $this->result = $data->result ?? false;
        return $this;
    }
}