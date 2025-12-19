<?php

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

use local_cdo_unti2035bas\video_progress\handler;

// Получаем параметры из командной строки
list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
        'userid' => false,
        'cmid' => false,
        'cleanup' => false,
        'cleanup-days' => 90
    ),
    array(
        'h' => 'help',
        'u' => 'userid', 
        'c' => 'cmid',
        'd' => 'cleanup-days'
    )
);

if ($options['help']) {
    $help = "
Скрипт для отправки xAPI statements о просмотре видео

Опции:
-h, --help              Показать эту справку
-u, --userid=ID         Отправить данные только для конкретного пользователя
-c, --cmid=ID          Отправить данные только для конкретного модуля курса
    --cleanup          Очистить старые записи об отправленных statements
-d, --cleanup-days=N   Количество дней для хранения записей (по умолчанию 90)

Примеры:
    php send_video_xapi.php                    # Отправить все данные
    php send_video_xapi.php -u 123             # Отправить для пользователя 123
    php send_video_xapi.php -c 456             # Отправить для модуля 456
    php send_video_xapi.php -u 123 -c 456     # Отправить для пользователя 123 и модуля 456
    php send_video_xapi.php --cleanup         # Очистить старые записи
    php send_video_xapi.php --cleanup -d 30   # Очистить записи старше 30 дней
";
    echo $help;
    exit(0);
}

$handler = new handler();

// Очистка старых записей
if ($options['cleanup']) {
    echo "Очистка старых записей...\n";
    $dependencies = \local_cdo_unti2035bas\infrastructure\dependencies_base::get_instance();
    $xapi_sent_repo = $dependencies->get_xapi_sent_repository();
    
    $cleanup_days = $options['cleanup-days'];
    $deleted = $xapi_sent_repo->cleanup_old_records($cleanup_days);
    
    echo "Удалено $deleted старых записей (старше $cleanup_days дней)\n";
    exit(0);
}

echo "Начинаем отправку xAPI statements для видео...\n";

try {
    // Если указаны конкретные параметры
    if ($options['userid'] && $options['cmid']) {
        echo "Отправка для пользователя {$options['userid']} и модуля {$options['cmid']}...\n";
        $result = $handler->send_video_progress_for_user_module($options['userid'], $options['cmid']);
        
        if ($result) {
            echo "Результат: " . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "Ошибка при отправке\n";
        }
    } 
    // Если указан только пользователь
    else if ($options['userid']) {
        echo "Отправка данных для пользователя {$options['userid']}...\n";
        // Нужно реализовать метод для отправки всех данных пользователя
        echo "Функция отправки для конкретного пользователя пока не реализована\n";
        echo "Используйте комбинацию -u и -c для отправки конкретного модуля\n";
    }
    // Если указан только модуль  
    else if ($options['cmid']) {
        echo "Отправка данных для модуля {$options['cmid']}...\n";
        echo "Функция отправки для конкретного модуля пока не реализована\n";
        echo "Используйте комбинацию -u и -c для отправки конкретного пользователя\n";
    }
    // Массовая отправка всех данных
    else {
        echo "Массовая отправка всех данных...\n";
        $result = $handler->send_video_progress_statements();
        
        if ($result) {
            echo "Отправка завершена:\n";
            echo "- Отправлено: {$result['sent']}\n";
            echo "- Пропущено (дубликаты): {$result['skipped']}\n";  
            echo "- Ошибок: {$result['errors']}\n";
            echo "- Всего обработано: {$result['total']}\n";
        } else {
            echo "Ошибка при массовой отправке\n";
        }
    }
    
} catch (Exception $e) {
    echo "Критическая ошибка: " . $e->getMessage() . "\n";
    echo "Трассировка:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "Отправка завершена.\n"; 