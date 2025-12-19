<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use stdClass;


class fact_edit extends dynamic_form {
    public function definition(): void {
        /** @var int $factid */
        $factid = $this->optional_param('factid', 0, PARAM_INT);
        /** @var int $factdefid */
        $factdefid = $this->optional_param('factdefid', 0, PARAM_INT);
        /** @var int $actoruntiid */
        $actoruntiid = $this->optional_param('actoruntiid', 0, PARAM_INT);
        $isupdate = (bool)$factid;
        $data = [
            'factid' => $factid,
            'factdefid' => $factdefid,
            'actoruntiid' => $actoruntiid,
            'success' => true,
        ];
        if ($isupdate) {
            $depends = new dependencies();
            $usecase = $depends->get_fact_read_use_case();
            $fact = $usecase->execute($factid);
            $data = array_merge($data, [
                'scoreraw' => $fact->result->scoreraw,
                'scoremin' => $fact->result->scoremin,
                'scoremax' => $fact->result->scoremax,
                'scoretarget' => $fact->result->scoretarget,
                'success' => $fact->result->success,
                'duration' => $fact->result->duration,
                'attemptsmax' => $fact->result->attemptsmax,
                'attemptnum' => $fact->result->attemptnum,
                'instructoruntiid' => $fact->instructoruntiid ?: '',
            ]);
        }
        $mform = $this->_form;
        $mform->addElement('hidden', 'factid');
        $mform->setType('factid', PARAM_INT);
        $mform->addElement('hidden', 'factdefid');
        $mform->setType('factdefid', PARAM_INT);
        $mform->addElement('hidden', 'actoruntiid');
        $mform->setType('actoruntiid', PARAM_INT);
        $mform->addElement('text', 'scoreraw', get_string('scoreraw', 'local_cdo_unti2035bas'));
        $mform->setType('scoreraw', PARAM_INT);
        $mform->addElement('text', 'scoremin', get_string('scoremin', 'local_cdo_unti2035bas'));
        $mform->setType('scoremin', PARAM_INT);
        $mform->addElement('text', 'scoremax', get_string('scoremax', 'local_cdo_unti2035bas'));
        $mform->setType('scoremax', PARAM_INT);
        $mform->addElement(
            'select',
            'scoretarget',
            get_string('scoretarget', 'local_cdo_unti2035bas'),
            ['max' => 'max', 'min' => 'min', 'None' => 'None'],
        );
        $mform->addElement('selectyesno', 'success', get_string('success', 'local_cdo_unti2035bas'));
        $mform->addElement('text', 'attemptsmax', get_string('attemptsmax', 'local_cdo_unti2035bas'));
        $mform->setType('attemptsmax', PARAM_INT);
        $mform->addElement('text', 'attemptnum', get_string('attemptnum', 'local_cdo_unti2035bas'));
        $mform->setType('attemptnum', PARAM_INT);
        $mform->addElement('text', 'duration', get_string('duration', 'local_cdo_unti2035bas'));
        $mform->setType('duration', PARAM_RAW);
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
        if ((int)$data->factid == 0) {
            $usecase = $depends->get_fact_create_use_case();
            $usecase->execute(
                (int)$data->factdefid,
                (int)$data->actoruntiid,
                (int)$data->scoreraw,
                (int)$data->scoremin,
                (int)$data->scoremax,
                $data->scoretarget,
                (bool)$data->success,
                (string)$data->duration,
                (int)$data->attemptsmax,
                (int)$data->attemptnum,
                (int)$data->instructoruntiid ?: null,
            );
        } else {
            $usecase = $depends->get_fact_update_use_case();
            $usecase->execute(
                (int)$data->factid,
                (int)$data->scoreraw,
                (int)$data->scoremin,
                (int)$data->scoremax,
                $data->scoretarget,
                (bool)$data->success,
                (string)$data->duration,
                (int)$data->attemptsmax,
                (int)$data->attemptnum,
                (int)$data->instructoruntiid ?: null,
            );
        }
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/facts.php');
    }
}
