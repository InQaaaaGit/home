<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class fact_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private factdef_repository $factdefrepo;
    private fact_repository $factrepo;
    private fact_xapi_service $factxapiservice;
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
        factdef_repository $factdefrepo,
        fact_repository $factrepo,
        fact_xapi_service $factxapiservice,
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
        $this->factdefrepo = $factdefrepo;
        $this->factrepo = $factrepo;
        $this->factxapiservice = $factxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $factid): void {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        if (!$factdef = $this->factdefrepo->read($fact->factdefid)) {
            throw new consistency_error();
        }
        if (!$stream = $this->streamrepo->read($factdef->streamid)) {
            throw new consistency_error();
        }
        $block = null;
        $module = null;
        $theme = null;
        $activity = null;
        $assessment = null;
        if ($factdef->baseobject == 'activity_entity') {
            if (!$activity = $this->activityrepo->read($factdef->baseobjectid)) {
                throw new consistency_error();
            }
            if (!$theme = $this->themerepo->read($activity->themeid)) {
                throw new consistency_error();
            }
            if (!$module = $this->modulerepo->read($theme->moduleid)) {
                throw new consistency_error();
            }
            if (!$block = $this->blockrepo->read($module->blockid)) {
                throw new consistency_error();
            }
        } else if ($factdef->baseobject == 'assessment_entity') {
            if (!$assessment = $this->assessmentrepo->read($factdef->baseobjectid)) {
                throw new consistency_error();
            }
        }
        $statement = $this->factxapiservice->execute($stream, $block, $module, $theme, $activity, $assessment, $factdef, $fact);
        [$lrid] = $this->xapiclient->send([$statement]);
        $fact->set_sentdata($lrid, $this->timedateservice->now());
        $this->factrepo->save($fact);
        $this->logger->info(
            "Fact sent, lrid: {$lrid}",
            'fact_entity',
            $fact->id,
            null,
            (string)json_encode($statement->dump()),
        );
    }
}
