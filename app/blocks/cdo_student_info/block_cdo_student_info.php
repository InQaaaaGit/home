<?php

use block_cdo_student_info\output\student_info\renderable;

class block_cdo_student_info extends block_base
{

    const COMPONENT_NAME = 'block_cdo_student_info';

    public function has_config(): bool
    {
        return true;
    }

    /**
     * @return void
     * @throws coding_exception
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', self::COMPONENT_NAME);
    }

    /**
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_content(): stdClass
    {
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        if (has_capability('block/cdo_student_info:view', context_system::instance())) {
            $render = $PAGE->get_renderer(self::COMPONENT_NAME, 'student_info');
            $html = $render->render(new renderable());
            $orders_link = "";
            if (get_config('block_cdo_student_info', 'use_orders')) {
                $orders_link = html_writer::div(html_writer::link(
                    new moodle_url('/blocks/cdo_student_info/my_orders.php'),
                    get_string('my_orders_link', self::COMPONENT_NAME),
                    ['class' => 'btn btn-primary w-100 mb-2']
                ));
            }
            $diplomas_link = "";
            if (get_config('block_cdo_student_info', 'use_diplomas')) {
                $diplomas_link = html_writer::div(html_writer::link(
                    new moodle_url('/blocks/cdo_student_info/my_diplomas.php'),
                    get_string('my_diplomas_link', self::COMPONENT_NAME),
                    ['class' => 'btn btn-primary w-100 mb-2']
                ));
            }
            $checklist_link = "";
            //TODO create capability
            if (get_config('block_cdo_student_info', 'use_checklist')) {
                $checklist_link = html_writer::div(html_writer::link(
                    new moodle_url('/blocks/cdo_student_info/pages/checklist.php'),
                    get_string('checklist_link', self::COMPONENT_NAME),
                    ['class' => 'btn btn-primary w-100 mb-2']
                ));
            }

            #$links = html_writer::div($orders_link);
            $this->content->text = $orders_link . $diplomas_link . $checklist_link . $html;

        } else {
            $this->content->text = "";
        }

        return $this->content;
    }
}
