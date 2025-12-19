<?php

class block_cdo_main extends block_base
{
    public function init(): void {
        $this->title = get_string('pluginname', 'block_cdo_main');
    }

    public function get_content(): stdClass {
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        try {
            $render = $PAGE->get_renderer('block_cdo_main', 'main');
            $html = $render->render(new \block_cdo_main\output\main\renderable());
            $render_data = $html;
        } catch (Exception $ex) {
            $render_data = $ex->getMessage();
        }


        $this->content->text = $render_data;

        return $this->content;
    }
}