<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details
 *
 * @package local_cdo_certification_sheet
 * @author  Sergey Nechaev CDO
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2025092901;
$plugin->requires = 2019111200;
$plugin->component = 'local_cdo_certification_sheet';
$plugin->dependencies = [
	"tool_cdo_config" => 20220512.01
];
$plugin->release = 'v1.0';
