<?php

namespace tool_cdo_config\request\DTO;

interface i_iterator {
	/**
	 * @return array
	 */
	public function to_array(): array;

	/**
	 * @return string
	 */
	public function to_json(): string;
}