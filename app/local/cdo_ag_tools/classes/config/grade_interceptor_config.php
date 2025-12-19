<?php

namespace local_cdo_ag_tools\config;

/**
 * Grade Interceptor Configuration
 * 
 * Класс для настройки поведения перехватчика оценок
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class grade_interceptor_config
{
    /** @var bool Включен ли перехватчик оценок */
    public static bool $enabled = true;
    
    /** @var bool Логировать ли события оценивания */
    public static bool $log_events = true;
    
    /** @var float Порог для определения низкой оценки (в процентах) */
    public static float $low_grade_threshold = 40.0;
    
    /** @var float Порог для определения отличной оценки (в процентах) */
    public static float $excellent_grade_threshold = 95.0;
    
    /** @var bool Отправлять ли уведомления о низких оценках */
    public static bool $notify_low_grades = true;
    
    /** @var bool Отправлять ли уведомления об отличных оценках */
    public static bool $notify_excellent_grades = false;
    
    /** @var array Типы элементов оценки которые нужно обрабатывать */
    public static array $process_item_types = ['mod', 'manual'];
    
    /** @var array Модули которые нужно исключить из обработки */
    public static array $excluded_modules = [];
    
    /** @var bool Включить ли статистический анализ */
    public static bool $enable_statistics = true;
    
    /** @var bool Синхронизировать ли с внешними системами */
    public static bool $external_sync_enabled = false;
    
    /** @var string URL для внешней синхронизации */
    public static string $external_sync_url = '';
    
    /** @var string API ключ для внешней синхронизации */
    public static string $external_sync_api_key = '';
    
    // === Настройки интеграции с 1С ===
    
    /** @var bool Включена ли отправка оценок в 1С */
    public static bool $send_to_1c_enabled = true;
    
    /** @var string URL веб-сервиса 1С для отправки оценок */
    public static string $onec_webservice_url = '';
    
    /** @var string Логин для аутентификации в 1С */
    public static string $onec_username = '';
    
    /** @var string Пароль для аутентификации в 1С */
    public static string $onec_password = '';
    
    /** @var string Имя базы данных 1С */
    public static string $onec_database = '';
    
    /** @var bool Отправлять ли все оценки или только финальные */
    public static bool $onec_send_final_only = true;
    
    /** @var array Типы модулей оценки для отправки в 1С */
    public static array $onec_allowed_modules = ['assign', 'quiz', 'lesson', 'workshop'];
    
    /** @var float Минимальная оценка для отправки в 1С (процент) */
    public static float $onec_min_grade_threshold = 0.0;
    
    /** @var bool Логировать ли отправку в 1С */
    public static bool $onec_log_enabled = true;
    
    /** @var string Формат даты для отправки в 1С */
    public static string $onec_date_format = 'd.m.Y H:i:s';
    
    /** @var int Таймаут соединения с 1С (секунды) */
    public static int $onec_connection_timeout = 30;
    
    /** @var bool Отправлять ли оценки в реальном времени или пакетно */
    public static bool $onec_realtime_sync = true;
    
    /** @var string Расписание для пакетной отправки (cron выражение) */
    public static string $onec_batch_schedule = '0 2 * * *'; // Каждый день в 2:00
    
    /**
     * Получить все настройки
     * 
     * @return array Массив всех настроек
     */
    public static function get_all_settings(): array
    {
        return [
            'enabled' => self::$enabled,
            'log_events' => self::$log_events,
            'low_grade_threshold' => self::$low_grade_threshold,
            'excellent_grade_threshold' => self::$excellent_grade_threshold,
            'notify_low_grades' => self::$notify_low_grades,
            'notify_excellent_grades' => self::$notify_excellent_grades,
            'process_item_types' => self::$process_item_types,
            'excluded_modules' => self::$excluded_modules,
            'enable_statistics' => self::$enable_statistics,
            'external_sync_enabled' => self::$external_sync_enabled,
            'external_sync_url' => self::$external_sync_url,
            'external_sync_api_key' => self::$external_sync_api_key,
            // 1С настройки
            'send_to_1c_enabled' => self::$send_to_1c_enabled,
            'onec_webservice_url' => self::$onec_webservice_url,
            'onec_username' => self::$onec_username,
            'onec_password' => self::$onec_password,
            'onec_database' => self::$onec_database,
            'onec_send_final_only' => self::$onec_send_final_only,
            'onec_allowed_modules' => self::$onec_allowed_modules,
            'onec_min_grade_threshold' => self::$onec_min_grade_threshold,
            'onec_log_enabled' => self::$onec_log_enabled,
            'onec_date_format' => self::$onec_date_format,
            'onec_connection_timeout' => self::$onec_connection_timeout,
            'onec_realtime_sync' => self::$onec_realtime_sync,
            'onec_batch_schedule' => self::$onec_batch_schedule,
        ];
    }
    
    /**
     * Проверить нужно ли обрабатывать данный тип элемента оценки
     * 
     * @param string $item_type Тип элемента оценки
     * @return bool
     */
    public static function should_process_item_type(string $item_type): bool
    {
        return in_array($item_type, self::$process_item_types);
    }
    
    /**
     * Проверить исключен ли данный модуль
     * 
     * @param string $module_name Название модуля
     * @return bool
     */
    public static function is_module_excluded(string $module_name): bool
    {
        return in_array($module_name, self::$excluded_modules);
    }
    
    /**
     * Проверить является ли оценка низкой
     * 
     * @param float $grade_percentage Процент оценки
     * @return bool
     */
    public static function is_low_grade(float $grade_percentage): bool
    {
        return $grade_percentage < self::$low_grade_threshold;
    }
    
    /**
     * Проверить является ли оценка отличной
     * 
     * @param float $grade_percentage Процент оценки
     * @return bool
     */
    public static function is_excellent_grade(float $grade_percentage): bool
    {
        return $grade_percentage >= self::$excellent_grade_threshold;
    }
    
    /**
     * Проверить включена ли отправка в 1С
     * 
     * @return bool
     */
    public static function is_1c_sync_enabled(): bool
    {
        return self::$send_to_1c_enabled;
    }
    
    /**
     * Проверить нужно ли отправлять данный модуль в 1С
     * 
     * @param string $module_name Название модуля
     * @return bool
     */
    public static function should_send_module_to_1c(string $module_name): bool
    {
        return in_array($module_name, self::$onec_allowed_modules);
    }
    
    /**
     * Проверить достаточна ли оценка для отправки в 1С
     * 
     * @param float $grade_percentage Процент оценки
     * @return bool
     */
    public static function is_grade_eligible_for_1c(float $grade_percentage): bool
    {
        return $grade_percentage >= self::$onec_min_grade_threshold;
    }
    
    /**
     * Получить параметры подключения к 1С
     * 
     * @return array Параметры подключения
     */
    public static function get_1c_connection_params(): array
    {
        return [
            'url' => self::$onec_webservice_url,
            'username' => self::$onec_username,
            'password' => self::$onec_password,
            'database' => self::$onec_database,
            'timeout' => self::$onec_connection_timeout
        ];
    }
    
    /**
     * Загрузить настройки из admin settings (если они определены)
     * 
     * @return void
     */
    public static function load_from_settings(): void
    {
        // Загружаем настройки из Moodle admin settings если они определены
        self::$enabled = get_config('local_cdo_ag_tools', 'grade_interceptor_enabled') ?? self::$enabled;
        self::$log_events = get_config('local_cdo_ag_tools', 'grade_interceptor_log_events') ?? self::$log_events;
        
        $low_threshold = get_config('local_cdo_ag_tools', 'grade_interceptor_low_threshold');
        if ($low_threshold !== false) {
            self::$low_grade_threshold = floatval($low_threshold);
        }
        
        $excellent_threshold = get_config('local_cdo_ag_tools', 'grade_interceptor_excellent_threshold');
        if ($excellent_threshold !== false) {
            self::$excellent_grade_threshold = floatval($excellent_threshold);
        }
        
        self::$notify_low_grades = get_config('local_cdo_ag_tools', 'grade_interceptor_notify_low') ?? self::$notify_low_grades;
        self::$notify_excellent_grades = get_config('local_cdo_ag_tools', 'grade_interceptor_notify_excellent') ?? self::$notify_excellent_grades;
        self::$enable_statistics = get_config('local_cdo_ag_tools', 'grade_interceptor_statistics') ?? self::$enable_statistics;
        self::$external_sync_enabled = get_config('local_cdo_ag_tools', 'grade_interceptor_external_sync') ?? self::$external_sync_enabled;
        
        $sync_url = get_config('local_cdo_ag_tools', 'grade_interceptor_sync_url');
        if ($sync_url !== false) {
            self::$external_sync_url = $sync_url;
        }
        
        $api_key = get_config('local_cdo_ag_tools', 'grade_interceptor_api_key');
        if ($api_key !== false) {
            self::$external_sync_api_key = $api_key;
        }
        
        // Загружаем настройки 1С
        self::$send_to_1c_enabled = get_config('local_cdo_ag_tools', 'onec_sync_enabled') ?? self::$send_to_1c_enabled;
        
        $onec_url = get_config('local_cdo_ag_tools', 'onec_webservice_url');
        if ($onec_url !== false) {
            self::$onec_webservice_url = $onec_url;
        }
        
        $onec_username = get_config('local_cdo_ag_tools', 'onec_username');
        if ($onec_username !== false) {
            self::$onec_username = $onec_username;
        }
        
        $onec_password = get_config('local_cdo_ag_tools', 'onec_password');
        if ($onec_password !== false) {
            self::$onec_password = $onec_password;
        }
        
        $onec_database = get_config('local_cdo_ag_tools', 'onec_database');
        if ($onec_database !== false) {
            self::$onec_database = $onec_database;
        }
        
        self::$onec_send_final_only = get_config('local_cdo_ag_tools', 'onec_send_final_only') ?? self::$onec_send_final_only;
        
        $onec_min_threshold = get_config('local_cdo_ag_tools', 'onec_min_grade_threshold');
        if ($onec_min_threshold !== false) {
            self::$onec_min_grade_threshold = floatval($onec_min_threshold);
        }
        
        self::$onec_log_enabled = get_config('local_cdo_ag_tools', 'onec_log_enabled') ?? self::$onec_log_enabled;
        self::$onec_realtime_sync = get_config('local_cdo_ag_tools', 'onec_realtime_sync') ?? self::$onec_realtime_sync;
        
        $onec_timeout = get_config('local_cdo_ag_tools', 'onec_connection_timeout');
        if ($onec_timeout !== false) {
            self::$onec_connection_timeout = intval($onec_timeout);
        }
        
        $onec_batch_schedule = get_config('local_cdo_ag_tools', 'onec_batch_schedule');
        if ($onec_batch_schedule !== false) {
            self::$onec_batch_schedule = $onec_batch_schedule;
        }
    }
    
    /**
     * Получить настройки для admin settings форм
     * 
     * @return array Массив настроек для admin_settingpage
     */
    public static function get_admin_settings(): array
    {
        return [
            'grade_interceptor_enabled' => [
                'type' => 'advcheckbox',
                'default' => self::$enabled,
                'name' => 'grade_interceptor_enabled',
                'title' => 'Включить Grade Interceptor',
                'description' => 'Включить перехват и обработку событий оценивания'
            ],
            'grade_interceptor_log_events' => [
                'type' => 'advcheckbox', 
                'default' => self::$log_events,
                'name' => 'grade_interceptor_log_events',
                'title' => 'Логировать события',
                'description' => 'Записывать события оценивания в лог для отладки'
            ],
            'grade_interceptor_low_threshold' => [
                'type' => 'text',
                'default' => self::$low_grade_threshold,
                'name' => 'grade_interceptor_low_threshold',
                'title' => 'Порог низкой оценки (%)',
                'description' => 'Процент ниже которого оценка считается низкой'
            ],
            'grade_interceptor_excellent_threshold' => [
                'type' => 'text',
                'default' => self::$excellent_grade_threshold,
                'name' => 'grade_interceptor_excellent_threshold',
                'title' => 'Порог отличной оценки (%)',
                'description' => 'Процент выше которого оценка считается отличной'
            ],
            'grade_interceptor_notify_low' => [
                'type' => 'advcheckbox',
                'default' => self::$notify_low_grades,
                'name' => 'grade_interceptor_notify_low',
                'title' => 'Уведомлять о низких оценках',
                'description' => 'Отправлять уведомления преподавателям о низких оценках студентов'
            ],
            'grade_interceptor_notify_excellent' => [
                'type' => 'advcheckbox',
                'default' => self::$notify_excellent_grades,
                'name' => 'grade_interceptor_notify_excellent',
                'title' => 'Уведомлять об отличных оценках',
                'description' => 'Отправлять уведомления об отличных достижениях студентов'
            ],
            'grade_interceptor_statistics' => [
                'type' => 'advcheckbox',
                'default' => self::$enable_statistics,
                'name' => 'grade_interceptor_statistics',
                'title' => 'Включить статистику',
                'description' => 'Собирать и анализировать статистические данные об оценках'
            ],
            'grade_interceptor_external_sync' => [
                'type' => 'advcheckbox',
                'default' => self::$external_sync_enabled,
                'name' => 'grade_interceptor_external_sync',
                'title' => 'Внешняя синхронизация',
                'description' => 'Синхронизировать оценки с внешними системами'
            ],
            'grade_interceptor_sync_url' => [
                'type' => 'text',
                'default' => self::$external_sync_url,
                'name' => 'grade_interceptor_sync_url',
                'title' => 'URL для синхронизации',
                'description' => 'URL API внешней системы для отправки данных об оценках'
            ],
            'grade_interceptor_api_key' => [
                'type' => 'passwordunmask',
                'default' => self::$external_sync_api_key,
                'name' => 'grade_interceptor_api_key',
                'title' => 'API ключ',
                'description' => 'API ключ для аутентификации во внешней системе'
            ],
            
            // === Настройки интеграции с 1С ===
            
            'onec_sync_enabled' => [
                'type' => 'advcheckbox',
                'default' => self::$send_to_1c_enabled,
                'name' => 'onec_sync_enabled',
                'title' => 'Включить отправку оценок в 1С',
                'description' => 'Автоматически отправлять выставленные оценки в систему 1С'
            ],
            'onec_webservice_url' => [
                'type' => 'text',
                'default' => self::$onec_webservice_url,
                'name' => 'onec_webservice_url',
                'title' => 'URL веб-сервиса 1С',
                'description' => 'Полный URL веб-сервиса 1С для приема данных об оценках (например: http://server:port/base/ws/grades.1cws)'
            ],
            'onec_username' => [
                'type' => 'text',
                'default' => self::$onec_username,
                'name' => 'onec_username',
                'title' => 'Логин 1С',
                'description' => 'Имя пользователя для аутентификации в базе 1С'
            ],
            'onec_password' => [
                'type' => 'passwordunmask',
                'default' => self::$onec_password,
                'name' => 'onec_password',
                'title' => 'Пароль 1С',
                'description' => 'Пароль пользователя для аутентификации в базе 1С'
            ],
            'onec_database' => [
                'type' => 'text',
                'default' => self::$onec_database,
                'name' => 'onec_database',
                'title' => 'Имя базы данных 1С',
                'description' => 'Название информационной базы 1С (если требуется)'
            ],
            'onec_send_final_only' => [
                'type' => 'advcheckbox',
                'default' => self::$onec_send_final_only,
                'name' => 'onec_send_final_only',
                'title' => 'Отправлять только финальные оценки',
                'description' => 'Если включено, в 1С будут отправляться только окончательные оценки (не промежуточные)'
            ],
            'onec_min_grade_threshold' => [
                'type' => 'text',
                'default' => self::$onec_min_grade_threshold,
                'name' => 'onec_min_grade_threshold',
                'title' => 'Минимальная оценка для отправки (%)',
                'description' => 'Оценки ниже этого порога не будут отправляться в 1С (0 = отправлять все оценки)'
            ],
            'onec_log_enabled' => [
                'type' => 'advcheckbox',
                'default' => self::$onec_log_enabled,
                'name' => 'onec_log_enabled',
                'title' => 'Логировать отправку в 1С',
                'description' => 'Записывать в лог все операции отправки данных в 1С'
            ],
            'onec_connection_timeout' => [
                'type' => 'text',
                'default' => self::$onec_connection_timeout,
                'name' => 'onec_connection_timeout',
                'title' => 'Таймаут соединения с 1С (сек)',
                'description' => 'Максимальное время ожидания ответа от веб-сервиса 1С'
            ],
            'onec_realtime_sync' => [
                'type' => 'advcheckbox',
                'default' => self::$onec_realtime_sync,
                'name' => 'onec_realtime_sync',
                'title' => 'Отправка в реальном времени',
                'description' => 'Отправлять оценки в 1С немедленно при выставлении (иначе пакетно по расписанию)'
            ],
            'onec_batch_schedule' => [
                'type' => 'text',
                'default' => self::$onec_batch_schedule,
                'name' => 'onec_batch_schedule',
                'title' => 'Расписание пакетной отправки',
                'description' => 'Cron выражение для пакетной отправки данных в 1С (например: "0 2 * * *" = каждый день в 2:00)'
            ]
        ];
    }
} 