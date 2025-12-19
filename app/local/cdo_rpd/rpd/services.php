<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

defined('MOODLE_INTERNAL') || die();

class ulsu_1c_services
{
    const GetPlan = "ulsu_eios/GetPlan?";
    const GetUserInfo = "campus/teacher/employment/info?";
    const GetPlanList = "ulsu_eios/ListPlans?";
    const GetChairInfo = "ulsu_eios/GetChairInfo?";
    const GetUserIdByMoodleID = "cdo_eois_Campus/GetIdByMoodle?";

    private function config()
    {
        require('../../CDO/config.php');
        global $CFG_CDO;
        return $CFG_CDO;
    }

    public function GetPlan($param)
    {
        $return = $this->requestServices(self::GetPlan, "", "", $param, "GET");
        return $return;
    }

    public function  GetListPlans($param){
        $return = $this->requestServices(self::GetPlanList, "", "", $param, "GET");

        return $return;
    }


    public function GetUserInfo($param)
    {
        $return = $this->requestServices(self::GetUserInfo, "", "", ['id' => $this->getUserIdByMoodleId($param['id'])], "GET");
        return $return;
    }
    public function getUserIdByMoodleId($id)
    {
        $return = $this->requestServices(self::GetUserIdByMoodleID, "", "", ['id' => $id], "GET");
        return json_decode($return)->code;
    }

    public function GetChairInfo($param)
    {
        $return = $this->requestServices(self::GetChairInfo, "", "", $param, "GET");
        return $return;
    }

    function requestServices($url, $username = "", $password = "", $urlParameters, $method = "GET", $anotherurl = false, $headers = array(), $justanother = false, $isFile = false, $httbuidquery = false)
    {

        if ($justanother) {
            $str = "";
            if ($method === "GET" || $method === "DELETE") {

                $c = count((array)$urlParameters);
                $j = 1;
                foreach ($urlParameters as $key => $value) {
                    if ($j !== $c)
                        $str .= $key . "=" . $value . "&";
                    else
                        $str .= $key . "=" . $value;
                    $j++;
                }
            }
            $myRequest = curl_init($url . $str);
        } else {
            $cfg = $this::config();
            if ($anotherurl) {
                if ($method === "GET" || $method === "DELETE") {
                    $str = "";
                    $c = count((array)$urlParameters);
                    $j = 1;
                    foreach ($urlParameters as $key => $value) {
                        if ($j !== $c)
                            $str .= $key . "=" . $value . "&";
                        else
                            $str .= $key . "=" . $value;
                        $j++;
                    }
                }

                $myRequest = curl_init($cfg->asr_url . $url . $str);
                //return $cfg->asr_url . $url . $str;
            } else {
                $str = "";
                if ($method === "GET" || $method === "DELETE") {
                    $str = "";
                    $c = count((array)$urlParameters);
                    $j = 1;
                    foreach ($urlParameters as $key => $value) {
                        if ($j !== $c)
                            $str .= $key . "=" . $value . "&";
                        else
                            $str .= $key . "=" . $value;
                        $j++;
                    }
                }

                //  $myRequest = curl_init($cfg->url . $url . $str);

                if (!empty($headers)) {
                    $myRequest = curl_init($url . $str);
                    //return $headers;
                } else {
                    $myRequest = curl_init($cfg->url . $url . $str);
                }
            }
        }
        // return $cfg->url . $url . $str;
        if ($method === "POST") {
            curl_setopt($myRequest, CURLOPT_POST, TRUE);

            if ($httbuidquery)
                curl_setopt($myRequest, CURLOPT_POSTFIELDS, http_build_query($urlParameters, "", "&"));
            else curl_setopt($myRequest, CURLOPT_POSTFIELDS, $urlParameters);

        } elseif ($method === "PATCH") {
            curl_setopt($myRequest, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($myRequest, CURLOPT_POSTFIELDS, $urlParameters);
        } elseif ($method === "DELETE") {
            curl_setopt($myRequest, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if (!empty($headers)) {
            curl_setopt($myRequest, CURLOPT_HTTPHEADER, $headers);
        }

        if (!$justanother) {

            if ($anotherurl) {
                //curl_setopt($myRequest, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($myRequest, CURLOPT_USERPWD, "$cfg->asr_hslogin:$cfg->asr_hspass");
            } else {
                curl_setopt($myRequest, CURLOPT_USERPWD, "$cfg->hslogin:$cfg->hspass");
            }
        } else {
            curl_setopt($myRequest, CURLOPT_USERPWD, "$username:$password");
        }

        curl_setopt($myRequest, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myRequest, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($myRequest, CURLOPT_BINARYTRANSFER, 1);

        $response = curl_exec($myRequest);
        $statusCode = curl_getinfo($myRequest, CURLINFO_HTTP_CODE);
        //console_log(curl_getinfo($myRequest));

        if ($statusCode === 200) {
            if ($isFile) {
                $return = $response;
            } else {
                $response = str_replace("\r\n", NULL, $response);
                $response = preg_replace(
                    '/
                    ^
                    [\pZ\p{Cc}\x{feff}]+
                    |
                    [\pZ\p{Cc}\x{feff}]+$
                /ux',
                    '',
                    $response
                );
                $return = $response;
            }
        } else {
            // $return = '{ "status": ".$statuscode." }';
            $return = $response;
        }

        curl_close($myRequest);
        return $return;

    }
}
