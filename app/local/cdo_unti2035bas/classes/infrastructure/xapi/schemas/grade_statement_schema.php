<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

use DateTime;

/**
 * Схема statement для цифровых следов выставления оценок
 */
class grade_statement_schema {
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
     * Создает statement для получения оценки
     */
    public static function create_grade_received_statement(
        string $untiId,
        string $itemId,
        float $grade,
        float $minGrade,
        float $maxGrade,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber= null,
        int $timestamp = null,
        string $registration = null,
        int $attemptsIndex = 1,
        int $attemptsMax = 1,
        float $scoreThreshold = 1,
        ?string $duration = null
    ): self {
        // Создаем actor
        $actorAccount = new agent_account_schema('https://my.2035.university', $untiId);
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb - используем "passed" для успешного прохождения
        $verb = new verb_schema('http://adlnet.gov/expapi/verbs/passed');

        // Создаем object как StatementRef
        $object = statement_ref_object_schema::create_grade_item_ref($itemId);

        // Создаем result с полной структурой
        $scaled = $maxGrade > $minGrade ? ($grade - $minGrade) / ($maxGrade - $minGrade) : 0;
        $result = result_schema::create_grade_result_full(
            $grade,
            $minGrade,
            $maxGrade,
            $scaled,
            $scaled >= ($scoreThreshold / 100), // success
            $duration, // duration
            [
                'https://api.2035.university/attempts_index' => $attemptsIndex,
                'https://api.2035.university/attempts_max' => $attemptsMax,
                'https://api.2035.university/score_threshold' => $scoreThreshold
            ]
        );

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
            $result,
            $context,
            [] // attachments
        );
    }

    /**
     * Создает statement для удаления оценки
     */
    public static function create_grade_deleted_statement(
        string $untiId,
        int $itemId,
        string $itemName,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        int $timestamp
    ): self {
        // Создаем actor
        $actorAccount = new agent_account_schema('https://my.2035.university', $untiId);
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema('http://adlnet.gov/expapi/verbs/deleted');

        // Создаем object как StatementRef
        $object = statement_ref_object_schema::create_grade_item_ref($itemId);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
            'https://api.2035.university/module_num' => $moduleNumber,
        ];

        $context = new context_schema(null, null, $contextExtensions);

        return new self(
            null,
            new DateTime('@' . $timestamp),
            $actor,
            $verb,
            $object,
            null,
            $context,
            []
        );
    }

    /**
     * Создает statement для создания элемента оценки
     */
    public static function create_grade_item_created_statement(
        int $itemId,
        string $itemName,
        string $itemType,
        string $itemModule,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        int $timestamp
    ): self {
        // Создаем actor (системный)
        $actorAccount = new agent_account_schema('https://my.2035.university', 'system');
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema('http://adlnet.gov/expapi/verbs/created');

        // Создаем object как StatementRef
        $object = statement_ref_object_schema::create_grade_item_ref($itemId);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
            'https://api.2035.university/module_num' => $moduleNumber,
        ];

        $context = new context_schema(null, null, $contextExtensions);

        return new self(
            null,
            new DateTime('@' . $timestamp),
            $actor,
            $verb,
            $object,
            null,
            $context,
            []
        );
    }

    /**
     * Создает statement для обновления элемента оценки
     */
    public static function create_grade_item_updated_statement(
        int $itemId,
        string $itemName,
        string $itemType,
        string $itemModule,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        int $timestamp
    ): self {
        // Создаем actor (системный)
        $actorAccount = new agent_account_schema('https://my.2035.university', 'system');
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema('http://adlnet.gov/expapi/verbs/updated');

        // Создаем object как StatementRef
        $object = statement_ref_object_schema::create_grade_item_ref($itemId);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
            'https://api.2035.university/module_num' => $moduleNumber,
        ];

        $context = new context_schema(null, null, $contextExtensions);

        return new self(
            null,
            new DateTime('@' . $timestamp),
            $actor,
            $verb,
            $object,
            null,
            $context,
            []
        );
    }

    /**
     * Создает statement для удаления элемента оценки
     */
    public static function create_grade_item_deleted_statement(
        int $itemId,
        string $itemName,
        string $itemType,
        string $itemModule,
        int $untiCourseId,
        int $untiFlowId,
        int $moduleNumber,
        int $timestamp
    ): self {
        // Создаем actor (системный)
        $actorAccount = new agent_account_schema('https://my.2035.university', 'system');
        $actor = new agent_schema('Agent', $actorAccount);

        // Создаем verb
        $verb = new verb_schema('http://adlnet.gov/expapi/verbs/deleted');

        // Создаем object как StatementRef
        $object = statement_ref_object_schema::create_grade_item_ref($itemId);

        // Создаем context с расширениями
        $contextExtensions = [
            'https://api.2035.university/project' => 'БАС',
            'https://api.2035.university/parent_course_id' => $untiCourseId,
            'https://api.2035.university/flow_id' => $untiFlowId,
            'https://api.2035.university/module_num' => $moduleNumber,
        ];

        $context = new context_schema(null, null, $contextExtensions);

        return new self(
            null,
            new DateTime('@' . $timestamp),
            $actor,
            $verb,
            $object,
            null,
            $context,
            []
        );
    }

    /**
     * Получить ID statement
     * 
     * @return string
     */
    public function getStatementId(): string {
        if ($this->id === null) {
            // Генерируем UUID если ID не установлен
            if (function_exists('random_bytes')) {
                $data = random_bytes(16);
            } else {
                $data = openssl_random_pseudo_bytes(16);
            }
            
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            
            $this->id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        
        return $this->id;
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