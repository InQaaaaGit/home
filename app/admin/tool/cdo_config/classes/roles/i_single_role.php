<?php

namespace tool_cdo_config\roles;

interface i_single_role {
	public function get_name_capability(): string;
	public function get_ignore_activity_capability(): array;
}