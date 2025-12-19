<?php

namespace block_cdo_main\output\main;

use coding_exception;
use context_system;
use dml_exception;
use moodle_exception;
use moodle_url;
use renderer_base;
use tool_cdo_config\supported_plugins\plugins;

class renderable implements \renderable, \templatable
{
    const COMPONENT_NAME = 'block_cdo_main';
    private static array $plugin_in_use = [
        'local_cdo_certification_sheet',
        'local_cdo_education_plan',
        'local_cdo_academic_progress',
        'local_cdo_debts',
        'local_cdo_order_documents',
        'local_cdo_rpd',
    ];

    private static string $index = '/index.php';
    private string $template = 'block_cdo_main/main';
    private array $output_links;
    /**
     * @throws moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_plugin_links(): renderable
    {
        $plugins = plugins::get_instances();
        $output_plugins = [];
        foreach ($plugins->get_plugins() as $plugin) {
            if (in_array($plugin->get_namespace(), self::$plugin_in_use)
                && has_capability("local/{$plugin->get_name()}:view", context_system::instance())
            ) {

                $output_structure = [
                    'title' => get_string('pluginname', $plugin->get_namespace()),
                    'path' => new moodle_url($plugin->get_path() . self::$index),
                    'class' => 'btn btn-primary'
                ];
                $output_plugins[] = $output_structure;
            }
           // if (in_array($plugin->get_namespace(), self::$plugin_in_use)
            if (($plugin->get_namespace() === 'local_cdo_rpd')
                && has_capability("local/{$plugin->get_name()}:view_rpd", context_system::instance())
            ) {
                $output_structure = [
                    'title' => get_string('RPD_my_list', $plugin->get_namespace()),
                    'path' => new moodle_url($plugin->get_path() . '/management/my_list_rpd.php'),
                    'class' => 'btn btn-primary'
                ];
                $output_plugins[] = $output_structure;
            }
            if (($plugin->get_namespace() === 'local_cdo_rpd')
                && has_capability("local/{$plugin->get_name()}:view_admin_rpd", context_system::instance())
            ) {
                $output_structure = [
                    'title' => get_string('management', $plugin->get_namespace()),
                    'path' => new moodle_url($plugin->get_path() . '/management/admin.php'),
                    'class' => 'btn btn-primary'
                ];
                $output_plugins[] = $output_structure;
            }

        }
        $this->output_links = $output_plugins;
        return $this;
    }

    public function additional_links(): renderable
    {

        return $this;
    }

    public function additional_html(): string
    {
        global $USER;
        $html = '';
        $buttons = '<form style="padding: 0" action="https://feedback.eduprosvet.ru/" target="_blank" method="post">';
        $buttons .= '<input type="hidden" name="user_id_eos" value="' . $USER->id . '">';
        $buttons .= '<input type="hidden" name="name_eos" value="' . $USER->firstname . '" >';
        $buttons .= '<input type="hidden" name="lastname_eos" value="' . $USER->lastname . '" >';
        $buttons .= '<input type="hidden" name="email_eos" value="' . $USER->email . '" >';
        $buttons .= '<button class="btn btn-success w-100" type="submit" name="submit" value="">Задать вопрос декану</button>';
        $buttons .= '</form>';

        return $html . $buttons;
    }
    /**
     * @throws moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): array
    {
        $array['template'] = $this->template;
        #$array['additional_html'] = $this->additional_html();
        $array['additional_html'] = "";
        $array['output_plugins'] = $this->get_plugin_links()->output_links;

        return $array;
    }
}