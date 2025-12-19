<?php

global $CFG;

use tool_cdo_config\di;

defined('MOODLE_INTERNAL') || die();

di::get_instance()->get_settings()->settings_builder();

