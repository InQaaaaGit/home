<?php

namespace tool_cdo_config\request;

interface i_request {

	public function set_debug(bool $debug): i_request;
	public function set_method(string $method): i_request;
	public function set_parameters($parameters): i_request;
	public function set_headers(array $headers): i_request;
	public function replace_header(bool $replace): i_request;

	public function build_curl(): i_request;
	public function build_options(bool $parameters_in_json): i_request;

	/**
	 * @return mixed
	 */
	public function send();
}