<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class verb_schema {
    const VERBDISPLAY = [
        'http://activitystrea.ms/schema/1.0/author' => 'Будет создан',
        'http://activitystrea.ms/schema/1.0/host' => 'Будет проведено',
        'http://activitystrea.ms/schema/1.0/watch' => 'Просмотрел видео',
        'http://activitystrea.ms/schema/1.0/cancel' => 'Будет отменено',
        'http://adlnet.gov/expapi/verbs/passed' => 'Прошёл испытание',
        'http://adlnet.gov/expapi/verbs/completed' => 'Завершил',
        'http://adlnet.gov/expapi/verbs/deleted' => 'Удалил',
        'http://adlnet.gov/expapi/verbs/created' => 'Создал',
        'http://adlnet.gov/expapi/verbs/updated' => 'Обновил',
        'http://id.tincanapi.com/verb/downloaded' => 'Скачал',
        'http://id.tincanapi.com/verb/viewed' => 'Просмотрел',
    ];

    /** @read-only */
    public string $id;

    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        $data = ['id' => $this->id];
        if ($display = $this::VERBDISPLAY[$this->id] ?? null) {
            $data['display'] = ['ru-RU' => $display];
        }
        return $data;
    }

    public static function from_verbid(string $verbid): self {
        return new self($verbid);
    }
}
