<?php

namespace tool_cdo_config\request;


use stdClass;

class options_build
{

    private string $method = 'GET';
    private $properties = [];
    private array $headers = [];
    private bool $debug = false;
    private bool $replace_headers = false;
    private bool $parameters_in_json = false;

    /**
     * @return string
     */
    public function get_method(): string
    {
        return $this->method;
    }

    /**
     * @return array|string
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function get_headers(): array
    {
        return $this->headers;
    }

    /**
     * @return bool
     */
    public function get_debug(): bool
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function get_replace_headers(): bool
    {
        return $this->replace_headers;
    }

    /**
     * @param string $method
     * @return options_build
     */
    public function set_method(string $method): options_build
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param array|stdClass|string $properties
     * @return options_build
     */
    public function set_properties($properties): options_build
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @param array $headers
     * @return options_build
     */
    public function set_headers(array $headers): options_build
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param bool $debug
     * @return options_build
     */
    public function set_debug(bool $debug): options_build
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @param bool $replace_headers
     * @return options_build
     */
    public function set_replace_headers(bool $replace_headers): options_build
    {
        $this->replace_headers = $replace_headers;
        return $this;
    }

    public function get_parameters_in_json(): bool
    {
        return $this->parameters_in_json;
    }
    /**
     * @return options_build
     */
    public function set_parameters_in_json(): options_build
    {
        $this->parameters_in_json = true;
        return $this;
    }
}