<?php

namespace tool_cdo_config\services;

use coding_exception;
use stdClass;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\models\cdo_config;
use tool_cdo_config\request\DTO\response_dto;
use tool_cdo_config\request\handler;
use tool_cdo_config\request\options_build;

class request_integration
{

    private int $id;
    private int $no_auth;
    private int $auth_token;
    private int $use_mock;

    private string $name;
    private string $description;
    private string $method;
    private string $endpoint;
    private ?string $login;
    private ?string $password;
    private ?string $type_token;
    private ?string $token;
    private string $code;
    private string $dto;
    private string $headers;
    private ?string $mock;

    private handler $handler;

    /**
     * @param string $code
     * @throws cdo_config_exception
     */
    public function __construct(string $code)
    {
        $detail = cdo_config::get_instance()->get_detail_by_code($code);
        if (!$detail) {
            throw new cdo_config_exception(3002);
        }

        foreach ($detail as $key => $item) {
            if (property_exists($this, $key)) {
                $this->{$key} = $item;
            }
        }
    }

    public function request(options_build $options): self
    {
        $this->handler = di::get_instance()->get_request_handler()
            ->set_class($this)
            ->build_curl()
            ->build_options($options->get_parameters_in_json())
            ->set_parameters($options->get_properties())
            ->set_method($this->method)
            ->set_debug($options->get_debug())
            ->replace_header($options->get_replace_headers())
            ->set_headers($options->get_headers());
        return $this;
    }

    /**
     * @param bool $return_body
     * @return response_dto|string
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function get_request_result(bool $return_body = false): response_dto|string
    {

        $_result = new stdClass();
        $result = $this->handler->send($return_body);
        if ($return_body) {
            return $result;
        }
        if (is_array($result)) {
            foreach ($result as $key => $item) {
                if (is_string($item) && is_number($key)) {
                    $_result->data = $result;
                    break;
                }
            }
        }

        if (property_exists($_result, 'data')) {
            $result = $_result;
        }

        return response_dto::transform(
            $this->dto,
            $result
        );
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return "";
    }
}
