<?php

namespace local_cdo_ag_tools\task;

use core\task\adhoc_task;
use dml_exception;
use local_cdo_ag_tools\controllers\repository_works;
use local_cdo_ag_tools\qr\generate_pdf;
use local_cdo_ag_tools\qr\generate_qr;
use local_cdo_ag_tools\qr\render;
use moodle_exception;
use repository_exception;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;

class create_archive extends adhoc_task
{
    use \core\task\logging_trait;

    /**
     * @inheritDoc
     * @throws dml_exception
     * @throws moodle_exception
     * @throws repository_exception
     * @throws PdfParserException
     * @throws PdfReaderException
     */
    public function execute(): void
    {
        $this->log_start("start generate archive");

        // Retrieve the user ID from customdata.
        $data = $this->get_custom_data();

        // If the user ID is not provided, log an error and return.
        if (is_null($data->userid)) {
            $this->log("User ID not provided in customdata.");
            return;
        }

        $s = new repository_works(
            new generate_pdf(
                new generate_qr(
                    new render()
                )
            ),
            $data->userid // Pass the user ID
        );
        $s->generate_output_zip_with_works();
        $this->log_finish("finish generate archive");
    }

    public static function queue(): void
    {
        $task = new self();
        \core\task\manager::queue_adhoc_task($task);
    }

    public static function instance(
        int $userid
    ): self
    {
        $task = new self();
        $task->set_custom_data((object)[
            'userid' => $userid,
        ]);

        return $task;
    }
}
