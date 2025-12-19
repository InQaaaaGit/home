<?php
namespace local_cdo_unti2035bas\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use stdClass;


class stream_edit extends dynamic_form {
    public function definition(): void {
        /** @var int $id */
        $id = $this->optional_param('id', 0, PARAM_INT);
        $isupdate = (bool)$id;
        if (!$isupdate) {
            $data = [
                'id' => 0,
                'courseid' => [1],
                // 'groupid' => [0],
                'comment' => 'New comment',
                'isonline' => true,
                'academichourminutes' => 45,
            ];
        } else {
            $depends = new dependencies();
            $usecase = $depends->get_stream_read_use_case();
            $stream = $usecase->execute($id);
            $data = [
                'id' => $id,
                'courseid' => [$stream->moodle->courseid],
                'groupid' => [$stream->moodle->groupid],
                'untiprogramid' => $stream->unti->programid,
                'untiflowid' => $stream->unti->flowid,
                'untimethodistid' => $stream->unti->methodistid,
                'isonline' => $stream->isonline,
                'academichourminutes' => $stream->academichourminutes,
                'comment' => $stream->comment,
            ];
        }
        $courseid = $data['courseid'][0];
        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        if (!$isupdate) {
            $mform->addElement('course', 'courseid', get_string('course'));
            $groups = array_map(fn($g) => $g->name, groups_get_all_groups($courseid));
            $mform->addElement(
                'autocomplete',
                'groupid',
                get_string('group'),
                $groups,
                ['ajax' => 'local_cdo_unti2035bas/local/stream_group_selector'],
                $isupdate ? ['readonly' => 'readonly'] : null,
            );
        }
        $mform->addElement(
            'text',
            'untiprogramid',
            get_string('untiprogramid', 'local_cdo_unti2035bas'),
            $isupdate ? ['readonly' => 'readonly'] : null,
        );
        $mform->setType('untiprogramid', PARAM_INT);
        $mform->addElement(
            'text',
            'untiflowid',
            get_string('untiflowid', 'local_cdo_unti2035bas'),
            $isupdate ? ['readonly' => 'readonly'] : null,
        );
        $mform->setType('untiflowid', PARAM_INT);
        $mform->addElement(
            'text',
            'untimethodistid',
            get_string('untimethodistid', 'local_cdo_unti2035bas'),
            $isupdate ? ['readonly' => 'readonly'] : null,
        );
        $mform->setType('untimethodistid', PARAM_INT);
        $mform->addElement(
            'text',
            'academichourminutes',
            get_string('academichourminutes', 'local_cdo_unti2035bas'),
        );
        $mform->setType('academichourminutes', PARAM_INT);
        $mform->addElement('text', 'comment', get_string('streamcomment', 'local_cdo_unti2035bas'));
        $mform->setType('comment', PARAM_TEXT);
        $mform->addElement(
            'selectyesno',
            'isonline',
            get_string('isonline', 'local_cdo_unti2035bas')
        );
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

    public function process_dynamic_submission(): bool {
        /** @var stdClass $data */
        $data = $this->get_data();

        $depends = new dependencies();
        if (!$data->id) {
            $usecase = $depends->get_stream_create_use_case();
            $usecase->execute(
                (int)$data->courseid,
                (int)$data->groupid,
                (int)$data->untiprogramid,
                (int)$data->untiflowid,
                (int)$data->untimethodistid,
                (int)$data->academichourminutes,
                (bool)$data->isonline,
                $data->comment,
            );
        } else {
            $usecase = $depends->get_stream_update_use_case();
            $usecase->execute(
                (int)$data->id,
                (int)$data->academichourminutes,
                (bool)$data->isonline,
                $data->comment,
            );
        }

        return true;
    }

    public function set_data_for_dynamic_submission(): void {
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/cdo_unti2035bas/streams.php');
    }
}
