<?php
namespace local_cdo_unti2035bas\infrastructure\video;

use context_module;
use local_cdo_unti2035bas\infrastructure\mediainfo\mediainfo_service;
use local_cdo_unti2035bas\exceptions\exec_error;

/**
 * Сервис для получения длительности видео из различных источников
 * Следует принципу Single Responsibility - отвечает только за получение длительности
 */
class video_duration_service {
    
    private array $durationProviders;
    private mediainfo_service $mediainfoservice;
    
    public function __construct(mediainfo_service $mediainfoservice) {
        $this->mediainfoservice = $mediainfoservice;
        
        // Инициализируем провайдеров в порядке приоритета
        $this->durationProviders = [
            new mediainfo_duration_provider($this->mediainfoservice),
            new videoplayer_duration_provider(),
            new videoprogress_duration_provider(),
            new resource_file_duration_provider(),
            new url_duration_provider(),
            new default_duration_provider()
        ];
    }
    
    /**
     * Получает длительность видео в секундах
     * 
     * @param int $cmid ID модуля курса
     * @return int длительность в секундах
     */
    public function getDuration(int $cmid): int {
        foreach ($this->durationProviders as $provider) {
            $duration = $provider->getDuration($cmid);
            if ($duration > 0) {
                debugging("Video duration for cmid {$cmid}: {$duration} seconds from " . get_class($provider), DEBUG_DEVELOPER);
                return $duration;
            }
        }
        
        // Если ни один провайдер не смог определить длительность
        debugging("Could not determine video duration for cmid {$cmid}, using default", DEBUG_DEVELOPER);
        return 3600; // 1 час по умолчанию
    }
}

/**
 * Интерфейс для провайдеров длительности видео
 * Следует принципу Interface Segregation
 */
interface duration_provider_interface {
    /**
     * Получает длительность видео
     * 
     * @param int $cmid ID модуля курса
     * @return int длительность в секундах или 0 если не может определить
     */
    public function getDuration(int $cmid): int;
}

/**
 * Провайдер для получения длительности через mediainfo (наиболее точный)
 */
class mediainfo_duration_provider implements duration_provider_interface {
    
    private mediainfo_service $mediainfoservice;
    
    public function __construct(mediainfo_service $mediainfoservice) {
        $this->mediainfoservice = $mediainfoservice;
    }
    
    public function getDuration(int $cmid): int {
        try {
            // Используем официальный API Moodle для получения информации о модуле
            $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                return 0;
            }
            
            // Получаем файлы из различных типов модулей
            $context = context_module::instance($cmid);
            $fs = get_file_storage();
            
            // Проверяем файлы в зависимости от типа модуля
            $fileareas = $this->getFileAreasForModule($cm->modname);
            
            foreach ($fileareas as $filearea) {
                $files = $fs->get_area_files($context->id, $filearea['component'], $filearea['filearea'], 0);
                
                foreach ($files as $file) {
                    if ($file->get_filename() === '.') {
                        continue;
                    }
                    
                    $mimetype = $file->get_mimetype();
                    if (strpos($mimetype, 'video/') === 0) {
                        // Получаем полный путь к файлу через временную копию
                        $filepath = $file->copy_content_to_temp();
                        
                        try {
                            $duration = $this->mediainfoservice->get_duration($filepath);
                            if ($duration > 0) {
                                return $duration;
                            }
                        } finally {
                            // Всегда удаляем временный файл
                            if (file_exists($filepath)) {
                                unlink($filepath);
                            }
                        }
                    }
                }
            }
            
            return 0;
            
        } catch (exec_error $e) {
            debugging("Mediainfo exec error for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        } catch (\Exception $e) {
            debugging("Error in mediainfo_duration_provider for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        }
    }
    
    /**
     * Получает области файлов для различных типов модулей
     * 
     * @param string $modname Имя модуля
     * @return array Массив областей файлов
     */
    private function getFileAreasForModule(string $modname): array {
        switch ($modname) {
            case 'resource':
                return [
                    ['component' => 'mod_resource', 'filearea' => 'content']
                ];
            case 'videoplayer':
                return [
                    ['component' => 'mod_videoplayer', 'filearea' => 'videos']
                ];
            case 'folder':
                return [
                    ['component' => 'mod_folder', 'filearea' => 'content']
                ];
            default:
                return [
                    ['component' => 'mod_' . $modname, 'filearea' => 'content']
                ];
        }
    }
}

/**
 * Провайдер для получения длительности из таблицы videoplayer
 */
class videoplayer_duration_provider implements duration_provider_interface {
    
    public function getDuration(int $cmid): int {
        global $DB;
        
        try {
            // Используем официальный API Moodle
            $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
            if (!$cm || $cm->modname !== 'videoplayer') {
                return 0;
            }
            
            $duration = $DB->get_field('videoplayer', 'duration', ['id' => $cm->instance]);
            return ($duration && $duration > 0) ? (int)$duration : 0;
            
        } catch (\Exception $e) {
            debugging("Error in videoplayer_duration_provider for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        }
    }
}

/**
 * Провайдер для получения длительности из существующих записей local_videoprogress
 */
class videoprogress_duration_provider implements duration_provider_interface {
    
    public function getDuration(int $cmid): int {
        global $DB;
        
        try {
            $duration = $DB->get_field_sql(
                "SELECT MAX(duration) FROM {local_videoprogress} WHERE cmid = ? AND duration > 0",
                [$cmid]
            );
            return $duration ? (int)$duration : 0;
            
        } catch (\Exception $e) {
            debugging("Error in videoprogress_duration_provider for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        }
    }
}

/**
 * Провайдер для получения длительности из файлов ресурсов
 */
class resource_file_duration_provider implements duration_provider_interface {
    
    public function getDuration(int $cmid): int {
        try {
            // Используем официальный API Moodle для получения информации о модуле
            $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
            if (!$cm || $cm->modname !== 'resource') {
                return 0;
            }
            
            $context = context_module::instance($cmid);
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0);
            
            foreach ($files as $file) {
                if ($file->get_filename() === '.') {
                    continue;
                }
                
                $mimetype = $file->get_mimetype();
                if (strpos($mimetype, 'video/') === 0) {
                    return $this->estimateDurationFromFilesize($file->get_filesize());
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            debugging("Error in resource_file_duration_provider for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        }
    }
    
    private function estimateDurationFromFilesize(int $filesize): int {
        // Эвристика: 1MB ≈ 10 секунд видео среднего качества
        // Ограничиваем от 5 минут до 2 часов
        $estimatedDuration = $filesize / (1024 * 1024) * 10;
        return (int)max(300, min(7200, $estimatedDuration));
    }
}

/**
 * Провайдер для получения длительности из URL модулей
 */
class url_duration_provider implements duration_provider_interface {
    
    public function getDuration(int $cmid): int {
        global $DB;
        
        try {
            // Используем официальный API Moodle
            $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
            if (!$cm || $cm->modname !== 'url') {
                return 0;
            }
            
            $url = $DB->get_field('url', 'externalurl', ['id' => $cm->instance]);
            
            if ($url) {
                return $this->estimateDurationFromUrl($url);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            debugging("Error in url_duration_provider for cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return 0;
        }
    }
    
    private function estimateDurationFromUrl(string $url): int {
        // YouTube видео
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return rand(60, 3600); // 1-60 минут
        }
        
        // Vimeo видео
        if (strpos($url, 'vimeo.com') !== false) {
            return rand(300, 2400); // 5-40 минут
        }
        
        // Прямые ссылки на видео файлы
        if (preg_match('/\.(mp4|webm|mov|avi|mkv)$/i', $url)) {
            return rand(600, 3600); // 10-60 минут
        }
        
        return 0;
    }
}

/**
 * Провайдер по умолчанию - всегда возвращает стандартное значение
 */
class default_duration_provider implements duration_provider_interface {
    
    public function getDuration(int $cmid): int {
        return 3600; // 1 час
    }
} 