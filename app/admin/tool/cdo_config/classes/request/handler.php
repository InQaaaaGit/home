<?php

namespace tool_cdo_config\request;

use coding_exception;
use curl;
use SoapClient;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\services\request_integration;
use tool_cdo_config\tools\dumper;

global $CFG;

require_once($CFG->libdir . '/filelib.php');

class handler implements i_request
{

    private Curl $curl;
    private SoapClient $soap;
    private request_integration $class;

    private bool $debug = false;
    private string $method;
    private $parameters = [];
    private bool $parameters_in_json = false;

    /**
     * @param request_integration $class
     * @return i_request
     */
    public function set_class(request_integration $class): i_request
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param bool $debug
     * @return i_request
     */
    public function set_debug(bool $debug): i_request
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @param string $method
     * @return i_request
     */
    public function set_method(string $method): i_request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param array|string $parameters
     * @return i_request
     */
    public function set_parameters($parameters): i_request
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param array $headers
     * @return i_request
     * @throws cdo_config_exception
     */
    public function set_headers(array $headers): i_request
    {
        $this->build_curl();
        $this->curl->setHeader($headers);
        return $this;
    }

    /**
     * @param bool $replace
     * @return i_request
     * @throws cdo_config_exception
     */
    public function replace_header(bool $replace): i_request
    {
        $this->build_curl();
        if ($replace) {
            $this->curl->resetHeader();
        }
        return $this;
    }

    /**
     * @return i_request
     * @throws cdo_config_exception
     */
    public function build_curl(): i_request
    {
        if (!isset($this->curl)) {
            $this->curl = new Curl(['ignoresecurity' => true]);
        }
        $this->set_curl_auth();
        $this->set_curl_headers();

        return $this;
    }

    /**
     * @throws cdo_config_exception
     */
    public function build_options($parameters_in_json): i_request
    {
        $this->parameters_in_json = $parameters_in_json;
        $port = $this->class->get("port");
        if (!empty($port)) {
            try {
                $this->curl->setopt(['CURLOPT_PORT' => (int)$port]);
            } catch (coding_exception $e) {
                throw new cdo_config_exception(1003);
            }
        }
        return $this;
    }

    public function build_soap($wsdl)
    {
        if (!isset($this->soap)) {
            $this->soap = new SoapClient($this->class->get("endpoint"));
        }
    }

    /**
     * @return void
     * @throws cdo_config_exception
     */
    public function set_curl_auth(): void
    {
        if ((int)$this->class->get("no_auth")) {
            return;
        }

        if ((int)$this->class->get("auth_token")) {
            $token_type = $this->class->get("token_type");
            $token = $this->class->get("token");
            if ($token_type === "") {
                $token_type = "Bearer";
            }
            if ($token === "") {
                throw new cdo_config_exception(1021);
            }
            $this->curl->setHeader([
                "Authorization: {$token_type} {$token}"
            ]);
        } else {
            $login = $this->class->get("login");
            $password = $this->class->get("password");

            if (!$login) {
                throw new cdo_config_exception(1022);
            }
            if (!$password) {
                throw new cdo_config_exception(1023);
            }
            $auth = base64_encode("{$login}:{$password}");
            $this->curl->setHeader([
                "Authorization: Basic {$auth}"
            ]);
        }
    }

    /**
     * @return void
     */
    public function set_curl_headers(): void
    {
        if ($this->class->get("headers") !== "") {
            $this->curl->setHeader(explode(PHP_EOL, $this->class->get("headers")));
        }
    }

    /**
     * @return string
     * @throws cdo_config_exception
     */
    private function get_curl_endpoint(): string
    {
        $endpoint = $this->class->get("endpoint");
        $matches = null;
        preg_match('((https?|ftp)\:\/\/)', $endpoint, $matches);
        if (count($matches)) {
            return trim($endpoint);
        }
        throw new cdo_config_exception(1001);
    }

    /**
     * @param bool $return_body
     * @return mixed
     * @throws cdo_config_exception
     */
    public function send(bool $return_body = false): mixed
    {
        $parameters = $this->parameters_in_json ? json_encode($this->parameters) : $this->parameters;
        if (!!$this->class->get("use_mock")) {

            return validate_json_response::get_instance()
                ->set_result($this->class->get("mock"))
                /*  ->set_curl($this->curl)*/
                ->validate(true, $return_body) // mock
                ->get_data();
        }
        $result = match ($this->method) {
            "POST" => $this->curl->post($this->get_curl_endpoint(), $parameters),
            "PUT" => $this->curl->put($this->get_curl_endpoint(), $parameters),
            "DELETE" => $this->curl->delete($this->get_curl_endpoint(), $this->parameters),
            "PATCH" => $this->curl->patch($this->get_curl_endpoint(), $this->parameters),
            default => $this->curl->get($this->get_curl_endpoint(), $this->parameters),
        };
        if (!$return_body) {
            $result = preg_replace(
                '/
                ^
                [\pZ\p{Cc}\x{feff}]+
                |
                [\pZ\p{Cc}\x{feff}]+$
            /ux',
                '',
                $result
            );
        }
        $this->print_debug($result);
        if ($return_body) {
            return $result;
        }
        return validate_json_response::get_instance()
            ->set_result($result)
            ->set_curl($this->curl)
            ->validate()
            ->get_data();
    }

    /**
     * @param mixed $result
     * @return void
     */
    private function print_debug($result): void
    {
        if ($this->debug) {
            echo '<h1>Return Data</h1>';
            dumper::dump($result);
            echo '<h1>Info</h1>';
            dumper::dump($this->curl->get_info());
            echo '<h1>Error</h1>';
            dumper::dd($this->curl->get_errno());
        }
    }
}
