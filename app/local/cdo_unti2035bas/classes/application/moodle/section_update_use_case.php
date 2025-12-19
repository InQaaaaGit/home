<?php
namespace local_cdo_unti2035bas\application\moodle;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class section_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
    }

    public function execute(int $courseid, int $sectionid): void {
        if (!$this->streamrepo->exists_by_courseid($courseid)) {
            return;
        }
        if ($blocks = $this->blockrepo->read_by_sectionid($sectionid)) {
            foreach ($blocks as $block) {
                $block->set_changed($this->timedateservice->now());
                $this->blockrepo->save($block);
                $this->logger->info(
                    "Moodle section updated, courseid: {$courseid}, sectionid: {$sectionid}",
                    "block_entity",
                    $block->id,
                    $block->version,
                );
            }
        }
        if ($modules = $this->modulerepo->read_by_sectionid($sectionid)) {
            foreach ($modules as $module) {
                $module->set_changed($this->timedateservice->now());
                $this->modulerepo->save($module);
                $this->logger->info(
                    "Moodle section updated, courseid: {$courseid}, sectionid: {$sectionid}",
                    "module_entity",
                    $module->id,
                    $module->version,
                );
            }
        }
        if ($themes = $this->themerepo->read_by_sectionid($sectionid)) {
            foreach ($themes as $theme) {
                $theme->set_changed($this->timedateservice->now());
                $this->themerepo->save($theme);
                $this->logger->info(
                    "Moodle section updated, courseid: {$courseid}, sectionid: {$sectionid}",
                    "theme_entity",
                    $theme->id,
                    $theme->version,
                );
            }
        }
    }
}
