<?php

namespace local_cdo_vkr\utility;

use context_system;
use dml_exception;

class fileinfo extends \stdClass
{
    public  $contextid;
    public  $component;
    public  $filearea;
    public  $itemid;
    public  $filepath;
    public  $filename;
    public  $userid;
    public  $params;

    /**
     * @throws dml_exception
     */
    public function __construct(

        $itemid,
        $filepath,
        $filename = "",
        $filearea = "vkr_area",
        $userid = 0
    )
    {
        global $USER;
        $this->contextid = context_system::instance()->id; //system context
        $this->component = 'local_cdo_vkr';
        $this->filearea = $filearea;
        $this->itemid = $itemid;
        $this->filepath = $filepath;
        $this->filename = $filename;
        $this->userid = $userid ?? $USER->id;
    }

    /**
     * @return array
     */
    public function getParamsArray(): array
    {
        return (array)$this;
    }

    public function get_contextid(): int
    {
        return $this->contextid;
    }

    public function get_component(): string
    {
        return $this->component;
    }

}