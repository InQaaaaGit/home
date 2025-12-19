<?php

namespace block_cdo_seamless_transition\transitions;

interface i_transition {
	public function get_code(): string;
	public function is_active(): bool;
	public function to(): string;
	public function get_transition_name(): string;
	public function get_external_data(): external_data_transition;
}