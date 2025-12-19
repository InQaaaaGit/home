<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


class module_edit extends dynamic_form {
    public function definition(): void {
        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement(
            'text',
            'moduleuntiid',
            get_string('untimoduleid', 'local_cdo_unti2035bas')
        );
        $mform->setType('untimoduleid', PARAM_INT);
        /** @var int */
        $id = $this->optional_param('id', 0, PARAM_INT);
        $depends = new dependencies();
        $usecase = $depends->get_module_read_use_case();
        $module = $usecase->execute($id);

        $this->set_data([
            'id' => $id,
            'moduleuntiid' => $module->unti->moduleid,
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

    public function process_dynamic_submission(): bool {
        $data = $this->get_data();

        $depends = new dependencies();
        $usecase = $depends->get_module_update_use_case();
        $usecase->execute(
            (int)$data->id,
            (int)$data->moduleuntiid,
        );

        return true;
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/details.php');
    }
}
