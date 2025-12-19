<?php
namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\infrastructure\config\fd_schema_service;

final class fd_schema_service_test extends \advanced_testcase {
    public function test_fd_schema_service(): void {
        global $CFG;
        $schemafilepath = join(DIRECTORY_SEPARATOR, [$CFG->dirroot, 'local', 'cdo_unti2035bas', 'config', 'fd.schema.json']);
        $service = new fd_schema_service($schemafilepath);

        $schema = $service->execute();
    }
}
