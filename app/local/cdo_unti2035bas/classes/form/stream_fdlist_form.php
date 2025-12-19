<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use stdClass;


class stream_fdlist_form extends dynamic_form {
    public function definition(): void {
        /** @var int $streamid */
        $streamid = $this->optional_param('streamid', 0, PARAM_INT);
        $mform = $this->_form;
        $mform->addElement('hidden', 'streamid');
        $mform->setType('streamid', PARAM_INT);
        $depends = new dependencies();
        $usecase = $depends->get_stream_fd_unused_use_case();
        [$resultexts, $contextexts] = $usecase->execute($streamid);
        $extensions = [];
        $fdcontextextensionstr = get_string('fdcontextextension', 'local_cdo_unti2035bas');
        $fdresultextensionstr = get_string('fdresultextension', 'local_cdo_unti2035bas');
        foreach ($contextexts as $ext) {
            $extensions[$ext->name] = "{$ext->description} [{$ext->name}] [{$fdcontextextensionstr}] {{$ext->type}}";
        }
        foreach ($resultexts as $ext) {
            $extensions[$ext->name] = "{$ext->description} [{$ext->name}] [{$fdresultextensionstr}] {{$ext->schemaref}}";
        }
        $mform->addElement(
            'autocomplete',
            'extensionname',
            get_string('fdextension', 'local_cdo_unti2035bas'),
            $extensions,
            null,
            null,
        );
        $mform->addElement('submit', 'addextension', get_string('add'));
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
        if (!$data->streamid || !$data->extensionname) {
            return;
        }
        $depends = new dependencies();
        $usecase = $depends->get_stream_fd_add_use_case();
        $usecase->execute($data->streamid, $data->extensionname);
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/stream_fdlist.php');
    }
}
