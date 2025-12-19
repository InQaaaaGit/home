<?php

use block_cdo_professional_info\output\professional_info\renderable;

class block_cdo_professional_info extends block_base
{
	/**
	 * @return void
	 * @throws coding_exception
	 */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_cdo_professional_info');
    }

    /**
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content(): stdClass
    {
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        if (has_capability('block/cdo_professional_info:view', context_system::instance())) {
            $render = $PAGE->get_renderer('block_cdo_professional_info', 'professional_info');
            $html = $render->render(new renderable());
            $this->content->text = $html;
        } else {
            $this->content->text = "";
        }

        return $this->content;
    }
}
