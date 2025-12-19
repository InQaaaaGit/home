<?php

defined('MOODLE_INTERNAL') || die();

function local_cdo_education_plan_extend_navigation(global_navigation $navigation)
{
    global $CFG, $PAGE;

      if (!has_capability("local/cdo_education_plan:view", context_system::instance())) {
          return;
      }

    $node = navigation_node::create(
        get_string('pluginname', 'local_cdo_education_plan'),
        new moodle_url('/local/cdo_education_plan/education_plan.php'),
        navigation_node::TYPE_CONTAINER,
        'shortname',
        'cdo_education_plan',
        new pix_icon('i/folder', get_string('alt_pix_navigation', 'local_cdo_education_plan'))
    );

    $node->showinflatnavigation = true;
    $navigation->add_node($node, 'myprofile');


}

function local_cdo_education_plan_get_fontawesome_icon_map(): array
{
    return [
        'local_cdo_education_plan:i/table' => 'fa-file-spreadsheet'
    ];
}