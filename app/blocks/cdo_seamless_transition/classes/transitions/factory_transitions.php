<?php

namespace block_cdo_seamless_transition\transitions;

use ReflectionClass;
use ReflectionException;

class factory_transitions {

	public const SUPPORTED_TRANSITIONS = [
		'yurayt',
		'lan',
		'ipr_books',
		'znanium',
		'consultant_student',
        'ibooks'
	];

	private static factory_transitions $instance;

	/**
	 * @var i_transition[]
	 */
	private array $transitions;

	/**
	 * @return factory_transitions
	 */
	public static function get_instance(): factory_transitions {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->get_transitions_list();
	}

	public function get_transitions_list(): void {
		foreach (self::SUPPORTED_TRANSITIONS as $transition) {
			$_class_name = __NAMESPACE__ . "\\" . "{$transition}_transition";
			try {
				$reflection = new ReflectionClass($_class_name);
				$_class = new $reflection->name();
				$this->transitions[$_class->get_code()] = $_class;
			} catch (ReflectionException $e) {
				//TODO не удалось создать сервис!!! Обработать
			}
		}
	}

	/**
	 * @param string $name
	 * @return i_transition|null
	 */
	public function get_transition(string $name): ?i_transition {
		return $this->transitions[$name] ?? null;
	}

	/**
	 * @return i_transition[]
	 */
	public function get_transitions(): array {
		return $this->transitions;
	}
}