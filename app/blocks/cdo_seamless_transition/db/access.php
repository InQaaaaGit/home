<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
	'block/cdo_seamless_transition:myaddinstance' => [
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
		'clonepermissionsfrom' => 'moodle/my:manageblocks'
	],

	'block/cdo_seamless_transition:addinstance' => [
		'riskbitmask' => RISK_SPAM | RISK_XSS,
		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
	]
];
