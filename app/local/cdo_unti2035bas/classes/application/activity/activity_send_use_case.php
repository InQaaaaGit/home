<?php
namespace local_cdo_unti2035bas\application\activity;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class activity_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private activity_xapi_service $activityxapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        activity_xapi_service $activityxapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->activityxapiservice = $activityxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $activityid): void {
        $activity = $this->activityrepo->read($activityid);
        if (!$activity) {
            throw new \InvalidArgumentException();
        }
        $theme = $this->themerepo->read($activity->themeid);
        if (!$theme) {
            throw new \InvalidArgumentException();
        }
        $module = $this->modulerepo->read($theme->moduleid);
        if (!$module) {
            throw new \InvalidArgumentException();
        }
        $block = $this->blockrepo->read($module->blockid);
        if (!$block) {
            throw new \InvalidArgumentException();
        }
        $stream = $this->streamrepo->read($block->streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->activityxapiservice->execute($stream, $block, $module, $theme, $activity);
        [$lrid] = $this->xapiclient->send([$statement]);
        $activity->set_sentdata($lrid, $this->timedateservice->now());
        $this->activityrepo->save($activity);
        $this->logger->info(
            "Activity sent, lrid: {$lrid}",
            'activity_entity',
            $activity->id,
            $activity->version,
            (string)json_encode($statement->dump()),
        );
    }
}
