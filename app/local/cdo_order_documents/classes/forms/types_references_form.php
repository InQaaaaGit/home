<?php

namespace local_cdo_order_documents\forms;

use local_cdo_order_documents\output\order_documents\renderable as order_documents;
use moodleform;

global $CFG;

require_once("$CFG->libdir/formslib.php");

class types_references_form extends moodleform
{
    private function get_types_references_clear(): array
    {
        $handler = new order_documents();
        $list = $handler->get_types_references();
       
        if (!empty($list['error_message'])) {
            return [];
        }
        $return_array = ['000000000' => 'Не выбрано'];
        foreach ($list as $element) {
            $return_array[$element['id']] = $element['name'];

        }
        return $return_array;
    }

    protected function definition(): void
    {
        $list = $this->get_types_references_clear();
        $mform = $this->_form;
        $mform->addElement('header', 'general', 'Выбор справки');
        $select = $mform->addElement(
            'select',
            'document_type',
            'Вид справки',
            $list,
            ['style' => 'width: 100%']
        );

        $mform->setType('document_type', PARAM_TEXT);
        $button_array = [
            $mform->createElement(
                'submit',
                'submitbutton',
                'Выбрать справку'
            //  ['style' => 'display: none']
            )
        ];
        $mform->addGroup($button_array, 'buttonar', '', ' ', false);
    }

    public function validation($data, $files): array
    {
        return [];
    }
}
