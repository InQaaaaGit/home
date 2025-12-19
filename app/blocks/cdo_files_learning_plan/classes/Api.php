<?php
require_once __DIR__ . '/config.php';

use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

abstract class Api
{
    /**
     * @param string $code
     * @param $options
     * @param array $urlParams
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    private static function createCurl(string $code, $options, bool $return_body=false): array
    {

        return di::get_instance()
            ->get_request($code)
            ->request($options)
            ->get_request_result($return_body)
            ->to_array();
    }

    /**
     * @param int $user_id
     * @return bool|array|string
     */
    public static function getEducationPrograms(int $user_id): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([
            "user_id" => $user_id,
            "mode" => 'EP_list'
        ]);

        try {
            return self::createCurl('files_learning_get_education_programs', $options);
        } catch (Throwable $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param $user_id
     * @param $doc_number
     * @return bool|array|string
     */
    public static function getEducationProgram($user_id, $doc_number): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([
            "user_id" => $user_id,
            "doc_number" => $doc_number,
            "mode" => 'EP_details'
        ]);

        try {
            return self::createCurl('files_learning_get_education_program', $options);

        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function putEducationProgramFile(array $urlParams, array $bodyParams): bool|array|string
    {
        global $CFG;
        $options = di::get_instance()->get_request_options();
        $bodyParams['mode'] = $urlParams['mode'];
        $options->set_properties($bodyParams);
       /* require_once $CFG->libdir . '/filelib.php';
        $curl = new curl();
        $auth = base64_encode("Администратор1:1");
        $curl->setHeader([
            "Authorization: Basic {$auth}"
        ]);
        $res = $curl->post('http://demo.cdo-global.ru/demo_eios/hs/cdo_eois_Campus/PutEducationProgramFile', $bodyParams);
        ;*/
        try {
            return self::createCurl('files_learning_put_education_program_file', $options);

        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function deleteEducationProgramFile(array $urlParams, array $bodyParams): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties(array_merge($urlParams, $bodyParams));

        try {
            return self::createCurl('files_learning_del_education_program_file', $options);
        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function putDisciplineProgramFile(array $urlParams, array $bodyParams = []): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties($urlParams);
        /*$options->set_properties([
            'doc_id' => '0000000059'
        ]);*/
        /*$options->set_parameters_in_json();*/

        try {
            return self::createCurl('files_learning_put_discipline_program_file', $options);

        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function putEducationProgramLink(array $urlParams, array $bodyParams = []): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties($bodyParams);
        $options->set_parameters_in_json();
        try {
            return self::createCurl('files_learning_put_education_program_link', $options);

        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function getFile(array $urlParams): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties($urlParams);

        try {
            return  di::get_instance()
                ->get_request('files_learning_get_file_binary')
                ->request($options)
                ->get_request_result(true);
        } catch (Throwable $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function getAllFiles(array $urlParams = []): bool|array|string
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]);

        try {
            return self::createCurl('files_learning_get_education_program_all_file', $options);

        } catch (Throwable $e) {

            return [
                'error' => $e->getMessage()
            ];
        }
    }

}
