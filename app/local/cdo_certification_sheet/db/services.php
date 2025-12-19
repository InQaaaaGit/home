<?php

use local_cdo_certification_sheet\external\sheet_api;

defined('MOODLE_INTERNAL') || die();


$functions = [
	'insert_grade' => [
		'classname' => sheet_api::class,
		'methodname' => 'update_grade',
		'description' => 'Update student grade',
		'type' => 'read',
		'ajax' => true,
	],
	'commission_agreed' => [
		'classname' => sheet_api::class,
		'methodname' => 'commission_agreed',
		'description' => 'Change agreed student grade',
		'type' => 'read',
		'ajax' => true,
	],
	'close_sheet' => [
		'classname' => sheet_api::class,
		'methodname' => 'close_sheet',
		'description' => 'Close close_sheet',
		'type' => 'read',
		'ajax' => true,
	],
    'get_list_sheet' => [
		'classname' => sheet_api::class,
		'methodname' => 'get_list_sheet',
		'description' => 'get_list_sheet',
		'type' => 'read',
		'ajax' => true,
	],
];