<?php
namespace local_cdo_unti2035bas\infrastructure\moodle;

/**
 * Сервис для работы с кастомными полями пользователей
 * Использует официальные функции Moodle Core
 */
class user_field_service {
    
    /**
     * Получает значение кастомного поля пользователя
     * 
     * @param int $userid ID пользователя
     * @param string $fieldname Короткое имя поля
     * @return string|null Значение поля или null если не найдено
     */
    public static function get_user_field_value(int $userid, string $fieldname): ?string {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        
        if (empty($fieldname) || $userid < 1) {
            return null;
        }
        
        try {
            // Получаем данные профиля пользователя используя официальную функцию Moodle
            $userprofile = profile_user_record($userid, false);
            
            // Проверяем есть ли такое поле в профиле
            if (isset($userprofile->{$fieldname}) && !empty($userprofile->{$fieldname})) {
                return trim((string)$userprofile->{$fieldname});
            }
            
            return null;
            
        } catch (\Exception $e) {
            debugging("Ошибка получения поля '{$fieldname}' для пользователя {$userid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
    }
    
    /**
     * Получает UNTI ID пользователя из настроенного кастомного поля
     * 
     * @param int $userid ID пользователя
     * @return string UNTI ID или user ID как fallback
     */
    public static function get_unti_id(int $userid): string {
        // Получаем настройку для имени кастомного поля
        $fieldname = get_config('local_cdo_unti2035bas', 'unti_user_field');
        
        if (empty($fieldname)) {
            // Если поле не настроено, используем ID пользователя
            debugging("UNTI user field не настроено, используем user ID {$userid}", DEBUG_DEVELOPER);
            return (string)$userid;
        }
        
        $untiId = self::get_user_field_value($userid, $fieldname);
        
        if ($untiId !== null) {
            debugging("Получен UNTI ID '{$untiId}' из поля '{$fieldname}' для пользователя {$userid}", DEBUG_DEVELOPER);
            return $untiId;
        }
        
        // Fallback к user ID если поле пустое или не найдено
        debugging("UNTI поле '{$fieldname}' пусто или не найдено для пользователя {$userid}, используем user ID", DEBUG_DEVELOPER);
        return (string)$userid;
    }
    
    /**
     * Проверяет существование кастомного поля пользователя
     * 
     * @param string $fieldname Короткое имя поля
     * @return bool
     */
    public static function field_exists(string $fieldname): bool {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        
        if (empty($fieldname)) {
            return false;
        }
        
        try {
            // Используем официальную функцию Moodle для получения информации о поле
            $fielddata = profile_get_custom_field_data_by_shortname($fieldname);
            return $fielddata !== null;
        } catch (\Exception $e) {
            debugging("Ошибка проверки существования поля '{$fieldname}': " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }
    
    /**
     * Получает все кастомные поля пользователя
     * 
     * @param int $userid ID пользователя  
     * @return array Ассоциативный массив [shortname => value]
     */
    public static function get_all_user_fields(int $userid): array {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        
        if ($userid < 1) {
            return [];
        }
        
        try {
            // Получаем все данные профиля используя официальную функцию
            $userprofile = profile_user_record($userid, false);
            return (array)$userprofile;
        } catch (\Exception $e) {
            debugging("Ошибка получения всех полей для пользователя {$userid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return [];
        }
    }
} 