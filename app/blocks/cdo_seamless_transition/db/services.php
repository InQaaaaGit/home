<?php

use block_cdo_seamless_transition\external\transition_api;

defined('MOODLE_INTERNAL') || die();


$functions = [
	'get_seamless_transition' => [
		'classname' => transition_api::class,
		'methodname' => 'get_transition',
		'description' => 'Get transition path',
		'type' => 'read',
		'ajax' => true,
	]
];