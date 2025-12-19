<?php

namespace local_cdo_certification_sheet\services;

defined('MOODLE_INTERNAL') || die();

interface i_sheet_component {

	public function build_info(): string;
}