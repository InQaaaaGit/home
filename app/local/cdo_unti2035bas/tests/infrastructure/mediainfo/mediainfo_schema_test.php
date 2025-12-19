<?php
namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\infrastructure\mediainfo\mediainfo_schema;


final class mediainfo_schema_test extends \advanced_testcase {
    /**
     * @return array<string, mixed>
     */
    public function get_sample() {
        global $CFG;
        return json_decode(
            file_get_contents(join(
                DIRECTORY_SEPARATOR,
                [
                    $CFG->dirroot,
                    'local',
                    'cdo_unti2035bas',
                    'tests',
                    'infrastructure',
                    'mediainfo',
                    'sample.json',
                ]
            )),
            true,
        );
    }

    public function test_mediainfo_schema(): void {
        $this->expectNotToPerformAssertions();
        $info = mediainfo_schema::validate($this->get_sample());
    }
}
