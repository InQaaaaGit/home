<?php

class block_cdo_files_learning_plan extends block_base
{

    function init(): void
    {
        $this->title = 'Прикрепление файлов к образовательной программе';
    }

    function has_config()
    {
        return true;
    }

    function applicable_formats(): array
    {
        return array(
            'admin' => true,
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
            'my' => true
        );
    }

    function instance_allow_multiple(): bool
    {
        return false;
    }

    public function get_content(): string
    {
        global $PAGE,$CFG;
       /* if ($this->content === null && Helpers::checkPermission()->permission ) {
            //        $PAGE->requires->css(new moodle_url(__DIR__ . '/style/style.css'));
//            $PAGE->requires->js_call_amd('cdo_block_files_learning_plan/files-program-lazy', 'init');
            $this->content = new stdClass;
            $this->content->text = '
                <div><a href="/blocks/files_learning_plan/index.php">Прикрепить файлы</a></div>
            ';
        }*/

        return "";
    }

}
