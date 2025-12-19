<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


class factdef_add_extension_form extends dynamic_form {
    use add_extension_trait;

    /** @var array<fd_result_extension_schema_vo> $resultexts */
    private array $resultexts;
    /** @var array<fd_context_extension_schema_vo> $contextexts */
    private array $contextexts;

    public function definition(): void {
        /** @var int $factdefid */
        $factdefid = $this->optional_param('factdefid', 0, PARAM_INT);
        $mform = $this->_form;
        $mform->addElement('hidden', 'factdefid');
        $mform->setType('factdefid', PARAM_INT);
        $depends = new dependencies();
        $usecase = $depends->get_factdef_get_unused_extensions_use_case();
        [$this->resultexts, $this->contextexts] = $usecase->execute($factdefid);
        $this->add_elements();
        $this->set_data([
            'factdefid' => $factdefid,
            'notapplicable' => false,
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
        $data = (array)$this->get_data();
        if (!$factdefid = $data['factdefid'] ?? null) {
            return;
        }
        if (!$extensionname = $data['extensionname'] ?? null) {
            return;
        }
        $unit = array_values(array_filter($data, fn($k) => strpos($k, 'unit') === 0, ARRAY_FILTER_USE_KEY))[0] ?? null;
        $resultsel = array_values(array_filter($data, fn($k) => strpos($k, 'resultsel') === 0, ARRAY_FILTER_USE_KEY))[0] ?? null;
        $depends = new dependencies();
        $usecase = $depends->get_factdef_extension_add_use_case();
        $usecase->execute(
            (int)$factdefid,
            $extensionname,
            (bool)$data['notapplicable'] ?? false,
            $data['textvalue'] ?? null,
            $data['score'] ?? null,
            $unit,
            $resultsel,
            $data['min'] ?? null ?: null,
            $data['max'] ?? null ?: null,
        );
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/factdef_edit.php');
    }
}
