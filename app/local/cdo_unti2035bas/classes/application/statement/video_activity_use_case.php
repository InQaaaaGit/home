<?php
namespace local_cdo_unti2035bas\application\statement;

use local_cdo_unti2035bas\infrastructure\xapi\client;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\video_statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\attachment_schema;

/**
 * Use case для отправки цифровых следов просмотра видео
 */
class video_activity_use_case {
    private client $xapiClient;

    public function __construct(client $xapiClient) {
        $this->xapiClient = $xapiClient;
    }

    /**
     * Отправляет цифровой след просмотра видео
     */
    public function send_video_watch_statement(
        string $untiId,
        string $contentId,
        string $duration,
        int $viewPercentage,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        bool $watchedRecording = false,
        ?string $recordingUrl = null,
        ?int $recordingSize = null,
        ?string $recordingHash = null
    ): array {
        // Создаем attachment для записи если есть URL
        $attachment = null;
//        if ($recordingUrl !== null) {
//            $attachment = attachment_schema::create_stream_recording(
//                $recordingUrl,
//                $recordingSize,
//                $recordingHash
//            );
//        }

        // Создаем statement для просмотра видео
        $statement = video_statement_schema::create_video_watch_statement(
            $untiId,
            $contentId,
            $duration,
            $viewPercentage,
            $untiCourseId,
            $untiFlowId,
            $moduleNumber,
            $watchedRecording,
            $attachment
        );

        try {
            // Отправляем statement на xAPI сервер
            $result = $this->xapiClient->send([$statement]);
            
            // Логируем успешную отправку
            debugging(
                "Video watch xAPI statement sent successfully. Content ID: {$contentId}, User: {$untiId}",
                DEBUG_DEVELOPER
            );
            
            return $result;
            
        } catch (\Exception $e) {
            // Логируем ошибку
            debugging(
                "Failed to send video watch xAPI statement. Content ID: {$contentId}, User: {$untiId}, Error: " . $e->getMessage(),
                DEBUG_DEVELOPER
            );
            
            // Переброс исключения для обработки на верхнем уровне
            throw $e;
        }
    }

    /**
     * Преобразует секунды в ISO 8601 duration формат (PT14M8S)
     */
    public static function seconds_to_duration(int|float $seconds): string {
        // Используем floor() для более точного вычисления часов
        $hours = (int)floor($seconds / 3600);
        
        // Вычисляем оставшиеся секунды после вычитания часов
        $remainingSecondsAfterHours = $seconds - ($hours * 3600);
        
        // Используем floor() для более точного вычисления минут
        $minutes = (int)floor($remainingSecondsAfterHours / 60);
        
        // Вычисляем оставшиеся секунды после вычитания минут
        $remainingSeconds = (int)round($remainingSecondsAfterHours - ($minutes * 60));

        $duration = 'PT';
        
        if ($hours > 0) {
            $duration .= $hours . 'H';
        }
        
        if ($minutes > 0) {
            $duration .= $minutes . 'M';
        }
        
        if ($remainingSeconds > 0) {
            $duration .= $remainingSeconds . 'S';
        }

        // Если duration пустой, возвращаем PT0S
        return $duration === 'PT' ? 'PT0S' : $duration;
    }

    /**
     * Вычисляет процент просмотра
     */
    public static function calculate_view_percentage(int $watchedSeconds, int $totalSeconds): int {
        if ($totalSeconds <= 0) {
            return 0;
        }
        
        return min(100, round(($watchedSeconds / $totalSeconds) * 100));
    }
} 