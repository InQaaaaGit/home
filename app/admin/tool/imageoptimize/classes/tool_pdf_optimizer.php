<?php

namespace tool_imageoptimize;

use dml_exception;
use stdClass;
use tool_imageoptimize\interfaces\optimizer;

class tool_pdf_optimizer
{

    protected ?\moodle_database $db = null;

    protected null|object $cfg = null;

    protected stdClass $filerecord;
    protected stdClass $sourcefilerecord;

    const PDF_MIME_TYPE = 'application/pdf';
    public function __construct(stdClass $filerecord = null, stdClass $sourcefilerecord = null)
    {
        global $DB, $CFG;
        $this->cfg = $CFG;
        $this->db = $DB;
        if ($filerecord) {
            $this->filerecord = $filerecord;
        }
        if ($sourcefilerecord) {
            $this->sourcefilerecord = $sourcefilerecord;
        }
    }

    protected function get_optimizer() :optimizer
    {
        //TODO create settings to choose compressors, Factory possible usage here
        return new convert_api_pdf_compressor();
    }

    /**
     * @throws dml_exception
     */
    public function handle(string $action): bool
    {

        if ($this->filerecord->mimetype === self::PDF_MIME_TYPE && $this->filerecord->filearea !== 'draft') {
            if ($fileStorage = get_file_storage()) {
                if ($fileSystem = $fileStorage->get_file_system()) {
                    if ($instance = $fileStorage->get_file_by_id($this->filerecord->id)) {
                        $fromFileContent = $fileSystem->get_content($instance);
                        $fromFileSourcePath = $fileSystem->get_local_path_from_storedfile($instance);
                        $fromFilePath = $this->temp_file_path();
                        $toFilePath = $this->temp_file_path();
                        file_put_contents($fromFilePath, $fromFileContent);
                        if (file_exists($fromFilePath)) {
                            $originalhash = $this->filerecord->contenthash;

                            $optimizer = $this->get_optimizer();
                            $optimizer->optimize($fromFilePath, $toFilePath);

                            $stored = $fileSystem->add_file_from_path($toFilePath);
                            if (!empty($stored[1]) && $stored[1] > 0) {
                                $sql = "UPDATE {files}
                                            SET contenthash=?,filesize=?
                                            WHERE contenthash=?";
                                $params = array(
                                    $stored[0],
                                    $stored[1],
                                    $originalhash,
                                );
                                $this->db->execute($sql, $params);
                                $fileSystem->remove_file($originalhash);
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function temp_file_path(): string {
        global $CFG;
        return $CFG->tempdir . '/' . random_string() . '.pdf';
    }
}