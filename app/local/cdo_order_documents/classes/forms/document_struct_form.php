<?php

namespace local_cdo_order_documents\forms;

global $CFG;

use local_cdo_order_documents\output\order_documents\renderable;
use moodleform;

require_once("$CFG->libdir/formslib.php");

class document_struct_form extends moodleform
{

    protected function definition(): void
    {
        $mform = $this->_form;

        $handler = new renderable();

        $document_type = $this->_customdata['document_type'];

        $structure = $handler->get_document_structure($document_type);
        //TODO - error_message define
        $mform->addElement('hidden', 'document_type', $document_type);
        $mform->setType('choose_document_type', PARAM_TEXT);
        $mform->addElement('html', '<h1 style="font-size: 36px; font-weight: bold; color: #333; margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #007bff;">' . $structure['name'] . '</h1>');

        $mform->setType('document_type', PARAM_TEXT);

        foreach ($structure['fields'] as $field) {
            switch ($field['type']) {
                case 'Bool':

                    $mform->addElement('checkbox', $field['name'], $field['description']);
                    $mform->setType($field['name'], PARAM_BOOL);
                    $mform->setDefault($field['name'], false);
                    break;
                case 'String':
                    $mform->addElement('text', $field['name'], $field['description'], ['size' => 75]);
                    $mform->setType($field['name'], PARAM_TEXT);
                    if ($field['value']) {
                        $mform->setDefault($field['name'], $field['value']);
                    }
                    break;
                case 'List':
                    $options = [];
                    foreach ($field['list_options'] as $list_option) {
                        $options[$list_option['id']] = $list_option['description'];
                    }
                    $mform->addElement('select', $field['name'], $field['description'], $options);
                    $mform->setDefault('userdata', 0);
                  //  $mform->addRule($field['name'], 'required', null, get_string('required'));


                    break;
                case 'DescriptionField':
                    $mform->addElement('html', $field['description']);
                    break;
                default:

                    break;
            }
        }
        $this->add_action_buttons(true, get_string('sending_button', 'local_cdo_order_documents'));
    }
}
