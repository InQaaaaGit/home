<?php

namespace local_cdo_vkr\VKR;

use coding_exception;
use curl;
use dml_exception;
use local_cdo_vkr\utility\file_of_vkr;
use moodle_exception;

class service_vkr_1c implements main_interface
{
    const URL_GET_LIST_OF_VKR = 'VKRService/getListVKR';
    const URL_UPDATE_DATA_OF_VKR = 'VKRService/updateStatusVKR';
    const URL_UPDATE_CHECK_IS_GEK = 'VKRService/checkIsGEK';

    private $host = '';
    /**
     * @throws coding_exception
     */
    private function curl(): curl
    {
        //version low then 3.10

        global $CFG;
        require_once($CFG->dirroot . "/lib/filelib.php");
        require_once($CFG->dirroot . "/CDO/config.php");
        global $CFG_CDO;
        $this->host = $CFG_CDO->url;
        $curl = new curl();

        $auth = base64_encode("$CFG_CDO->hslogin:$CFG_CDO->hspass");
        $curl->setopt(['CURLOPT_USERPWD' => $auth]);
        $curl->setHeader([
            "Authorization: Basic {$auth}"
        ]);
        return $curl;
    }

    /**
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_list_of_VKR($constraint = []): array
    {
        global $CFG;
        require($CFG->dirroot . '/lib/filelib.php'); // latest version dont hae namespace
        $curl = $this->curl();
        if (empty($constraint)) {
            $constraint = [
                'user_id' => 'all'
            ];
        }
        $response = $curl->get($this->host . self::URL_GET_LIST_OF_VKR, $constraint);

        if (isset($curl) && array_key_exists('http_code', (array)$curl->get_info())) {
            $http_code = (int)$curl->get_info()['http_code'];
            if ($http_code !== 200) {
                throw new moodle_exception($http_code);
            }
        }
        if (is_string($response)) {
            return json_decode($response);
        }
        return [];
    }

    /**
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function update_data_of_VKR(array $new_data): bool
    {
        $curl = $this->curl();

        $response = $curl->post($this->host . self::URL_UPDATE_DATA_OF_VKR, json_encode($new_data));
        if (isset($curl) && array_key_exists('http_code', (array)$curl->get_info())) {
            $http_code = (int)$curl->get_info()['http_code'];
            if ($http_code !== 200) {
                throw new moodle_exception(
                    200,
                    'local_cdo_vrk',
                    '',
                    $response
                );
            }
        }
        if (is_string($response)) {
            return json_decode($response);
        }
        return false;
    }

    /**
     * @throws moodle_exception
     * @throws dml_exception
     */
    public function delete_data_of_VKR($id_vkr): array
    {
        $fov = new file_of_vkr($id_vkr);
        return $fov->delete_files_of_vkr();
    }

    public function check_is_gek()
    {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/lib/filelib.php'); // latest version dont hae namespace
        $curl = $this->curl();
        $user_id = $USER->id;
        if ($USER->id == '3') {
            $user_id = 4463;

        }
        $constraint = [
            'user_id' => $user_id
        ];

        $response = $curl->get($this->host . self::URL_UPDATE_CHECK_IS_GEK, $constraint);

        if (isset($curl) && array_key_exists('http_code', (array)$curl->get_info())) {
            $http_code = (int)$curl->get_info()['http_code'];
            if ($http_code !== 200) {
                throw new moodle_exception($http_code);
            }
        }
        if (is_string($response)) {
            return json_decode($response);
        }
        return [];
    }

}