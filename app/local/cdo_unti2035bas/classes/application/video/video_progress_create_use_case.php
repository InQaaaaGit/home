<?php
namespace local_cdo_unti2035bas\application\video;

use local_cdo_unti2035bas\infrastructure\video\video_duration_service;

/**
 * Use case для создания записей видео прогресса
 * Следует принципу Single Responsibility - отвечает только за создание записей прогресса
 */
class video_progress_create_use_case {
    
    private video_duration_service $durationService;
    
    public function __construct(video_duration_service $durationService) {
        $this->durationService = $durationService;
    }
    
    /**
     * Создает запись видео прогресса с рандомным значением от 85% до 100%
     * 
     * @param int $userId ID пользователя
     * @param int $cmid ID модуля курса
     * @return video_progress_result результат создания
     * @throws \InvalidArgumentException если запись уже существует
     * @throws \moodle_exception при ошибке создания
     */
    public function execute(int $userId, int $cmid): video_progress_result {
        global $DB;
        
        // Проверяем, что еще нет записей для этого пользователя и модуля
        if ($this->progressAlreadyExists($userId, $cmid)) {
            throw new \InvalidArgumentException('Progress record already exists for this user and module');
        }
        
        // Генерируем рандомный прогресс от 85 до 100%
        $randomProgress = $this->generateRandomProgress();
        
        // Получаем реальную длительность видео
        $videoDuration = $this->durationService->getDuration($cmid);
        
        // Создаем запись
        $recordId = $this->createProgressRecord($userId, $cmid, $randomProgress, $videoDuration);
        
        return new video_progress_result(
            $recordId,
            $randomProgress,
            $videoDuration,
            true,
            'Progress created successfully'
        );
    }
    
    /**
     * Проверяет, существует ли уже запись прогресса
     */
    private function progressAlreadyExists(int $userId, int $cmid): bool {
        global $DB;
        
        return $DB->count_records('local_videoprogress', [
            'userid' => $userId,
            'cmid' => $cmid
        ]) > 0;
    }
    
    /**
     * Генерирует рандомный прогресс от 85.00% до 100.00%
     */
    private function generateRandomProgress(): float {
        return rand(8500, 10000) / 100;
    }
    
    /**
     * Создает запись в базе данных
     */
    private function createProgressRecord(int $userId, int $cmid, float $progress, int $duration): int {
        global $DB;
        
        $progressRecord = new \stdClass();
        $progressRecord->userid = $userId;
        $progressRecord->cmid = $cmid;
        $progressRecord->progress = $progress;
        $progressRecord->duration = $duration;
        $progressRecord->timecreated = time();
        $progressRecord->timemodified = time();
        
        $recordId = $DB->insert_record('local_videoprogress', $progressRecord);
        
        if (!$recordId) {
            throw new \moodle_exception('errorcreateprogress', 'local_cdo_unti2035bas');
        }
        
        return $recordId;
    }
}

/**
 * Value object для результата создания записи прогресса
 * Следует принципу immutability
 */
class video_progress_result {
    
    public readonly int $recordId;
    public readonly float $progress;
    public readonly int $duration;
    public readonly bool $success;
    public readonly string $message;
    
    public function __construct(int $recordId, float $progress, int $duration, bool $success, string $message) {
        $this->recordId = $recordId;
        $this->progress = $progress;
        $this->duration = $duration;
        $this->success = $success;
        $this->message = $message;
    }
    
    /**
     * Возвращает отформатированный прогресс
     */
    public function getFormattedProgress(): string {
        return round($this->progress, 1) . '%';
    }
    
    /**
     * Возвращает отформатированную длительность
     */
    public function getFormattedDuration(): string {
        return gmdate('H:i:s', $this->duration);
    }
} 