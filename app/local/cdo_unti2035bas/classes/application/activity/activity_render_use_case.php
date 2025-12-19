<?php
namespace local_cdo_unti2035bas\application\activity;

use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class activity_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private activity_xapi_service $activityxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        activity_xapi_service $activityxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->activityxapiservice = $activityxapiservice;
    }

    public function execute(int $activityid): string {
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
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
