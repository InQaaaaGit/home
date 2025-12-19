<?php
namespace local_cdo_unti2035bas\domain;

class validator {
    public static function validate_activity_type(string $type): bool {
        return in_array($type, ['video', 'webinar', 'article', 'practice']);
    }

    public static function validate_activity_admittance_form(?string $form): bool {
        return in_array($form, [null, 'online', 'offline', 'hybrid']);
    }

    public static function validate_duration(string $value): bool {
        return (bool)preg_match('/^P(?!$)(\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?$/', $value);
    }
}
