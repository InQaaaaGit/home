<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class themes extends base_dto
{

    public string $id;
    public ?response_dto $selectedTheme;
    public string $name;
    public string $target;
    public string $content;
    public string $result;
    public string $link;

    protected function get_object_name(): string
    {
        return 'themes';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->selectedTheme = $data->selectedTheme ?
            response_dto::transform(
                selected_theme::class,
                $data->selectedTheme
            ) : null;
        $this->name = $data->name;
        $this->target = $data->target;
        $this->content = $data->content;
        $this->result = $data->result;
        $this->link = $data->link;
        return $this;
    }
}