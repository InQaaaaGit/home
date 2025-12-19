<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class factdef_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private factdef_repository $factdefrepo;
    private factdef_xapi_service $factdefxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo,
        factdef_repository $factdefrepo,
        factdef_xapi_service $factdefxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->factdefrepo = $factdefrepo;
        $this->factdefxapiservice = $factdefxapiservice;
    }

    public function execute(int $factdefid): string {
        $factdef = $this->factdefrepo->read($factdefid);
        if (!$factdef) {
            throw new \InvalidArgumentException();
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
            if ($assessment->parentobject == 'block_entity') {
                if (!$block = $this->blockrepo->read($assessment->parentobjectid)) {
                    throw new consistency_error();
                }
            }
        }
        $statement = $this->factdefxapiservice->execute($stream, $block, $module, $theme, $activity, $assessment, $factdef);
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
