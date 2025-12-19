<?php

namespace local_cdo_certification_sheet\services;

defined('MOODLE_INTERNAL') || die();

/**
 * Сервис для работы с настройками Vue компонентов
 */
class vue_settings {

    /**
     * Проверяет, включены ли Vue компоненты
     *
     * @return bool
     */
    public static function is_vue_enabled(): bool {
        return (bool)get_config('local_cdo_certification_sheet', 'enable_vue_components');
    }

    /**
     * Возвращает настройку для Vue компонентов с возможностью переопределения
     *
     * @param bool|null $override Переопределение настройки (если null - используется системная настройка)
     * @return bool
     */
    public static function get_vue_setting(?bool $override = null): bool {
        if ($override !== null) {
            return $override;
        }
        return self::is_vue_enabled();
    }

    /**
     * Проверяет, можно ли использовать Vue компоненты для рендеринга
     *
     * @param array $context_data Данные контекста
     * @return bool
     */
    public static function can_use_vue(array $context_data = []): bool {
        $vue_enabled = self::is_vue_enabled();
        
        // Дополнительные проверки можно добавить здесь
        // Например, проверка наличия необходимых данных для Vue
        
        return $vue_enabled;
    }
}
