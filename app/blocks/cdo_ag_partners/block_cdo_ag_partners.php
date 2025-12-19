<?php

use local_cdo_ag_tools\files\controller_work_archive;

class block_cdo_ag_partners extends block_base
{
    const COMPONENT_NAME = 'block_cdo_ag_partners';

    /**
     * @throws coding_exception
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', self::COMPONENT_NAME);
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content(): stdClass
    {
        global $USER;
        /*if ($this->content !== null) {
            return $this->content;
        }*/
        $this->content = new stdClass;
        if (has_capability('blocks/cdo_ag_partners:view_download_report', context_system::instance(0))) {
            $download = html_writer::link(
                new moodle_url('/local/cdo_ag_tools/download_report_grade.php'),
                get_string('download_button', self::COMPONENT_NAME)
            );
            $upload = html_writer::link(
                new moodle_url('/local/cdo_ag_tools/upload.php'),
                get_string('upload_button', self::COMPONENT_NAME)
            );
            $qr = html_writer::link(
                new moodle_url('/local/cdo_ag_tools/qr.php'),
                get_string('download_qr', self::COMPONENT_NAME)
            );

            $cwa = new controller_work_archive();
            $file_url = ($cwa->create_file_link($USER->id));
            $list = [$download, $upload, $qr];
            if (!empty($file_url)) {
                $icon = html_writer::tag('i', '', ['class' => 'fa fa-download']);
                $link_text = $icon . ' ' . 'Файл с работами для скачивания';
                $list[] = html_writer::link($file_url, $link_text);
            }

            $block_body = html_writer::div(
                html_writer::alist($list),
                'container-fluid'
            );
            $this->content->text = $block_body;
        }

        return $this->content;
    }
}