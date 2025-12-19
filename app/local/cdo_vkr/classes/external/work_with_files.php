<?php

namespace local_cdo_vkr\external;

use coding_exception;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use file_exception;
use invalid_parameter_exception;
use local_cdo_vkr\utility\external_return_types;
use local_cdo_vkr\utility\file_of_vkr;
use local_cdo_vkr\utility\fileinfo;
use local_cdo_vkr\VKR\layer;
use local_cdo_vkr\VKR\main;
use moodle_exception;
use moodle_url;
use stored_file_creation_exception;

class work_with_files extends external_api
{
    public static function save_file_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'binary_string' => new external_value(PARAM_RAW, 'binary_string', VALUE_REQUIRED),
                'filename' => new external_value(PARAM_TEXT, 'filename', VALUE_REQUIRED),
                'itemid' => new external_value(PARAM_TEXT, 'itemid', VALUE_REQUIRED),
                'filepath' => new external_value(PARAM_TEXT, 'filepath', VALUE_DEFAULT, '/'),
                'id_vkr' => new external_value(PARAM_TEXT, 'id_vkr', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function save_file($binary_string, $filename, $itemid, $filepath, $id_vkr)
    {
        $params = self::validate_parameters(self::save_file_parameters(),
            [
                'binary_string' => $binary_string,
                'filename' => $filename,
                'itemid' => $itemid,
                'filepath' => $filepath,
                'id_vkr' => $id_vkr,
            ]
        );

        $fileinfo = new fileinfo(
            $params['itemid'],
            '/' . $id_vkr . $params['filepath'] ?? '/',
            time() .$params['filename']
        );

        $fileinfo = $fileinfo->getParamsArray();

        $fs = get_file_storage();
        try {
            # $transaction = $DB->start_delegated_transaction();
            $file = $fs->create_file_from_string($fileinfo, base64_decode($params['binary_string']));
            $file_id = $file->get_id();
            $fileVKR = new file_of_vkr(
                $params['id_vkr'],
                $file_id
            );
            $comment = $fileVKR
                ->create_file_of_vkr($file_id, $fileinfo['filepath'], 0)
                ->type_of_vkr_file_structure();

            #  $DB->commit_delegated_transaction($transaction);
        } catch (stored_file_creation_exception|file_exception $e) {
            #  $DB->rollback_delegated_transaction($transaction, $e);
            return $e->getMessage();
        }

        return $comment;
    }

    public static function save_file_returns(): external_single_structure
    {
        return (new external_return_types('', VALUE_DEFAULT, []))->type_of_vkr_file();
    }

    public static function get_file_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'itemid' => new external_value(PARAM_TEXT, 'filename', VALUE_REQUIRED),
                'filepath' => new external_value(PARAM_TEXT, 'filename', VALUE_DEFAULT, '/'),
            ]
        );
    }

    /**
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function get_file($itemid, $filepath)
    {

        $params = self::validate_parameters(self::get_file_parameters(),
            [
                'itemid' => $itemid,
                'filepath' => $filepath,
            ]
        );

        $fileinfo = (new fileinfo(
            $params['itemid'],
            $params['filepath'] ?? '/'
        ))->getParamsArray();

        $fileURLs = [];

        $fs = get_file_storage();

        $filesInArea = $fs->get_area_files(
            $fileinfo['contextid'],
            $fileinfo['component'],
            $fileinfo['filearea'],
            $params['itemid']
        );
        foreach ($filesInArea as $file) {
            #$fileURLs[] = base64_encode($file->get_content());
            $fileURLs[] = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                false                    // Do not force download of the file.
            )->out();
        }

        return array_pop($fileURLs);
    }

    public static function get_file_returns(): external_value
    {
        return new external_value(PARAM_RAW, 'url for file', VALUE_REQUIRED);
    }

    public static function get_files_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id_vkr' => new external_value(PARAM_TEXT, 'id_vkr', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_files($id_vkr): array
    {
        $file_of_vkr = new file_of_vkr($id_vkr);
        return $file_of_vkr->get_files_of_vkr()->prepared_for_service();
    }

    public static function get_files_returns(): external_single_structure
    {
        $file_struct = (new external_return_types('file sub structure', VALUE_DEFAULT, []))->type_of_vkr_file();
        #$review = new external_return_types('review', VALUE_DEFAULT, []);

        return new external_single_structure(
            [
                'comment' => $file_struct,
                'review' => $file_struct,
                'work' => $file_struct,
                #  'work_archive' => new external_single_structure([], '', VALUE_OPTIONAL), //TODO this is array
                'work_archive' => new external_multiple_structure(
                    $file_struct,
                    '',
                    VALUE_OPTIONAL
                ), //TODO this is array
            ],
            'structure with comment, review, work and archive',
            VALUE_REQUIRED
        );
    }

    public static function delete_file_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'file_id' => new external_value(PARAM_INT, 'file id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function delete_file($file_id): bool
    {
        $params = self::validate_parameters(self::delete_file_parameters(),
            [
                'file_id' => $file_id
            ]
        );
        try {
            (new file_of_vkr(0, $params['file_id']))->delete_file_of_vrk_complex();
        } catch (moodle_exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public static function delete_file_returns(): external_value
    {
        return new external_value(PARAM_BOOL, '', VALUE_REQUIRED);
    }

    public static function push_work_to_archive_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'file_id' => new external_value(PARAM_INT, 'file id', VALUE_REQUIRED),
                'id_vkr' => new external_value(PARAM_TEXT, 'file id', VALUE_REQUIRED),
                'reason' => new external_value(PARAM_TEXT, 'reason', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @param $file_id
     * @param $id_vkr
     * @return null
     * @throws dml_exception
     * @throws file_exception
     * @throws invalid_parameter_exception
     * @throws stored_file_creation_exception
     */
    public static function push_work_to_archive($file_id, $id_vkr, $reason): ?bool
    {
        $params = self::validate_parameters(self::push_work_to_archive_parameters(),
            [
                'file_id' => $file_id,
                'id_vkr' => $id_vkr,
                'reason' => $reason
            ]
        );
        $fs = get_file_storage();
        $file_fs = $fs->get_file_by_id($params['file_id']);
        if ($file_fs) {
            $new_archive_file = new fileinfo(
                $file_fs->get_itemid() + 1,
                '/' . $params['id_vkr'] . '/archive/',
                $file_fs->get_filename()
            );

            $archive_file = $fs->create_file_from_storedfile(
                $new_archive_file,
                $file_fs
            );
            $fov = new file_of_vkr($params['id_vkr'], $params['file_id']);
            $fov->delete_file_of_vrk_complex();
            $fov->create_file_of_vkr(
                $archive_file->get_id(),
                '/' . $params['id_vkr'] . '/archive/',
                0, // - work student doesn't need status for today,
                $params['reason']
            );
        } else {
            return false;
        }

        return true;
    }

    public static function push_work_to_archive_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function set_acquainted_status_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'file_id' => new external_value(PARAM_INT, 'file id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function set_acquainted_status($file_id): bool
    {
        $params = self::validate_parameters(self::set_acquainted_status_parameters(),
            [
                'file_id' => $file_id,
            ]
        );

        $fov = new file_of_vkr(0, $params['file_id']);

        $params['status'] = 1;
        $params['id'] = $fov->get_vkr_file_id();

        return $fov->update_data_of_file_VKR($params);
    }

    public static function set_acquainted_status_returns(): external_value
    {
        return new external_value(PARAM_BOOL, '', VALUE_REQUIRED);
    }

}