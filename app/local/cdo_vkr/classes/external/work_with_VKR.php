<?php

namespace local_cdo_vkr\external;

use coding_exception;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_cdo_vkr\utility\external_return_types;
use local_cdo_vkr\utility\file_of_vkr;
use local_cdo_vkr\VKR\create_complex_pdf;
use local_cdo_vkr\VKR\layer;
use local_cdo_vkr\VKR\main;
use local_cdo_vkr\VKR\main_interface;
use local_cdo_vkr\VKR\vkr_ebs;
use Matrix\Exception;
use moodle_exception;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use function local_cdo_vkr\utility\external_return_types;


class work_with_VKR extends external_api
{
    public static function change_status_of_vkr_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id vkr', VALUE_REQUIRED),
                'status_id' => new external_value(PARAM_INT, 'new status id', VALUE_REQUIRED),
                'acquainted' => new external_value(PARAM_BOOL, 'acquainted', VALUE_DEFAULT, false),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function change_status_of_vkr($id, $status, $acquainted = false): bool
    {
        global $CFG;
        $params = self::validate_parameters(self::change_status_of_vkr_parameters(),
            [
                'id' => $id,
                'status_id' => $status,
                'acquainted' => $acquainted,
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        return $l->change_status_of_vkr($params);
    }

    public static function change_status_of_vkr_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'new status id', VALUE_REQUIRED);
    }

    public static function get_vkrs_by_user_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'mode_gek' => new external_value(PARAM_BOOL, 'new status id', VALUE_DEFAULT, false)
            ],
            '',
            VALUE_OPTIONAL,
            []
        );
    }

    /**
     * @throws dml_exception
     */
    public static function get_vkrs_by_user($mode_gek): array
    {
        global $USER, $CFG;
        $params = self::validate_parameters(self::get_vkrs_by_user_parameters(),
            [
                'mode_gek' => $mode_gek,
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        $user_id = $USER->id;
        if ($USER->id == '3') {
            $user_id = 4463;
            $user_id = 5194;
        }
        return $l->get_list_of_VKR(['user_id' => $user_id, 'mode_gek' => $params['mode_gek']]);
        #return $l->get_list_of_VKR(['user_id' => 4463]); //TODO
    }

    public static function get_vkrs_by_user_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                    'timecreated' => new external_value(PARAM_INT, 'timecreated', VALUE_REQUIRED),
                    'user_id' => new external_value(PARAM_INT, 'user_id', VALUE_REQUIRED),
                    'FIO' => new external_value(PARAM_TEXT, 'fio', VALUE_REQUIRED),
                    'name_of_vkr' => new external_value(PARAM_TEXT, 'name_of_vkr', VALUE_REQUIRED),
                    'manager' => new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'id', VALUE_REQUIRED),
                        'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                        'info' => new external_value(PARAM_TEXT, 'info', VALUE_OPTIONAL),
                    ]),
                    'years' => new external_value(PARAM_TEXT, 'years', VALUE_REQUIRED),
                    'edu_group' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'edu_division' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'edu_level' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'edu_form' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'edu_speciality' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'grade' => new external_value(PARAM_TEXT, 'years', VALUE_DEFAULT, ''),
                    'agreedEBS' => new external_value(PARAM_BOOL, 'agreedebs', VALUE_DEFAULT, false),
                    'acquainted' => new external_value(PARAM_BOOL, 'acquainted', VALUE_DEFAULT, false),
                    'admitted' => new external_value(PARAM_BOOL, 'admitted', VALUE_DEFAULT, false),
                    'isGEK' => new external_value(PARAM_BOOL, 'isGEK', VALUE_DEFAULT, false),
                    'status' => new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'id', VALUE_REQUIRED),
                        'changed' => new external_value(PARAM_BOOL, 'name', VALUE_REQUIRED),
                    ]),
                ]
            )
        );
    }

    public static function get_vkr_info_by_student_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            []
        );
    }

    public static function get_vkr_info_by_student(): array
    {
        global $CFG, $USER;
        $l = new layer(new $CFG->use_class_integration());
        $user_id = $USER->id;
        if ($USER->id == '3') {
            $user_id = 1431;
        }

        $production = true;
        if ($production) {
            require_once $CFG->dirroot . '/blocks/buttons/lib.php';
            $class = new \general_function();
            $type = $class->getCurrentType();
            $grade_book = $class->getCurrentGB()->name;
            if ($USER->id == '3') {
                $grade_book = '43055119002';
            }
        } else {
            $grade_book = '';
            $type = 'student';
        }

        return $l->get_list_of_VKR(['user_id' => $user_id, 'type' => $type->type, 'grade_book' => $grade_book]);
    }

    public static function get_vkr_info_by_student_returns(): external_multiple_structure
    {
        return self::get_vkrs_by_user_returns();
    }

    public static function accept_EBS_agreed_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'agreedebs' => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 1), //static, because use only fo agreed and never again

            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function accept_EBS_agreed(string $id_vkr): bool
    {
        global $CFG;
        $params = self::validate_parameters(self::accept_EBS_agreed_parameters(),
            [
                'id' => $id_vkr,
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        return $l->change_status_of_vkr($params);
    }

    public static function accept_EBS_agreed_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function change_manager_status_of_vkr_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'status_changed' => new external_value(PARAM_INT, 'status_changed', VALUE_DEFAULT, 1),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function change_manager_status_of_vkr(string $id_vkr, $status = 1): bool
    {
        global $CFG;
        $params = self::validate_parameters(self::change_manager_status_of_vkr_parameters(),
            [
                'id' => $id_vkr,
                'status_changed' => $status
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        return $l->change_status_of_vkr($params);
    }

    public static function change_manager_status_of_vkr_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function set_acquainted_to_vkr_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'acquainted' => new external_value(PARAM_BOOL, 'acquainted', VALUE_DEFAULT, true),
                'status_id' => new external_value(PARAM_INT, 'status_id', VALUE_DEFAULT, 5),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function set_acquainted_to_vkr($id): bool
    {
        global $CFG;
        $params = self::validate_parameters(self::set_acquainted_to_vkr_parameters(),
            [
                'id' => $id
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        return $l->change_status_of_vkr($params);
    }

    public static function set_acquainted_to_vkr_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function activate_process_placing_vkr_into_ebs_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'data' => new external_single_structure(
                    [
                        'id_vkr' => new external_value(PARAM_TEXT, 'id vkr GUID', VALUE_REQUIRED),
                        'edu_division' => new external_value(PARAM_TEXT, 'edu_division', VALUE_REQUIRED),
                        'theme_name' => new external_value(PARAM_TEXT, 'edu_division', VALUE_REQUIRED),
                        'fio' => new external_value(PARAM_TEXT, 'edu_division', VALUE_REQUIRED),
                        'edu_specialization' => new external_value(PARAM_TEXT, 'edu_division', VALUE_REQUIRED),
                        'fio_manager' => new external_value(PARAM_TEXT, 'fio_manager', VALUE_REQUIRED),
                        'year' => new external_value(PARAM_TEXT, 'year', VALUE_REQUIRED),
                        'edu_group' => new external_value(PARAM_TEXT, 'edu_group', VALUE_REQUIRED),
                        'student_course' => new external_value(PARAM_TEXT, '$student_course', VALUE_REQUIRED),
                        'edu_profile' => new external_value(PARAM_TEXT, '$edu_profile', VALUE_REQUIRED),
                        'edu_specialization_code' => new external_value(PARAM_TEXT, '$edu_specialization_code', VALUE_REQUIRED),
                        'number_document' => new external_value(PARAM_TEXT, 'number_document', VALUE_REQUIRED),
                        'edu_lectern' => new external_value(PARAM_TEXT, 'edu_lectern', VALUE_OPTIONAL),
                        'edu_form' => new external_value(PARAM_TEXT, 'edu_form', VALUE_OPTIONAL),
                        'edu_level' => new external_value(PARAM_TEXT, 'edu_form', VALUE_OPTIONAL),
                        'reviewer' => new external_value(PARAM_TEXT, 'reviewer', VALUE_OPTIONAL),
                    ],
                    '',
                    VALUE_REQUIRED,
                    ''
                )
            ]
        );
    }

    /**
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function activate_process_placing_vkr_into_ebs($data): array
    {
        $params = self::validate_parameters(
            self::activate_process_placing_vkr_into_ebs_parameters(),
            [
                'data' => $data
            ]
        );
        try {
            $fov = new file_of_vkr($params['data']['id_vkr']);
            $files = $fov->get_files_of_vkr()->prepared_for_service();
            $ccp = new create_complex_pdf(
                $params['data']['id_vkr'],
                $params['data']['edu_division'],
                $params['data']['theme_name'],
                $params['data']['fio'],
                $params['data']['edu_specialization'],
                $params['data']['fio_manager'],
                $params['data']['year'],
                $params['data']['edu_profile'],
                $params['data']['edu_group'],
                $params['data']['student_course'],
                $params['data']['edu_specialization_code'],
                $params['data']['number_document'],
                $params['data']['edu_lectern'],
                $params['data']['edu_form'],
                $params['data']['edu_level'],
                $params['data']['reviewer']
            );
            $fov->delete_archive_files();
            try {
                $ccp->start_process($files);
            } catch (PdfTypeException|
            CrossReferenceException|
            PdfReaderException|
            PdfParserException|
            FilterException|
            coding_exception $e) {
                throw new coding_exception($e->getLine() . $e->getMessage() . $e->getFile());
            }


           /* $vkr_ebs = new vkr_ebs($ccp);
            return $vkr_ebs->create_data_of_VKR();*/
        } catch (coding_exception $e) {
            throw new coding_exception($e->getLine() . $e->getMessage() . $e->getFile());
        }
    }

    public static function activate_process_placing_vkr_into_ebs_returns(): external_single_structure
    {
        return external_return_types::type_of_result_return();
        # return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function delete_vkr_entirely_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id_vkr' => new external_value(PARAM_TEXT, 'id vkr GUID', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function delete_vkr_entirely($id_vkr)
    {
        global $CFG;

        $params = self::validate_parameters(
            self::delete_vkr_entirely_parameters(),
            [
                'id_vkr' => $id_vkr
            ]
        );
        $l = new layer(new $CFG->use_class_integration());
        return $l->delete_vkrs($params['id_vkr']);
    }

    public static function delete_vkr_entirely_returns(): external_single_structure
    {
        return new external_single_structure([
            'message' => new external_value(PARAM_TEXT, 'result', VALUE_REQUIRED),
            'status' => new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED)
        ]);
    }

    public static function check_is_gek_parameters()
    {
        return new external_function_parameters(
            []
        );
    }

    public static function check_is_gek()
    {
        global $CFG;
        $s = new $CFG->use_class_integration();

        return $s->check_is_gek();
    }

    public static function check_is_gek_returns()
    {
        return null;
    }
}