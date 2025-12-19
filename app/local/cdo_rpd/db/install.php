<?php

use local_cdo_rpd\helpers\plugin;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws coding_exception
 * @throws dml_exception
 */
function xmldb_local_cdo_rpd_install(): bool {
    $context = context_system::instance();
	$rpd = create_role('(ЦДО) Работа с РПД', plugin::$cdo_work_with_rpd, '');
    create_role('(ЦДО) Администратор РПД', 'cdo_rpd_admin', '');
    create_role('(ЦДО) Администратор библиотеки РПД', 'cdo_rpd_library_admin', '');
    create_role('(ЦДО) Сотрудник Библиотеки РПД', 'cdo_rpd_library_worker', '');
    create_role('(ЦДО) Ответственный секретарь', 'cdo_rpd_executive_secretary', '');
    //assign_capability('local/cdo_rpd:view_admin_rpd', CAP_ALLOW, $rpd, $context);
	return true;
}