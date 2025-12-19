<?php

namespace tool_imageoptimize\hooks;

use core_files\hook\after_file_created;
use dml_exception;
use tool_image_optimize;
use tool_imageoptimize\tool_image_optimize_helper;
use tool_imageoptimize\tool_pdf_optimizer;

class optimizer
{
    /**
     * @throws dml_exception
     */
    public static function optimize(after_file_created $hook): void
    {
        $filerecord = $hook->filerecord;
        if (during_initial_install()) {
            return;
        }
        if (!get_config('tool_imageoptimize', 'version')) {
            return;
        }

        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->get_enabled_mimetypes();

        $is_image = false;
        $is_pdf = false;

        if (in_array($filerecord->mimetype, $imageoptimizehelper->enabledmimetypes)) {
            $is_image = true;
        }

        if ($filerecord->mimetype === 'application/pdf' && !!get_config('tool_imageoptimize', 'pdf_enabled')) {
            $is_pdf = true;
        }

        if ($is_image && empty(get_config('tool_imageoptimize', 'enablebackgroundoptimizing'))) {
            $obj = new tool_image_optimize($filerecord);
            $obj->handle('create');
        }

        if ($is_pdf && !empty(get_config('tool_imageoptimize', 'pdf_converter_token'))) {
            $obj = new tool_pdf_optimizer($filerecord);
            $obj->handle('create');
        }
        //secret_laD1x8Q33GRezeod
        $imageoptimizehelper->insert_fileinfo_depending_on_contenthash($filerecord);
    }
}