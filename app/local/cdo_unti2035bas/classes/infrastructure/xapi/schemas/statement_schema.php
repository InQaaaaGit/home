<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

use DateTime;


class statement_schema {
    /** @readonly */
    public ?string $id;
    /** @readonly */
    public DateTime $timestamp;
    /** @readonly */
    public agent_schema $actor;
    /** @readonly */
    public verb_schema $verb;
    /** @readonly */
    public object_schema $object;
    /**
     * @readonly
     * @var result_schema2|result_schema|null
     */
    public $result;
    /** @readonly */
    public ?context_schema $context;
    /**
        @readonly
        @var array<attachment_schema>|null
     */
    public ?array $attachments;

    /**
     * @param array<attachment_schema> $attachments
     * @param result_schema2|result_schema|null $result
     */
    public function __construct(
        ?string $id,
        DateTime $timestamp,
        agent_schema $actor,
        verb_schema $verb,
        object_schema $object,
        ?context_schema $context,
        ?array $attachments = null,
        $result = null
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
            'result' => $this->result ? $this->result->dump() : null,
            'context' => $this->context ? $this->context->dump() : null,
            'attachments' => $this->attachments ? array_map(fn($a) => $a->dump(), $this->attachments) : null,
        ]);
    }
}
