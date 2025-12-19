<?php

defined('MOODLE_INTERNAL') || die();

function local_cdo_mto_extend_navigation(global_navigation $navigation)
{
    global $CFG, $PAGE;

      if (!has_capability("local/cdo_mto:view", context_system::instance())) {
          return;
      }

    $node = navigation_node::create(
        get_string('pluginname', 'local_cdo_mto'),
        new moodle_url('/local/cdo_mto/mto.php'),
        navigation_node::TYPE_CONTAINER,
        'shortname',
        'cdo_mto',
        new pix_icon('i/folder', '')
    );

    $node->showinflatnavigation = true;
    $navigation->add_node($node, 'myprofile');


}

function local_cdo_mto_get_fontawesome_icon_map(): array
{
    return [
        'local_cdo_mto:i/table' => 'fa-file-spreadsheet'
    ];
}
