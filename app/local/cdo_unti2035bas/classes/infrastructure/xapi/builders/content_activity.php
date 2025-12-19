<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\context_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_definition_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;

/**
 * Билдер для создания xAPI statements для активностей с контентом
 * (скачивание/просмотр файлов, переходы на страницы с контентом)
 */
class content_activity extends base {
    private string $contentid;
    private string $verb;
    private int $modulenum;
    private ?object_definition_schema $objectdefinition = null;

    /**
     * Устанавливает ID контента (ссылка на ЦС методиста)
     */
    public function with_content_id(string $contentid): self {
        $this->contentid = $contentid;
        return $this;
    }

    /**
     * Устанавливает действие: 'downloaded' или 'viewed'
     */
    public function with_verb(string $verb): self {
        if (!in_array($verb, ['downloaded', 'viewed'])) {
            throw new \InvalidArgumentException('Verb must be either "downloaded" or "viewed"');
        }
        $this->verb = $verb;
        return $this;
    }

    /**
     * Устанавливает номер модуля
     */
    public function with_module_num(int $modulenum): self {
        $this->modulenum = $modulenum;
        return $this;
    }

    /**
     * Устанавливает определение объекта (необязательно)
     * Используется когда нужно описать объект подробнее
     */
    public function with_object_definition(
        ?string $name,
        ?string $description,
        string $type,
        array $extensions = []
    ): self {
        $this->objectdefinition = new object_definition_schema(
            $name,
            $description,
            $type,
            $extensions
        );
        return $this;
    }

    public function build(): statement_schema {
        $this->validate();

        $verburl = $this->verb === 'downloaded' 
            ? 'http://id.tincanapi.com/verb/downloaded'
            : 'http://id.tincanapi.com/verb/viewed';

        return new statement_schema(
            $this->lrid,
            $this->timestamp,
            agent_schema::from_agentname($this->actorname),
            new verb_schema($verburl),
            new object_schema(
                $this->contentid,
                'StatementRef',
                $this->objectdefinition ?? null
            ),
            new context_schema(
                null, null,
                [
                    'https://api.2035.university/project' => 'БАС',
                    'https://api.2035.university/parent_course_id' => $this->unticourseid,
                    'https://api.2035.university/flow_id' => $this->untiflowid,
                    'https://api.2035.university/module_num' => $this->modulenum,
                ]
            )
        );
    }

    /**
     * Валидация обязательных полей
     */
    private function validate(): void {
        if (empty($this->actorname)) {
            throw new \InvalidArgumentException('Actor name is required');
        }
        if (empty($this->contentid)) {
            throw new \InvalidArgumentException('Content ID is required');
        }
        if (empty($this->verb)) {
            throw new \InvalidArgumentException('Verb is required');
        }
        if (!isset($this->timestamp)) {
            throw new \InvalidArgumentException('Timestamp is required');
        }
        if (!isset($this->unticourseid)) {
            throw new \InvalidArgumentException('UNTI course ID is required');
        }
        if (!isset($this->untiflowid)) {
            throw new \InvalidArgumentException('UNTI flow ID is required');
        }
        if (!isset($this->modulenum)) {
            throw new \InvalidArgumentException('Module number is required');
        }
    }
} 