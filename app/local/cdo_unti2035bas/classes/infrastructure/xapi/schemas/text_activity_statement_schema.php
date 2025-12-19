<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

use DateTime;

/**
 * Схема statement для цифровых следов текстовых активностей (просмотр/скачивание контента)
 */
class text_activity_statement_schema {
    /** @readonly */
    public ?string $id;
    /** @readonly */
    public DateTime $timestamp;
    /** @readonly */
    public agent_schema $actor;
    /** @readonly */
    public verb_schema $verb;
    /** @readonly */
    public statement_ref_object_schema $object;
    /** @readonly */
    public ?context_schema $context;

    public function __construct(
        ?string $id,
        DateTime $timestamp,
        agent_schema $actor,
        verb_schema $verb,
        statement_ref_object_schema $object,
        ?context_schema $context = null
    ) {
        $this->id = $id;
        $this->timestamp = $timestamp;
        $this->actor = $actor;
        $this->verb = $verb;
        $this->object = $object;
        $this->context = $context;
    }

    /**
     * Создает statement для просмотра текстового контента
     */
    public static function create_text_viewed_statement(
        string $untiId,
        string $objectId,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber = null,
        int $timestamp = null
    ): self {
        return self::create_text_activity_statement(
            $untiId,
            $objectId,
            'http://id.tincanapi.com/verb/viewed',
            $untiCourseId,
            $untiFlowId,
            $moduleNumber,
            $timestamp
        );
    }

    /**
     * Создает statement для скачивания текстового контента
     */
    public static function create_text_downloaded_statement(
        string $untiId,
        string $objectId,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber = null,
        int $timestamp = null
    ): self {
        return self::create_text_activity_statement(
            $untiId,
            $objectId,
            'http://id.tincanapi.com/verb/downloaded',
            $untiCourseId,
            $untiFlowId,
            $moduleNumber,
            $timestamp
        );
    }

    /**
     * Создает statement для завершения текстовой активности (общий метод)
     */
    public static function create_text_completed_statement(
        string $untiId,
        string $objectId,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber = null,
        int $timestamp = null
    ): self {
        return self::create_text_activity_statement(
            $untiId,
            $objectId,
            'http://id.tincanapi.com/verb/viewed',
            $untiCourseId,
            $untiFlowId,
            $moduleNumber,
            $timestamp
        );
    }

    /**
     * Внутренний метод для создания statement текстовой активности
     */
    private static function create_text_activity_statement(
        string $untiId,
        string $objectId,
        string $verbId,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber = null,
        int $timestamp = null
    ): self {
        // Создаем actor
        $actorAccount = new agent_account_schema('https://my.2035.university', $untiId);
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema($verbId);

        // Создаем object как StatementRef
        $object = new statement_ref_object_schema($objectId);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
        ];

        if (!empty($moduleNumber)) {
            $contextExtensions['https://api.2035.university/module_num'] = $moduleNumber;
        }

        $context = new context_schema(null, $contextExtensions);

        if (empty($timestamp)) {
            $timestamp = time();
        }

        return new self(
            null, // ID будет сгенерирован автоматически
            new DateTime('@' . $timestamp),
            $actor,
            $verb,
            $object,
            $context
        );
    }

    /**
     * Экспортирует statement в массив для отправки
     * 
     * @return array<string, mixed>
     */
    public function dump(): array {
        return array_filter([
            'id' => $this->id,
            'version' => '1.0.3',
            'timestamp' => $this->timestamp->format(DateTime::ATOM),
            'actor' => $this->actor->dump(),
            'verb' => $this->verb->dump(),
            'object' => $this->object->dump(),
            'context' => $this->context ? $this->context->dump() : null,
        ]);
    }
} 