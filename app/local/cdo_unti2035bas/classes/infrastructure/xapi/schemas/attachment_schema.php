<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

/**
 * Схема вложения xAPI statement
 */
class attachment_schema {
    /** @readonly */
    public string $usageType;
    /** @readonly */
    public array $display;
    /** @readonly */
    public array $description;
    /** @readonly */
    public string $contentType;
    /** @readonly */
    public ?int $length;
    /** @readonly */
    public ?string $fileUrl;
    /** @readonly */
    public ?string $sha2;

    /**
     * @param array<string, string> $display
     * @param array<string, string> $description
     */
    public function __construct(
        string $usageType,
        array $display,
        array $description,
        string $contentType,
        ?int $length = null,
        ?string $fileUrl = null,
        ?string $sha2 = null
    ) {
        $this->usageType = $usageType;
        $this->display = $display;
        $this->description = $description;
        $this->contentType = $contentType;
        $this->length = $length;
        $this->fileUrl = $fileUrl;
        $this->sha2 = $sha2;
    }

    /**
     * Создает вложение для записи онлайн трансляции
     */
    public static function create_stream_recording(
        string $fileUrl,
        ?int $length = null,
        ?string $sha2 = null
    ): self {
        return new self(
            'http://id.tincanapi.com/attachment/supporting_media',
            ['ru-RU' => 'Записанная онлайн трансляция'],
            ['ru-RU' => 'Ссылка на запись онлайн трансляции'],
            'text/html',
            $length,
            $fileUrl,
            $sha2
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        $result = [
            'usageType' => $this->usageType,
            'display' => $this->display,
            'description' => $this->description,
            'contentType' => $this->contentType,
        ];

        if ($this->length !== null) {
            $result['length'] = $this->length;
        }

        if ($this->fileUrl !== null) {
            $result['fileUrl'] = $this->fileUrl;
        }

        if ($this->sha2 !== null) {
            $result['sha2'] = $this->sha2;
        }

        return $result;
    }
} 