<?php
namespace local_cdo_unti2035bas\video;

use local_cdo_unti2035bas\application\video\video_progress_create_use_case;
use local_cdo_unti2035bas\infrastructure\video\video_duration_service;
use local_cdo_unti2035bas\infrastructure\mediainfo\mediainfo_service;

/**
 * Фабрика для создания зависимостей видео модуля
 * Следует принципу Dependency Inversion
 */
class dependencies {
    
    private static ?video_duration_service $durationService = null;
    private static ?video_progress_create_use_case $createUseCase = null;
    
    /**
     * Получает сервис длительности видео (Singleton)
     */
    public static function getVideoDurationService(): video_duration_service {
        if (self::$durationService === null) {
            // Получаем mediainfo_service через dependencies_base
            $dependenciesBase = new \local_cdo_unti2035bas\dependencies_base();
            $mediainfoService = $dependenciesBase->get_mediainfo_service();
            
            self::$durationService = new video_duration_service($mediainfoService);
        }
        return self::$durationService;
    }
    
    /**
     * Получает use case для создания прогресса
     */
    public static function getVideoProgressCreateUseCase(): video_progress_create_use_case {
        if (self::$createUseCase === null) {
            self::$createUseCase = new video_progress_create_use_case(
                self::getVideoDurationService()
            );
        }
        return self::$createUseCase;
    }
} 