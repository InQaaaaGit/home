<?php
namespace local_cdo_unti2035bas\application\assessment;

use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class assessment_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private assessment_repository $assessmentrepo;
    private assessment_xapi_service $assessmentxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        assessment_repository $assessmentrepo,
        assessment_xapi_service $assessmentxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->assessmentxapiservice = $assessmentxapiservice;
    }

    public function execute(int $assessmentid): string {
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
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
