<?php


class block_slider extends block_base
{

    function init()
    {
        $this->title = get_string('pluginname', 'block_slider');
    }


    function has_config()
    {
        return true;
    }

    /* function applicable_formats()
     {
         return array(
             'course-view' => true,
             'site-index' => true,
             'my-index' => true
         );
     }*/

    public
    function get_content()
    {

        global $CFG, $USER, $PAGE, $OUTPUT;
       // require_once($CFG->dirroot . '/blocks/slider/lib.php');
        if ($this->content !== NULL) {
            return $this->content;
        }
        $PAGE->requires->js_call_amd('block_slider/main', 'init');
        $PAGE->requires->css(new moodle_url($CFG->wwwroot."/blocks/slider/css/style.css"));
        $alist = "";
        $context = context_block::instance($this->instance->id);
        $on_front_page = 0;
        if ($context instanceof context_block) {
            $on_front_page = strpos($context->path, '/2/');
        }

        if (is_siteadmin()) {
            $parsingExcel = html_writer::link(
                new moodle_url("/blocks/slider/parsing_excel.php"),
                "Перейти к загрузке CSV для установки зарвешения модулей"
            );
            $link = html_writer::link(
                new moodle_url("/blocks/slider/admin.php"),
                "Перейти к заполнению слайдов"
            );
            $link_cat = html_writer::link(
                new moodle_url("/blocks/slider/list_categories.php"),
                "Перейти к добавлению изображений к категориям"
            );
            $alist = html_writer::alist([$link, $parsingExcel, $link_cat]);
        }
 /*       $element = 'block1.php';
        $candidate1 = $CFG->dirroot . 'theme/space/layout/parts/' . $element;
        include ($candidate1);
        $output = ob_get_clean();*/
   //     $block1 = create_frontpage_block1();

        $this->content = new stdClass;
        $this->content->text = $alist . ($content_block ?? '') ;

        return $this->content;

    }
}

