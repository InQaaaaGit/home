<?php
namespace local_cdo_unti2035bas\application\log;

use local_cdo_unti2035bas\domain\log_record_vo;
use local_cdo_unti2035bas\infrastructure\persistence\log_record_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class log_service {
    private timedate_service $timedateservice;
    private log_record_repository $logrecordrepo;

    public function __construct(
        timedate_service $timedateservice,
        log_record_repository $logrecordrepo
    ) {
        $this->timedateservice = $timedateservice;
        $this->logrecordrepo = $logrecordrepo;
    }

    public function debug(
        string $message,
        ?string $object = null,
        ?int $objectid = null,
        ?int $objectversion = null,
        ?string $xapi = null
    ): void {
        $this->logrecordrepo->save(
            new log_record_vo(
                $object,
                $objectid,
                $objectversion,
                $message,
                $this->timedateservice->now(),
                'debug',
                $xapi,
            )
        );
    }

    public function info(
        string $message,
        ?string $object = null,
        ?int $objectid = null,
        ?int $objectversion = null,
        ?string $xapi = null
    ): void {
        $this->logrecordrepo->save(
            new log_record_vo(
                $object,
                $objectid,
                $objectversion,
                $message,
                $this->timedateservice->now(),
                'info',
                $xapi,
            )
        );
    }

    public function warning(
        string $message,
        ?string $object = null,
        ?int $objectid = null,
        ?int $objectversion = null,
        ?string $xapi = null
    ): void {
        $this->logrecordrepo->save(
            new log_record_vo(
                $object,
                $objectid,
                $objectversion,
                $message,
                $this->timedateservice->now(),
                'warning',
                $xapi,
            )
        );
    }

    public function error(
        string $message,
        ?string $object = null,
        ?int $objectid = null,
        ?int $objectversion = null,
        ?string $xapi = null
    ): void {
        $this->logrecordrepo->save(
            new log_record_vo(
                $object,
                $objectid,
                $objectversion,
                $message,
                $this->timedateservice->now(),
                'error',
                $xapi,
            )
        );
    }

    public function critical(
        string $message,
        ?string $object = null,
        ?int $objectid = null,
        ?int $objectversion = null,
        ?string $xapi = null
    ): void {
        $this->logrecordrepo->save(
            new log_record_vo(
                $object,
                $objectid,
                $objectversion,
                $message,
                $this->timedateservice->now(),
                'critical',
                $xapi,
            )
        );
    }
}
