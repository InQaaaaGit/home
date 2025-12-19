<?php
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configcheckbox(
            'block_cdo_schedule_use_sub_groups',
            get_string('block_cdo_schedule_use_sub_groups', 'block_cdo_schedule'),
            get_string('block_cdo_schedule_use_sub_groups_description', 'block_cdo_schedule'),
            1,
            PARAM_BOOL
        )
    );
}