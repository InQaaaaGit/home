<?php

use tool_cdo_config\roles\role_factory;

defined('MOODLE_INTERNAL') || die;

function xmldb_tool_cdo_config_install(): bool {
	$role_factory = new role_factory();
	$role_factory->create_roles();
	return true;
}