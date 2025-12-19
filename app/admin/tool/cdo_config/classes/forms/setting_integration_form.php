<?php

namespace tool_cdo_config\forms;

use coding_exception;
use moodleform;
use ReflectionClass;
use ReflectionException;
use tool_cdo_config\configs\main_config;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class setting_integration_form extends moodleform {

	public static string $prefix = 'setting_integration_form_';

	private static array $methods = ['GET', 'PATCH', 'POST', 'PUT', 'DELETE'];

	/**
	 * @return void
	 * @throws coding_exception
	 */
	protected function definition(): void {
		$this->_form->addElement('hidden', "action", 'create');
		$this->_form->setType("action", PARAM_TEXT);
		$this->_form->addElement('hidden', "id", '');
		$this->_form->setType("id", PARAM_TEXT);

		$this->build_main();
		$this->build_auth();
		$this->build_call();
		$this->build_other();
		$this->add_action_buttons();
	}

	/**
	 * @param $data
	 * @param $files
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function validation($data, $files): array {
		return array_unique(array_merge(
			$this->valid_auth($data),
			$this->valid_code($data),
			$this->valid_dto($data),
			$this->valid_mock($data)
		));
	}

	public function get_method_name(int $key): string {
		if (array_key_exists($key, self::$methods)) {
			return self::$methods[$key];
		}
		return self::$methods[0];
	}

	/**
	 * @return void
	 * @description Создаем блок с основными данными
	 * @throws coding_exception
	 */
	private function build_main(): void {
		//Заголовок блока с основной информацией
		$this->_form->addElement('header', self::$prefix . "main", $this->get_string('main'));
		//Поле для ввода названия
		$this->add_element_text("name", true);
		//Поле для ввода названия
		//Поле для ввода описания
		$this->add_element_textarea("description");
		//Поле для ввода описания
		//Поле для выбора метода обращения
		$this->_form->addElement('select', self::$prefix . "method", $this->get_string("method"), self::$methods);
		$this->_form->addHelpButton(
			self::$prefix . "method",
			self::$prefix . "method",
			'tool_cdo_config'
		);
		//Поле для выбора метода обращения
		//Поле для ввода точки запроса
		$this->add_element_text("endpoint", true);
		//Поле для ввода точки запроса
        $this->add_element_text("port");

    }

	/**
	 * @return void
	 * @description Создаем блок с данными для авторизации
	 * @throws coding_exception
	 */
	private function build_auth(): void {
		//Заголовок для блока авторизации
		$this->_form->addElement('header', self::$prefix . "auth", $this->get_string("auth"));
		//Поле для выбора использования авторизации
		$this->add_element_checkbox("no_auth");
		//Поле для выбора использования авторизации
		//Поле для выбора использования токена или логина
		$this->add_element_checkbox("auth_token");
		//Поле для выбора использования токена или логина
		//Поле для ввода логина
		$this->add_element_text("login", false, true, 'auth_token');
		//Поле для ввода логина
		//Поле для ввода пароля
		$this->add_element_text("password", false, true, 'auth_token');
		//Поле для ввода пароля
		//Поле для ввода типа токена
		$this->add_element_text("type_token", false, true, 'auth_token', 'notchecked');
		//Поле для ввода типа токена
		//Поле для ввода токена
		$this->add_element_text("token", false, true, 'auth_token', 'notchecked');
		//Поле для ввода токена
	}

	/**
	 * @return void
	 * @description Создаем блок с данными для вызова
	 * @throws coding_exception
	 */
	private function build_call(): void {
		//Заголовок для блока с вызовами
		$this->_form->addElement('header', self::$prefix . "call", $this->get_string("call"));
		//Поле для ввода кода обращения к методу
		$this->add_element_text("code", true);
		//Поле для ввода кода обращения к методу
		//Поле для ввода namespace файла DTO
		$this->add_element_text("dto", true);
		//Поле для ввода namespace файла DTO
	}

	/**
	 * @return void
	 * @description Создаем блок с дополнительными данными
	 */
	private function build_other(): void {
		//Заголовок для блока с дополнительными параметрами
		$this->_form->addElement('header', self::$prefix . "other", $this->get_string("other"));
		//Поле для ввода дополнительных параметров заголовка
		$this->add_element_textarea("headers");
		//Поле для ввода дополнительных параметров заголовка
		//Поле для включения использования mock
		$this->add_element_checkbox("use_mock");
		//Поле для включения использования mock
		//Поле для ввода данных mock
		$this->add_element_textarea("mock", true, 'use_mock', 'notchecked');
		//Поле для ввода данных mock
	}

	/**
	 * @param string $name
	 * @param bool $required
	 * @param bool $hide
	 * @param string $hide_element
	 * @param string $hide_type
	 * @return void
	 * @throws coding_exception
	 * @description Создаем текстовый (input) элемент
	 */
	private function add_element_text(
		string $name,
		bool $required = false,
		bool $hide = false,
		string $hide_element = '',
		string $hide_type = 'checked'
	): void {
		$attribute = ['size' => 50];
		$this->_form->addElement('text', self::$prefix . $name, $this->get_string($name), $attribute);
		$this->_form->addHelpButton(self::$prefix . $name, self::$prefix . $name, main_config::$component);
		$this->_form->setType(self::$prefix . $name, PARAM_TEXT);

		if ($required) {
			$this->_form->addRule(self::$prefix . $name, $this->get_string('required_param'), 'required');
		}

		if ($hide) {
			$this->_form->hideIf(self::$prefix . $name, self::$prefix . $hide_element, $hide_type);
		}
	}

	/**
	 * @param string $name
	 * @param bool $hide
	 * @param string $hide_element
	 * @param string $hide_type
	 * @return void
	 * @description Создаем текстовую область (textarea)
	 */
	private function add_element_textarea(
		string $name,
		bool $hide = false,
		string $hide_element = '',
		string $hide_type = 'checked'
	): void {
		$attribute = ['rows' => 3, 'cols' => 50];
		$this->_form->addElement('textarea', self::$prefix . $name, $this->get_string($name), $attribute);
		$this->_form->addHelpButton(
			self::$prefix . $name,
			self::$prefix . $name,
			main_config::$component
		);
		if ($hide) {
			$this->_form->hideIf(self::$prefix . $name, self::$prefix . $hide_element, $hide_type);
		}

	}

	/**
	 * @param string $name
	 * @return void
	 * @description Создаем переключатель (checkbox)
	 */
	private function add_element_checkbox(string $name): void {
		$this->_form->addElement('checkbox', self::$prefix . $name, $this->get_string($name));
		$this->_form->addHelpButton(
			self::$prefix . $name,
			self::$prefix . $name,
			main_config::$component
		);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function valid_auth(array $data): array {
		$result = [];

		$req_string = $this->get_string('required_param');

		$closure = static function(string $name) use ($result, $data, $req_string) {
			if (!array_key_exists(self::$prefix . $name, $data) || $data[self::$prefix . $name] === "") {
				$result[self::$prefix . $name] = $req_string;
				return $result;
			}
			return [];
		};

		//Параметр no_auth не задан, нужно проверить существование параметров для авторизации
		if (array_key_exists(self::$prefix . "no_auth", $data)) {
			return $result;
		}
		//Параметр no_auth не задан, нужно проверить существование параметров для авторизации
		//Параметр auth_token не задан, проверяем наличие логина и пароля
		if (!array_key_exists(self::$prefix . "auth_token", $data)) {
			$result = array_merge($result, $closure("login"));
			$result = array_merge($result, $closure("password"));
		} //Параметр auth_token задан, проверяем наличие токена и типа токена
		else {
			$result = array_merge($result, $closure("type_token"));
			$result = array_merge($result, $closure("token"));
		}
		return $result;
	}

	/**
	 * @param array $data
	 * @return array
	 * @throws cdo_config_exception
	 */
	private function valid_code(array $data): array {
		$result = [];
		if ($this->_form->getElementValue('id') === ''
			&& !cdo_config::check_code_uniq($data[self::$prefix . "code"])) {
			$result[self::$prefix . "code"] = $this->get_string('is_use_param');
		}
		return $result;
	}

	private function valid_dto(array $data): array {
		$result = [];
		try {
			//Проверяем существование указанного класса для обработки DTO
			$data[self::$prefix . "dto"] = str_replace('/', '\\', $data[self::$prefix . "dto"]);
			(new ReflectionClass($data[self::$prefix . "dto"]));
		} catch (ReflectionException $e) {
			$result[self::$prefix . "dto"] = $this->get_string('empty_class');
		}
		return $result;
	}

	private function valid_mock(array $data): array {
		$result = [];
		//Параметр use_mock задан, нужно проверить на заполненность mock
		if (
			array_key_exists(self::$prefix . "use_mock", $data)
			&& (
				!array_key_exists(self::$prefix . "mock", $data)
				|| $data[self::$prefix . "mock"] === ""
			)
		) {
			$result[self::$prefix . "mock"] = $this->get_string('required_param');
		}
		return $result;
	}

	/**
	 * @param string $id
	 * @return string
	 */
	private function get_string(string $id): string {
		$string_manager = get_string_manager();
		if ($string_manager && $string_manager->string_exists(self::$prefix . $id, main_config::$component)) {
			return $string_manager->get_string(self::$prefix . $id, main_config::$component);
		}
		return self::$prefix . $id;
	}
}