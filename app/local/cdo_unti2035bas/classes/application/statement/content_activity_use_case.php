<?php

namespace local_cdo_unti2035bas\application\statement;

use DateTime;
use local_cdo_unti2035bas\infrastructure\xapi\builders\content_activity;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;

/**
 * Use case для обработки событий активности с контентом
 */
class content_activity_use_case {
    private xapi_client $xapiclient;

    public function __construct(xapi_client $xapiclient) {
        $this->xapiclient = $xapiclient;
    }

    /**
     * Обрабатывает событие скачивания контента
     */
    public function handle_download(
        string $actorname,
        string $contentid,
        int $unticourseid,
        int $untiflowid,
        int $modulenum,
        ?DateTime $timestamp = null
    ): array {
        return $this->handle_event(
            $actorname,
            $contentid,
            'downloaded',
            $unticourseid,
            $untiflowid,
            $modulenum,
            $timestamp
        );
    }

    /**
     * Обрабатывает событие просмотра контента
     */
    public function handle_view(
        string $actorname,
        string $contentid,
        int $unticourseid,
        int $untiflowid,
        int $modulenum,
        ?DateTime $timestamp = null
    ): array {
        return $this->handle_event(
            $actorname,
            $contentid,
            'viewed',
            $unticourseid,
            $untiflowid,
            $modulenum,
            $timestamp
        );
    }

    /**
     * Общий метод обработки событий
     */
    private function handle_event(
        string $actorname,
        string $contentid,
        string $verb,
        int $unticourseid,
        int $untiflowid,
        int $modulenum,
        ?DateTime $timestamp = null
    ): array {
        $builder = new content_activity();
        
        $statement = $builder
            ->with_actorname($actorname)
            ->with_content_id($contentid)
            ->with_verb($verb)
            ->with_unticourseid($unticourseid)
            ->with_untiflowid($untiflowid)
            ->with_module_num($modulenum)
            ->with_timestamp($timestamp ?? new DateTime())
            ->build();

        // Отправляем statement в xAPI (в массиве, как ожидает клиент)
        $response = $this->xapiclient->send([$statement]);
        
        return $response;
    }
} 