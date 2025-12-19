<?php

/**
 *
 */
class block_questionnaire extends block_base
{

    protected string $component_name = 'block_questionnaire';
    /**
     * @return void
     * @throws coding_exception
     */
    function init(): void
    {
        $this->title = get_string('pluginname', $this->component_name);
    }

    /**
     * @return bool
     */
    function has_config(): bool
    {
        return true;
    }

    /**
     * Returns the content object
     *
     * @throws dml_exception
     * @throws coding_exception
     */
    function get_content()
    {

        global $USER;
        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = "";
        if (has_capability('block/questionnaire:view', context_system::instance())) {
            $this->content->text = "Не указан маршрут для анкетирования";

            $link = get_config($this->component_name, 'link');
            if (!$link) {
                return $this->content;
            }

            $link = str_replace("##user_id##", $USER->id, $link);
            $title = get_config($this->component_name, 'title');
            $title = !$title ? $this->title : $title;
            $link = html_writer::div(html_writer::link($link, $title, ["target" => "_blank"]));

            // 344-17 - request
            $link1html = '';
            $link1 = get_config($this->component_name, 'link1');
            $title1 = get_config($this->component_name, 'title1');
            if ($link1) {
                $link1html = html_writer::div(html_writer::link($link1, $title1, ["target" => "_blank"]));
            }
            $link2html = '';
            $link2 = get_config($this->component_name, 'link2');
            $title2 = get_config($this->component_name, 'title2');
            if ($link2) {
                $link2html = html_writer::div(html_writer::link($link2, $title2, ["target" => "_blank"]));
            }

            $this->content->text = $link . $link1html . $link2html;
        }
        return $this->content;
    }
}

