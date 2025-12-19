<?php

namespace tool_cdo_config\request\DTO;

abstract class base_dto {

	/**
	 * @return string
	 */
	abstract protected function get_object_name(): string;

	/**
	 * @param object $data
	 * @return $this
	 */
	abstract public function build(object $data): self;
}