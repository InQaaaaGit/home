<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

/**
 * Схема объекта типа StatementRef для xAPI
 */
class statement_ref_object_schema {
    /** @readonly */
    public string $id;
    /** @readonly */
    public string $objectType;

    public function __construct(string $id) {
        $this->id = $id;
        $this->objectType = 'StatementRef';
    }

    /**
     * Создает StatementRef для видеоконтента методиста
     */
    public static function create_video_content_ref(string $contentId): self {
        return new self($contentId);
    }

    /**
     * Создает StatementRef для элемента оценки
     */
    public static function create_grade_item_ref(
        string $object_id,
        string $itemName = '',
        string $itemType = 'unknown',
        string $itemModule = 'unknown'
    ): self {
        return new self($object_id);
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return [
            'id' => $this->id,
            'objectType' => $this->objectType,
        ];
    }
} 