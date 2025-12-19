<?php

namespace local_cdo_unti2035bas\application\statement;

use DateTime;
use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\cancel_statement;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class statement_cancel_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private moodle_service $moodleservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo,
        moodle_service $moodleservice,
        xapi_client $xapiclient

    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->moodleservice = $moodleservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $streamid, string $object, int $objectid, string $lrid): void {
        $stream = $this->streamrepo->read($streamid);
        $entity = null;
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        if ($object == 'activity_entity') {
            $entity = $this->activityrepo->read($objectid);
        } else if ($object == 'assessment_entity') {
            $entity = $this->assessmentrepo->read($objectid);
        } else if ($object == 'theme_entity') {
            $entity = $this->themerepo->read($objectid);
        } else if ($object == 'module_entity') {
            $entity = $this->modulerepo->read($objectid);
        } else if ($object == 'block_entity') {
            $entity = $this->blockrepo->read($objectid);
        } else if ($object == 'stream_entity') {
            $entity = $stream;
        }
        if (!$entity) {
            throw new \InvalidArgumentException();
        }
        /** @var array<string, mixed> $xapidata */
        $xapidata = $this->xapiclient->download($lrid);
        /** @var array<string, mixed> $xapiobjectdata */
        $xapiobjectdata = $xapidata['object'];
        /** @var string $xapiobjectid */
        $xapiobjectid = $xapiobjectdata['id'];
        $prefix = $this->moodleservice->get_config()->wwwroot;
        if (strpos($xapiobjectid, $prefix) !== 0) {
            throw new \Exception('Prefix not match');
        }
        $timestamp = $this->timedateservice->now();
        $builder = new cancel_statement();
        $builder->with_timestamp(new DateTime("@{$timestamp}"));
        $builder->with_prefix($prefix);
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_statementlrid($lrid);
        $builder->with_objectid($xapiobjectid);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $statement = $builder->build();
        [$cancellrid] = $this->xapiclient->send([$statement]);
        $entity->set_canceled();
        if ($entity instanceof activity_entity) {
            $entity = $this->activityrepo->save($entity);
        } else if ($entity instanceof assessment_entity) {
            $entity = $this->assessmentrepo->save($entity);
        } else if ($entity instanceof theme_entity) {
            $entity = $this->themerepo->save($entity);
        } else if ($entity instanceof module_entity) {
            $entity = $this->modulerepo->save($entity);
        } else if ($entity instanceof block_entity) {
            $entity = $this->blockrepo->save($entity);
        } else {
            $entity = $this->streamrepo->save($entity);
        }
        $this->logger->info(
            "Statement canceled, lrid: {$cancellrid}",
            $object,
            $objectid,
            $entity->version,
            (string)json_encode($statement->dump()),
        );
   }
}
