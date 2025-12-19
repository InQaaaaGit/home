<?php

namespace tool_cdo_config\settings;

use moodle_url;

interface i_settings {

	/**
	 * @description Уникальный код для раздела
	 * @return string
	 */
	public function get_directory_code(): string;

	/**
	 * @description Название раздела
	 * @return string
	 */
	public function get_directory_name(): string;

	/**
	 * @description Уникальный код пункта меню
	 * @return string
	 */
	public function get_code(): string;

	/**
	 * @description Название пункта меню
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * @description Ссылка на страницу с отображением формы
	 * @return moodle_url
	 */
	public function get_url_view(): moodle_url;

	/**
	 * @description Разрешения
	 * @return array
	 */
	public function get_capability(): array;
}