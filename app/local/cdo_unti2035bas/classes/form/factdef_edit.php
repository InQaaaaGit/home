<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use stdClass;


class factdef_edit extends dynamic_form {
    public function definition(): void {
        /** @var int $factdefid */
        $factdefid = $this->optional_param('factdefid', 0, PARAM_INT);
        $depends = new dependencies();
        $usecase = $depends->get_factdef_read_use_case();
        $factdef = $usecase->execute($factdefid);
        $data = [
            'factdefid' => $factdef->id,
            'instructoruntiid' => $factdef->instructoruntiid ?: '',
        ];
        $mform = $this->_form;
        $mform->addElement('hidden', 'factdefid');
        $mform->setType('factdefid', PARAM_INT);
        $mform->addElement('text', 'instructoruntiid', get_string('instructoruntiid', 'local_cdo_unti2035bas'));
        $mform->setType('instructoruntiid', PARAM_INT);
        $this->set_data($data);
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
        $depends = new dependencies();
        $usecase = $depends->get_factdef_update_use_case();
        $usecase->execute(
            (int)$data->factdefid,
            (int)$data->instructoruntiid ?: null,
        );
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/details.php');
    }
}
