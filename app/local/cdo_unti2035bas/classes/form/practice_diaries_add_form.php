<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use stdClass;


class practice_diaries_add_form extends dynamic_form {
    public function definition(): void {
        /** @var int */
        $streamid = $this->optional_param('streamid', 0, PARAM_INT);
        if (!$streamid) {
            throw new \InvalidArgumentException();
        }
        $depends = new dependencies();
        $usecase = $depends->get_practice_diary_read_due_students_use_case();
        $students = $usecase->execute($streamid);
        $mform = $this->_form;
        $mform->addElement('hidden', 'streamid');
        $mform->addElement(
            'select',
            'studentuntiid',
            get_string('student', 'local_cdo_unti2035bas'),
            $students
        );
        $mform->setType('streamid', PARAM_INT);
        $mform->addElement('filepicker', 'diaryfile');
        $mform->addElement('submit', 'addpracticediary', get_string('add'));

        $this->set_data([
            'streamid' => $streamid,
        ]);
    }

    protected function check_access_for_dynamic_submission(): void {
    }

    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $files
     * @return array<string, string>
     */
    public function validation($data, $files = []): array {
        $errors = [];
        return $errors;
    }

    public function process_dynamic_submission(): void {
        /** @var stdClass $data */
        $data = $this->get_data();
        $filepath = $this->save_temp_file('diaryfile');
        try {
            $depends = new dependencies();
            $usecase = $depends->get_practice_diary_create_use_case();
            $usecase->execute(
                $data->streamid,
                $data->studentuntiid,
                $filepath,
            );
        } finally {
            @unlink($filepath);
        }
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/practice_diaries.php');
    }
}
