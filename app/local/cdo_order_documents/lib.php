<?php

defined('MOODLE_INTERNAL') || die();

function local_cdo_order_documents_extend_navigation(global_navigation $navigation)
{
    global $CFG, $PAGE;

      if (!has_capability("local/cdo_order_documents:view", context_system::instance())) {
          return;
      }

    $node = navigation_node::create(
        get_string('pluginname', 'local_cdo_order_documents'),
        new moodle_url('/local/cdo_order_documents/index.php'),
        navigation_node::TYPE_CONTAINER,
        'shortname',
        'cdo_order_documents',
        new pix_icon('i/folder', get_string('alt_pix_navigation', 'local_cdo_order_documents'))
    );

    $node->showinflatnavigation = true;
    $navigation->add_node($node, 'myprofile');


}

function local_cdo_order_documents_get_fontawesome_icon_map(): array
{
    return [
        'local_cdo_order_documents:i/table' => 'fa-file-spreadsheet'
    ];
}
