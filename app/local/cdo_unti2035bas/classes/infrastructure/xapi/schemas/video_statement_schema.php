<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

use DateTime;

/**
 * Расширенная схема statement для цифровых следов просмотра видео
 */
class video_statement_schema {
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
    public ?result_schema $result;
    /** @readonly */
    public ?context_schema $context;
    /**
     * @readonly
     * @var array<attachment_schema>
     */
    public array $attachments;

    /**
     * @param array<attachment_schema> $attachments
     */
    public function __construct(
        ?string $id,
        DateTime $timestamp,
        agent_schema $actor,
        verb_schema $verb,
        statement_ref_object_schema $object,
        ?result_schema $result = null,
        ?context_schema $context = null,
        array $attachments = []
    ) {
        $this->id = $id;
        $this->timestamp = $timestamp;
        $this->actor = $actor;
        $this->verb = $verb;
        $this->object = $object;
        $this->result = $result;
        $this->context = $context;
        $this->attachments = $attachments;
    }

    /**
     * Создает statement для просмотра видео
     */
    public static function create_video_watch_statement(
        string $untiId,
        string $contentId,
        string $duration,
        int $viewPercentage,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        bool $watchedRecording = false,
        ?attachment_schema $attachment = null
    ): self {
        // Создаем actor
        $actorAccount = new agent_account_schema('https://my.2035.university', $untiId);
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema('http://activitystrea.ms/schema/1.0/watch');

        // Создаем object
        $object = statement_ref_object_schema::create_video_content_ref($contentId);

        // Создаем result
        $result = result_schema::create_video_result($duration, $viewPercentage);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
            'https://api.2035.university/module_num' => $moduleNumber,
        ];

        if ($watchedRecording) {
            $contextExtensions['https://api.2035.university/watched_the_recording'] = true;
        }

        $context = new context_schema(null, $contextExtensions);

        // Добавляем attachments если есть
        $attachments = $attachment ? [$attachment] : [];

        return new self(
            null, // ID будет сгенерирован автоматически
            new DateTime(),
            $actor,
            $verb,
            $object,
            $result,
            $context,
            $attachments
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        $data = [
            'version' => '1.0.3',
            'timestamp' => $this->timestamp->format(DateTime::ATOM),
            'actor' => $this->actor->dump(),
            'verb' => $this->verb->dump(),
            'object' => $this->object->dump(),
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        if ($this->result !== null) {
            $data['result'] = $this->result->dump();
        }

        if ($this->context !== null) {
            $data['context'] = $this->context->dump();
        }

        if (!empty($this->attachments)) {
            $data['attachments'] = array_map(fn($attachment) => $attachment->dump(), $this->attachments);
        }

        return array_filter($data);
    }
} 