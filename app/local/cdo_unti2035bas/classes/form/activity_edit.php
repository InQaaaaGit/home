<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


class activity_edit extends dynamic_form {
    public function definition(): void {
        /** @var int */
        $id = $this->optional_param('id', 0, PARAM_INT);
        $depends = new dependencies();
        $usecase = $depends->get_activity_read_use_case();
        $activity = $usecase->execute($id);

        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement(
            'selectyesno',
            'required',
            get_string('required', 'local_cdo_unti2035bas')
        );
        $mform->addElement(
            'selectyesno',
            'collaborative',
            get_string('collaborative', 'local_cdo_unti2035bas')
        );
        $mform->addElement(
            'text',
            'lectureshours',
            get_string('lectureshours', 'local_cdo_unti2035bas')
        );
        $mform->setType('lectureshours', PARAM_FLOAT);
        $mform->addElement(
            'text',
            'workshopshours',
            get_string('workshopshours', 'local_cdo_unti2035bas')
        );
        $mform->setType('workshopshours', PARAM_FLOAT);
        $mform->addElement(
            'text',
            'independentworkhours',
            get_string('independentworkhours', 'local_cdo_unti2035bas')
        );
        $mform->setType('independentworkhours', PARAM_FLOAT);
        $mform->addElement(
            'selectyesno',
            'resultcomparability',
            get_string('resultcomparability', 'local_cdo_unti2035bas'),
        );
        if ($activity->config->admittanceform) {
            $mform->addElement(
                'select',
                'admittanceform',
                get_string('admittanceform', 'local_cdo_unti2035bas'),
                ['offline' => 'offline', 'online' => 'online', 'hybrid' => 'hybrid'],
            );
        }

        $this->set_data([
            'id' => $id,
            'required' => $activity->config->required,
            'collaborative' => $activity->config->collaborative,
            'lectureshours' => $activity->config->lectureshours,
            'workshopshours' => $activity->config->workshopshours,
            'independentworkhours' => $activity->config->independentworkhours,
            'resultcomparability' => $activity->config->resultcomparability,
            'admittanceform' => $activity->config->admittanceform,
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
        /** @var \stdClass $data */
        $data = $this->get_data();

        $depends = new dependencies();
        $usecase = $depends->get_activity_update_use_case();
        $usecase->execute(
            (int)$data->id,
            (bool)$data->required,
            (bool)$data->collaborative,
            (float)$data->lectureshours,
            (float)$data->workshopshours,
            (float)$data->independentworkhours,
            (int)$data->resultcomparability,
            $data->admittanceform ?? null,
        );
        return true;
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/details.php');
    }
}
