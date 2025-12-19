<?php

defined('MOODLE_INTERNAL') || die();

/**
 * @param global_navigation $nav
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 */
function local_cdo_certification_sheet_extend_navigation(global_navigation $nav): void {

	/*if(!has_capability("local/cdo_certification_sheet:view", context_system::instance())) {
		return;
	}

	$node = navigation_node::create(
		get_string('menu_item', 'local_cdo_certification_sheet'),
		new moodle_url('/local/cdo_certification_sheet/pages/list.php'),
		navigation_node::TYPE_CONTAINER,
		'shortname',
		'graduates',
		// new pix_icon('i/table', 'test', 'local_cdo_certification_sheet')
        new pix_icon('i/folder', get_string('alt_pix_navigation', 'local_cdo_debts'))
	);

	$node->showinflatnavigation = true;
	$nav->add_node($node, 'mycourses');*/
}


