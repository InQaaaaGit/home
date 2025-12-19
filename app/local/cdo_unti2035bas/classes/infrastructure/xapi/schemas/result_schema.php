<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

/**
 * Схема результата xAPI statement
 */
class result_schema {
    /** @readonly */
    public ?string $duration;
    /** @readonly */
    public ?float $score;
    /** @readonly */
    public ?bool $completion;
    /** @readonly */
    public ?bool $success;
    /** @readonly */
    public ?float $scoreRaw;
    /** @readonly */
    public ?float $scoreMin;
    /** @readonly */
    public ?float $scoreMax;
    /** @readonly */
    public ?float $scoreScaled;
    /**
     * @readonly
     * @var array<string, mixed>
     */
    public array $extensions;

    /**
     * @param array<string, mixed> $extensions
     */
    public function __construct(
        ?string $duration = null,
        ?float $score = null,
        ?bool $completion = null,
        ?bool $success = null,
        ?float $scoreRaw = null,
        ?float $scoreMin = null,
        ?float $scoreMax = null,
        ?float $scoreScaled = null,
        array $extensions = []
    ) {
        $this->duration = $duration;
        $this->score = $score;
        $this->completion = $completion;
        $this->success = $success;
        $this->scoreRaw = $scoreRaw;
        $this->scoreMin = $scoreMin;
        $this->scoreMax = $scoreMax;
        $this->scoreScaled = $scoreScaled;
        $this->extensions = $extensions;
    }

    /**
     * Создает result для просмотра видео
     */
    public static function create_video_result(string $duration, int $viewPercentage): self {
        return new self(
            $duration,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            [
                'https://id.2035.university/xapi/extension/view-percentage' => $viewPercentage
            ]
        );
    }

    /**
     * Создает result для оценки с полной структурой
     */
    public static function create_grade_result_full(
        float $raw,
        float $min,
        float $max,
        float $scaled,
        bool $success,
        ?string $duration = null,
        array $extensions = []
    ): self {
        return new self(
            $duration,
            $scaled, // Используем scaled как основной score
            null, // completion
            $success,
            $raw,
            $min,
            $max,
            $scaled,
            $extensions
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        $result = [];
        
        if ($this->duration !== null) {
            $result['duration'] = $this->duration;
        }
        
        // Создаем полную структуру score если есть все компоненты
        if ($this->scoreRaw !== null && $this->scoreMin !== null && $this->scoreMax !== null && $this->scoreScaled !== null) {
            $result['score'] = [
                'raw' => $this->scoreRaw,
                'min' => $this->scoreMin,
                'max' => $this->scoreMax,
                'scaled' => $this->scoreScaled
            ];
        } else if ($this->score !== null) {
            $result['score'] = ['raw' => $this->score];
        }
        
        if ($this->completion !== null) {
            $result['completion'] = $this->completion;
        }
        
        if ($this->success !== null) {
            $result['success'] = $this->success;
        }
        
        if (!empty($this->extensions)) {
            $result['extensions'] = array_filter($this->extensions, fn($v) => !is_null($v));
        }
        
        return array_filter($result);
    }
} 