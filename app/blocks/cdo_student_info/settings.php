<?php

defined('MOODLE_INTERNAL') || die;
$block = 'block_cdo_student_info';
if ($ADMIN->fulltree) {

    $settings->add(
        new admin_setting_configtext(
            'block_cdo_student_info_find_mask',
            get_string('block_cdo_student_info_find_mask', $block),
            get_string('block_cdo_student_info_find_mask_description', $block),
            ''
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            $block . '/use_orders',
            get_string('block_cdo_student_info_use_orders', $block),
            get_string('block_cdo_student_info_use_orders_description', $block),
            false
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            $block . '/use_diplomas',
            get_string('block_cdo_student_info_use_diplomas', $block),
            get_string('block_cdo_student_info_use_diplomas_description', $block),
            false
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            $block . '/use_checklist',
            get_string('block_cdo_student_info_use_checklist', $block),
            get_string('block_cdo_student_info_use_checklist_description', $block),
            false
        )
    );
}

