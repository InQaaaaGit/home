<?php

use block_cdo_schedule\output\schedule\renderable;

class block_cdo_schedule extends block_base
{

    /**
     * @return void
     * @throws coding_exception
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', 'block_cdo_schedule');
    }

    public function has_config(): bool
    {
        return true;
    }
    /**
     * @return stdClass
     * @throws coding_exception
     */
    public function get_content(): stdClass
    {
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $render = $PAGE->get_renderer('block_cdo_schedule', 'schedule');

        $html = $render->render(new renderable());
        $this->content = new stdClass;
        $this->content->text = $html;

        return $this->content;
    }
}