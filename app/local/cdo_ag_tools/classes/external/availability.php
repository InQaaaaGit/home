<?php

namespace local_cdo_ag_tools\external;

use core\exception\coding_exception;
use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_user;
use core\course\external\course_summary_exporter;
use core_user_external;
use dml_exception;

use local_cdo_ag_tools\controllers\availability_controller;
use moodle_exception;

require_once(__DIR__ . '/../../../../user/externallib.php');
require_once($CFG->libdir . '/externallib.php');

class availability extends core_user_external
{

    /**
     * Returns a list of users matching the given full name.
     *
     * @param array $criteria
     * @return array
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_users($criteria = array()): array
    {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::get_users_parameters(),
            array('criteria' => $criteria));

        // Validate the criteria and retrieve the users.
        $users = array();
        $warnings = array();
        $sqlparams = array();
        $usedkeys = array();

        // Do not retrieve deleted users.
        $sql = ' deleted = 0';

        foreach ($params['criteria'] as $criteriaindex => $criteria) {

            // Check that the criteria has never been used.
            if (array_key_exists($criteria['key'], $usedkeys)) {
                throw new moodle_exception('keyalreadyset', '', '', null, 'The key ' . $criteria['key'] . ' can only be sent once');
            } else {
                $usedkeys[$criteria['key']] = true;
            }

            $invalidcriteria = false;
            // Clean the parameters.
            $paramtype = PARAM_RAW;
            switch ($criteria['key']) {
                case 'id':
                    $paramtype = core_user::get_property_type('id');
                    break;
                case 'idnumber':
                    $paramtype = core_user::get_property_type('idnumber');
                    break;
                case 'username':
                    $paramtype = core_user::get_property_type('username');
                    break;
                case 'email':
                    // We use PARAM_RAW to allow searches with %.
                    $paramtype = core_user::get_property_type('email');
                    break;
                case 'auth':
                    $paramtype = core_user::get_property_type('auth');
                    break;
                case 'lastname':
                case 'firstname':
                    $paramtype = core_user::get_property_type('firstname');
                    break;
                default:
                    // Send back a warning that this search key is not supported in this version.
                    // This warning will make the function extandable without breaking clients.
                    $warnings[] = array(
                        'item' => $criteria['key'],
                        'warningcode' => 'invalidfieldparameter',
                        'message' =>
                            'The search key \'' . $criteria['key'] . '\' is not supported, look at the web service documentation'
                    );
                    // Do not add this invalid criteria to the created SQL request.
                    $invalidcriteria = true;
                    unset($params['criteria'][$criteriaindex]);
                    break;
            }

            if (!$invalidcriteria) {
                $cleanedvalue = clean_param($criteria['value'], $paramtype);

                $sql .= ' AND ';

                // Create the SQL.
                switch ($criteria['key']) {
                    case 'id':
                    case 'idnumber':
                    case 'username':
                    case 'auth':
                        $sql .= $criteria['key'] . ' = :' . $criteria['key'];
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    case 'email':
                    case 'lastname':
                    case 'firstname':
                        $sql .= $DB->sql_like($criteria['key'], ':' . $criteria['key'], false);
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    default:
                        break;
                }
            }
        }

        $users = $DB->get_records_select('user', $sql, $sqlparams, 'id ASC');

        // Finally retrieve each users information.
        $returnedusers = array();
        foreach ($users as $user) {

            $validuser = true;

            // foreach ($params['criteria'] as $criteria) {
            if (empty((array)$user->auth)) {
                $validuser = false;
            }
            //   }

            if ($validuser) {
                $user->fullname = fullname($user);
                $returnedusers[] = (array)$user;
            }

        }

        return array('users' => $returnedusers, 'warnings' => $warnings);
    }

    public static function get_users_parameters(): external_function_parameters
    {
        return parent::get_users_parameters();
    }

    public static function get_users_returns(): external_description|external_single_structure
    {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(core_user::get_property_type('id'), 'ID of the user'),
                            'username' => new external_value(core_user::get_property_type('username'), 'The username', VALUE_OPTIONAL),
                            'firstname' => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user', VALUE_OPTIONAL),
                            'lastname' => new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),
                            'fullname' => new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),

                        ]
                    )
                )
            )
        );
    }

    public static function set_availability_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_INT, 'user id'),
                'quarter_start' => new external_value(PARAM_TEXT, 'start quarter', VALUE_DEFAULT, ''),
                'quarter_end' => new external_value(PARAM_TEXT, 'end quarter', VALUE_DEFAULT, ''),
                'grant_access' => new external_value(PARAM_BOOL, 'Grant access or revoke it', VALUE_DEFAULT, true),
            ]
        );
    }

    /**
     * @param int $user_id
     * @param string $quarter_start
     * @param string $quarter_end
     * @param bool $grant_access
     * @return array
     * @throws \coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function set_availability(int $user_id, string $quarter_start, string $quarter_end, bool $grant_access = true): array
    {
        global $DB, $USER;
        $result = [];
        $log = '';
        $user = core_user::get_user($user_id);

        if (!$user) {
            return ['error' => "User with ID $user_id not found"];
        }
        $user_email = $user->email;

        $my_courses = enrol_get_users_courses($user_id);

        foreach ($my_courses as $course) {
            $log .= 'course_id: ' . $course->id . PHP_EOL;
            $moduleinfo = get_fast_modinfo($course);
         //  $sections = $moduleinfo->get_sections();
            $sections = $moduleinfo->get_section_info_all();
            $quarter_start_id = null;
            $quarter_end_id = null;
            $sections_to_update = [];

            foreach ($sections as $index => $sectionid) {
                $section_info = $sectionid;
                $section_name = $section_info->name ?? '';
                $condition_same_name = trim(mb_strtolower($section_name)) === trim(mb_strtolower($quarter_start));
                $condition_same_name_end = trim(mb_strtolower($section_name)) === trim(mb_strtolower($quarter_end));

                if ($condition_same_name !== false) {
                    $quarter_start_id = $index;
                }
                if ($condition_same_name_end !== false) {
                    $quarter_end_id = $index;
                }
            }

            if ($quarter_start !== '' && $quarter_end !== '') {
                if ($quarter_start_id !== null && $quarter_end_id !== null) {
                    $start = min($quarter_start_id, $quarter_end_id);
                    $end = max($quarter_start_id, $quarter_end_id);

                    for ($i = $start; $i < $end; $i++) {
                        if (isset($sections[$i])) {
                            $sections_to_update[] = $moduleinfo->get_section_info($i)->id;
                        }
                    }
                } elseif ($quarter_start_id !== null || $quarter_end_id !== null) {
                    if ($quarter_start_id !== null) {
                        $sections_to_update[] = $moduleinfo->get_section_info($quarter_start_id)->id;
                    }
                    if ($quarter_end_id !== null) {
                        $sections_to_update[] = $moduleinfo->get_section_info($quarter_end_id)->id;
                    }
                }
            } elseif ($quarter_start !== '' && $quarter_end === '') {
                if ($quarter_start_id !== null) {
                    $start = $quarter_start_id;
                    $end = count($sections) - 1;
                    for ($i = $start; $i <= $end; $i++) {
                        if (isset($sections[$i])) {
                            $sections_to_update[] = $sections[$i]->id;
                        }
                    }
                }
            } elseif ($quarter_start === '' && $quarter_end === '') {
                foreach ($sections as $index => $section_id) {
                    $sections_to_update[] = $section_id->id;
                }
            }
            if (!empty($sections_to_update)) {
                availability_controller::set_section_availability($sections_to_update, $user_email, null, null, !$grant_access);
                foreach ($sections_to_update as $section) {
                    $log .= 'назначены доступности для: ' . $section . PHP_EOL;
                }

            }

            rebuild_course_cache($course->id, true);
        }
        return ['status' => 'ok', 'message' => $log];
    }

    public static function set_availability_returns(): external_description
    {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Operation status'),
            ]
        );
    }
}
