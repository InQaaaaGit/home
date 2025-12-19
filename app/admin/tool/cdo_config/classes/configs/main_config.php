<?php

namespace tool_cdo_config\configs;

final class main_config {
	//Плагины поддерживаемые сервисом
	public static array $supported_plugins = [
		'tool_cdo_config',
		'block_cdo_student_info',
		'block_cdo_seamless_transition',
		'local_cdo_certification_sheet',
        'local_cdo_education_plan',
        'local_cdo_academic_progress',
        'local_cdo_debts',
        'local_cdo_order_documents',
        'local_cdo_rpd',
        'block_cdo_files_learning_plan',
	];

	//Альтернативные имена по которым может происходить обращение к плагинам
	public static array $alias_supported_plugins = [
		'tool_cdoconfig' => 'tool_cdo_config',
		'cdo_config' => 'tool_cdo_config',

		'local_cdocertificationsheet' => 'local_cdo_certification_sheet',
		'cdocertificationsheet' => 'local_cdo_certification_sheet',

		'block_cdostudentinfo' => 'block_cdo_student_info',
		'cdostudentinfo' => 'block_cdo_student_info',

		'cdo_seamless_transition' => 'blocks_cdo_seamless_transition',
		'seamless_transition' => 'blocks_cdo_seamless_transition',
		'block_cdo_seamless_transition' => 'blocks_cdo_seamless_transition',
	];

	//Название компонента для поиска строковых переменных
	public static string $component = 'tool_cdo_config';
}