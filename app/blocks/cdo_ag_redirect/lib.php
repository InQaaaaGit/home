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
 * AG Redirect block library functions.
 *
 * @package    block_cdo_ag_redirect
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get user's Bitrix lead ID.
 *
 * @param int $userid User ID
 * @return string|null Bitrix lead ID or null if not found
 */
function block_cdo_ag_redirect_get_bitrix_lead_id($userid) {
    global $DB;
    
    // Try to get from user profile field first
    $sql = "SELECT ud.data 
            FROM {user_info_data} ud 
            JOIN {user_info_field} uf ON ud.fieldid = uf.id 
            WHERE ud.userid = :userid AND uf.shortname = 'bitrix_lead_id'";
    
    $record = $DB->get_record_sql($sql, ['userid' => $userid]);
    if ($record) {
        return $record->data;
    }
    
    // Try to get from user object property
    $user = $DB->get_record('user', ['id' => $userid], 'id, bitrix_lead_id');
    if ($user && !empty($user->bitrix_lead_id)) {
        return $user->bitrix_lead_id;
    }
    
    return null;
}

/**
 * Check if user needs to complete registration.
 *
 * @param int $userid User ID
 * @return bool True if user needs to complete registration
 */
function block_cdo_ag_redirect_needs_registration($userid) {
    $bitrix_id = block_cdo_ag_redirect_get_bitrix_lead_id($userid);
    return !empty($bitrix_id);
}

/**
 * Get redirect URL for user.
 *
 * @param int $userid User ID
 * @return string|null Redirect URL or null if no bitrix_id
 */
function block_cdo_ag_redirect_get_url($userid) {
    $bitrix_id = block_cdo_ag_redirect_get_bitrix_lead_id($userid);
    
    if (empty($bitrix_id)) {
        return null;
    }
    
    return "http://lkreg.academ-school.ru/register?bx_lead_id={$bitrix_id}&utm_medium=social&utm_source=facebook&utm_campaign=summer_promo&utm_content=ad1&utm_term=online_courses";
} 