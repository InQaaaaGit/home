<?php
$string['pluginname'] = 'Инструменты AG';
$string['privacy:metadata'] = 'Плагин Инструменты AG не хранит персональные данные.';
$string['settings_availability'] = 'Дополнительные настройки доступности';
$string['chose_user'] = 'Выберите пользователя';
$string['grade_report'] = 'Файл с заполненными оценками';
$string['header'] = 'Настройки для назначения хранения контрольных работ';
$string['header_desc'] = '';
$string['file_repository'] = 'Репозиторий';
$string['file_repository_desc'] = 'Выберите репозиторий, содержащий требуемую структуру файлов';
$string['qr_code_y'] = 'Координата Y';
$string['qr_code_y_desc'] = 'Укажите координату Y QR-кода';
$string['qr_code_x'] = 'Координата X';
$string['qr_code_x_desc'] = 'Укажите координату X QR-кода';
$string['qr_code_size'] = 'Размер QR-кода';
$string['qr_code_size_desc'] = 'Укажите размер QR-кода в пикселях';
$string['fio_y'] = 'Координата Y для ФИО';
$string['fio_y_desc'] = 'Укажите координату Y для ФИО';
$string['fio_x'] = 'Координата X для ФИО';
$string['fio_x_desc'] = 'Укажите координату X для ФИО';

// Новые строки для qr_view.php
$string['qrcodegenerator'] = 'Генератор QR-кодов';
$string['qrcode'] = 'Генерация контрольных работ';
$string['yourpersonalqrcode'] = 'Через некоторое время файл будет сгенерирован, и вы сможете его скачать';
$string['uniqueqrcode'] = 'Этот QR-код уникален для вашего аккаунта.';

// Новая строка для настройки чекбокса
$string['only_final_works'] = 'Загружать только итоговые работы';
$string['only_final_works_desc'] = 'Если отмечено, будут загружены только итоговые работы.';

$string['new_numeric_setting'] = 'Новая числовая настройка';
$string['new_numeric_setting_desc'] = 'Описание для новой числовой настройки.';
$string['button_text'] = 'Выполнить действие';

$string['accumulate_grades_task_name'] = 'Задача накопления оценок';

$string['update_grades_for_doubling_title'] = 'Обновление оценок для удвоения';
$string['update_grades_for_doubling_heading'] = 'Обновление оценок для удвоения';
$string['course_id_label'] = 'ID курса';
$string['course_id_required'] = 'Пожалуйста, введите ID курса';
$string['course_id_numeric'] = 'Пожалуйста, введите корректное число';
$string['submit_button'] = 'Отправить';
$string['notify_success'] = 'Переоценка запланирована. Курс для переоценки: ';

$string['clear_zero_grades_link'] = 'Начать очистку нулевых оценок';
$string['run_regrade_course_link'] = 'Начать переоценку в курсе (удвоение оценок)';
$string['availability_settings_link'] = 'Настройки доступности';
$string['choose_course'] = 'Выберите курс';
$string['open_all_quarter'] = 'Открыть все четверти';
$string['close_all_quarter'] = 'Закрыть все четверти';
$string['open'] = 'Открыть';
$string['close'] = 'Закрыть';
$string['st_quarter'] = '1-я четверть';
$string['nd_quarter'] = '2-я четверть';
$string['rd_quarter'] = '3-я четверть';
$string['th_quarter'] = '4-я четверть';
$string['quarter'] = 'Четверть';
$string['actions'] = 'Действия';
$string['enter_surname'] = 'Введите фамилию';

$string['grade_notification_subject'] = 'Получена новая оценка';
$string['grade_notification_message'] = 'Здравствуйте! Вы получили новую оценку {$grade} за {$activityname} в курсе "{$coursename}".';
$string['usernotfound'] = 'Пользователь не найден';
$string['noemail'] = 'У пользователя нет email-адреса';

$string['course_list'] = 'Список курсов';

$string['gradeitemnotfound'] = 'Элемент оценки не найден';

$string['messageprovider:grade_update'] = 'Уведомления об обновлении оценок';
$string['messageprovider:grade_update_subject'] = 'Новая оценка';
$string['messageprovider:grade_update_message'] = 'Уважаемый(ая) {$a->fullname}, вы получили оценку {$a->grade} за активность "{$a->activityname}" в курсе "{$a->coursename}".';

// Уведомления для заданий
$string['messageprovider:grade_update_assign_subject'] = 'Оценка за задание';
$string['messageprovider:grade_update_assign_message'] = 'ИНФОРМАЦИЯ ОБ ОБУЧАЮЩЕМСЯ. Код: {$a->fullname}. Здравствуйте! 
    Произведена проверка работы по "{$a->coursename}" педагогами "Академической гимназии". Отметка {$a->grade} ';

// Уведомления для тестов
$string['messageprovider:grade_update_quiz_subject'] = 'Результаты теста';
$string['messageprovider:grade_update_quiz_message'] = 'ИНФОРМАЦИЯ ОБ ОБУЧАЮЩЕМСЯ. Код: {$a->fullname}. Здравствуйте! 
    Подтверждаем получение выполненной работы. Работа по "{$a->coursename}" успешно зафиксирована. Оценка-{$a->grade} ';

// Уведомления для уроков
$string['messageprovider:grade_update_lesson_subject'] = 'Оценка за урок';
$string['messageprovider:grade_update_lesson_message'] = 'Уважаемый(ая) {$a->fullname}, вы получили оценку {$a->grade} за урок "{$a->activityname}" в курсе "{$a->coursename}".';

$string['accumulate_grades_single_task_name'] = 'Задача накопления оценок для одного пользователя';
$string['alt_pix_navigation'] = 'Иконка навигации';
