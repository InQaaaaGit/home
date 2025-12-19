<?php

namespace local_cdo_rpd\DTO;

use tool_cdo_config\request\DTO\base_dto;

class literature_data extends base_dto
{
    public string $EBS;
    public string $ISBN;
    public string $author;
    public string $name;
    public string $publishing;
    public string $year;
    public string $ISSN;
    public string $link;
    public string $count;
    public string $place_publication;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'literature_data';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->EBS = $data->EBS ?? '';
        $this->ISBN = $data->ISBN ?? '';
        $this->author = $data->author ?? '';
        $this->name = $data->name ?? '';
        $this->publishing = $data->publishing ?? '';
        $this->year = $data->year ?? '';
        $this->ISSN = $data->ISSN ?? '';
        $this->link = $data->link ?? '';
        $this->count = $data->count ?? '';
        $this->place_publication = $data->place_publication ?? '';

        return $this;
    }
}