<?php
namespace local_cdo_unti2035bas\application\assessment;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class assessment_send_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private assessment_repository $assessmentrepo;
    private assessment_xapi_service $assessmentxapiservice;
    private xapi_client $xapiclient;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        assessment_repository $assessmentrepo,
        assessment_xapi_service $assessmentxapiservice,
        xapi_client $xapiclient
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->assessmentxapiservice = $assessmentxapiservice;
        $this->xapiclient = $xapiclient;
    }

    public function execute(int $assessmentid): void {
        $assessment = $this->assessmentrepo->read($assessmentid);
        if (!$assessment) {
            throw new \InvalidArgumentException();
        }
        $stream = null;
        $block = null;
        $module = null;
        $theme = null;
        if ($assessment->parentobject == 'stream_entity') {
            $stream = $this->streamrepo->read($assessment->parentobjectid);
        }
        if ($assessment->parentobject == 'block_entity') {
            $block = $this->blockrepo->read($assessment->parentobjectid);
            if (!$block) {
                throw new \InvalidArgumentException();
            }
            $stream = $this->streamrepo->read($block->streamid);
        }
        if ($assessment->parentobject == 'module_entity') {
            $module = $this->modulerepo->read($assessment->parentobjectid);
            if (!$module) {
                throw new \InvalidArgumentException();
            }
            $block = $this->blockrepo->read($module->blockid);
            if (!$block) {
                throw new \InvalidArgumentException();
            }
            $stream = $this->streamrepo->read($block->streamid);
        }
        if ($assessment->parentobject == 'theme_entity') {
            $theme = $this->themerepo->read($assessment->parentobjectid);
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
        }
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $statement = $this->assessmentxapiservice->execute($stream, $block, $module, $theme, $assessment);
        [$lrid] = $this->xapiclient->send([$statement]);
        $assessment->set_sentdata($lrid, $this->timedateservice->now());
        $this->assessmentrepo->save($assessment);
        $this->logger->info(
            "Assessment sent, lrid: {$lrid}",
            'assessment_entity',
            $assessment->id,
            $assessment->version,
            (string)json_encode($statement->dump()),
        );
    }
}
