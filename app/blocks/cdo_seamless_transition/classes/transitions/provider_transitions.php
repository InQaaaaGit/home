<?php

namespace block_cdo_seamless_transition\transitions;

class provider_transitions {
	public static function handler(): void {
		factory_transitions::get_instance();
	}

}