<?php
namespace local_cdo_unti2035bas\application\stream;

use core_text;
use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\activity_config_vo;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\activity_moodle_vo;
use local_cdo_unti2035bas\domain\assessment_config_vo;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\assessment_moodle_vo;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\block_moodle_vo;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\module_moodle_vo;
use local_cdo_unti2035bas\domain\module_unti_vo;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\domain\theme_moodle_vo;
use local_cdo_unti2035bas\domain\theme_unti_vo;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class stream_sync_service {
    private log_service $logger;
    private timedate_service $timedateservice;
    private block_repository $blockrepo;
    private module_repository $modulerepo;
    private theme_repository $themerepo;
    private activity_repository $activityrepo;
    private assessment_repository $assessmentrepo;
    private moodle_service $moodleservice;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        block_repository $blockrepo,
        module_repository $modulerepo,
        theme_repository $themerepo,
        activity_repository $activityrepo,
        assessment_repository $assessmentrepo,
        moodle_service $moodleservice
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->blockrepo = $blockrepo;
        $this->modulerepo = $modulerepo;
        $this->themerepo = $themerepo;
        $this->activityrepo = $activityrepo;
        $this->assessmentrepo = $assessmentrepo;
        $this->moodleservice = $moodleservice;
    }

    public function execute(stream_entity $stream): void {
        if (!$stream->id) {
            throw new \InvalidArgumentException();
        }
        $sections = $this->moodleservice->get_sections($stream->moodle->courseid);
        $this->sync_blocks($stream->id, $sections);
        $this->sync_activities(
            $stream->id,
            null,
            null,
            null,
            $stream->moodle->courseid,
            $stream->moodle->sectionid,
            $sections,
        );
        $blocks = $this->blockrepo->read_by_streamid($stream->id);
        foreach ($blocks as $block) {
            if ($block->deleted || $block->override->ismanual) {
                continue;
            }
            assert(is_int($block->id));
            /** @var array<int, array<string, mixed>> */
            $subsections = $sections[$block->moodle->sectionid]['subsections'];
            $this->sync_modules($block->id, $subsections);
            $this->sync_activities(
                $stream->id,
                $block->id,
                null,
                null,
                $stream->moodle->courseid,
                $block->moodle->sectionid,
                $subsections,
            );
            $modules = $this->modulerepo->read_by_blockid($block->id);
            foreach ($modules as $module) {
                if ($module->deleted || $module->override->ismanual) {
                    continue;
                }
                assert(is_int($module->id));
                /** @var array<int, array<string, mixed>> */
                $subsubsections = $subsections[$module->moodle->sectionid]['subsections'];
                $this->sync_themes($module->id, $subsubsections);
                $this->sync_activities(
                    $stream->id,
                    $block->id,
                    $module->id,
                    null,
                    $stream->moodle->courseid,
                    $module->moodle->sectionid,
                    $subsubsections,
                );
                $themes = $this->themerepo->read_by_moduleid($module->id);
                foreach ($themes as $theme) {
                    if ($theme->deleted || $theme->override->ismanual) {
                        continue;
                    }
                    assert(is_int($theme->id));
                    $this->sync_activities(
                        $stream->id,
                        $block->id,
                        $module->id,
                        $theme->id,
                        $stream->moodle->courseid,
                        $theme->moodle->sectionid,
                        $subsubsections[$theme->moodle->sectionid]['subsections'],
                    );
                }
            }
        }
    }

    /**
     * @param int $streamid
     * @param array<int, array<string, mixed>> $sections
     */
    private function sync_blocks(int $streamid, array $sections): void {
        $sections = array_filter(
            $sections,
            fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') === false,
        );
        $blocks = $this->blockrepo->read_by_streamid($streamid);
        $blocksmapped = [];
        foreach ($blocks as $block) {
            $blocksmapped[$block->moodle->sectionid] = $block;
        }
        foreach ($blocks as $block) {
            if (!$block->deleted && !$block->override->ismanual && !isset($sections[$block->moodle->sectionid])) {
                $block->set_deleted($this->timedateservice->now());
                $this->blockrepo->save($block);
                $this->logger->info(
                    "Block deleted, sectionid: {$block->moodle->sectionid}",
                    'block_entity',
                    $block->id,
                    $block->version,
                );
                /** @var int $blockid */
                $blockid = $block->id;
                $this->sync_activities_in_deleted_section($blockid, null, null);
                $this->sync_modules_in_deleted_block($blockid);
            }
        }
        foreach ($sections as $section) {
            if (array_filter($blocks, fn($b) => $b->type == 'theoretical' && !$b->deleted)) {
                $type = 'practical';
            } else {
                $type = 'theoretical';
            }
            /** @var int */
            $sectionid = $section['sectionid'];
            if (!isset($blocksmapped[$sectionid])) {
                $block = new block_entity(
                    null,
                    $streamid,
                    $type,
                    null,
                    $this->timedateservice->now(),
                    new block_moodle_vo($sectionid),
                );
                $block = $this->blockrepo->save($block);
                $this->logger->info(
                    "Block created, sectionid: {$sectionid}",
                    'block_entity',
                    $block->id,
                    $block->version,
                );
                $blocks[] = $block;
            }
        }
    }

    /**
     * @param int $blockid
     * @param array<int, array<string, mixed>> $sections
     */
    private function sync_modules(int $blockid, array $sections): void {
        $sections = array_filter(
            $sections,
            fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') === false,
        );
        $modules = $this->modulerepo->read_by_blockid($blockid);
        $modulesmapped = [];
        foreach ($modules as $module) {
            $modulesmapped[$module->moodle->sectionid] = $module;
        }
        foreach ($modules as $module) {
            if (!$module->override->ismanual && !$module->deleted && !isset($sections[$module->moodle->sectionid])) {
                $module->set_deleted($this->timedateservice->now());
                $this->modulerepo->save($module);
                $this->logger->info(
                    "Module deleted, sectionid: {$module->moodle->sectionid}",
                    'module_entity',
                    $module->id,
                    $module->version,
                );
                /** @var int $moduleid */
                $moduleid = $module->id;
                $this->sync_activities_in_deleted_section(null, $moduleid, null);
                $this->sync_themes_in_deleted_module($moduleid);
            }
        }
        $position = 0;
        foreach ($sections as $section) {
            $position++;
            /** @var int */
            $sectionid = $section['sectionid'];
            if (!isset($modulesmapped[$sectionid])) {
                $module = new module_entity(
                    null,
                    null,
                    $blockid,
                    new module_moodle_vo($sectionid, $position),
                    new module_unti_vo(null),
                    $this->timedateservice->now(),
                );
                $module = $this->modulerepo->save($module);
                $this->logger->info(
                    "Module created, sectionid: {$sectionid}",
                    'module_entity',
                    $module->id,
                    $module->version,
                );
                $modules[] = $module;
            } else {
                $module = $modulesmapped[$sectionid];
                $updated = $module->set_moodledata(
                    new module_moodle_vo($sectionid, $position),
                    $this->timedateservice->now(),
                );
                if ($updated) {
                    $module = $this->modulerepo->save($module);
                    $this->logger->info(
                        "Moodle section updated, sectionid: {$sectionid}",
                        'module_entity',
                        $module->id,
                        $module->version,
                    );
                }
            }
        }
    }

    private function sync_modules_in_deleted_block(int $blockid): void {
        $modules = $this->modulerepo->read_by_blockid($blockid);
        foreach ($modules as $module) {
            if (!$module->deleted) {
                $module->set_deleted($this->timedateservice->now());
                $this->modulerepo->save($module);
                /** @var int $moduleid */
                $moduleid = $module->id;
                $this->sync_activities_in_deleted_section(null, $moduleid, null);
                $this->sync_themes_in_deleted_module($moduleid);
            }
        }
    }

    /**
     * @param int $moduleid
     * @param array<int, array<string, mixed>> $sections
     */
    private function sync_themes(int $moduleid, array $sections): void {
        $sections = array_filter(
            $sections,
            fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') === false,
        );
        $themes = $this->themerepo->read_by_moduleid($moduleid);
        $themesmapped = [];
        foreach ($themes as $theme) {
            $themesmapped[$theme->moodle->sectionid] = $theme;
        }
        foreach ($themes as $theme) {
            if (!$theme->override->ismanual && !$theme->deleted && !isset($sections[$theme->moodle->sectionid])) {
                $theme->set_deleted($this->timedateservice->now());
                $this->themerepo->save($theme);
                $this->logger->info(
                    "Theme deleted, sectionid: {$theme->moodle->sectionid}",
                    'theme_entity',
                    $theme->id,
                    $theme->version,
                );
                $this->sync_activities_in_deleted_section(null, null, $theme->id);
            }
        }
        $position = 0;
        foreach ($sections as $section) {
            $position++;
            /** @var int */
            $sectionid = $section['sectionid'];
            if (!isset($themesmapped[$sectionid])) {
                $theme = new theme_entity(
                    null,
                    null,
                    $moduleid,
                    new theme_moodle_vo($sectionid, $position),
                    new theme_unti_vo(null),
                    $this->timedateservice->now(),
                );
                $theme = $this->themerepo->save($theme);
                $this->logger->info(
                    "Theme created, sectionid: {$sectionid}",
                    'theme_entity',
                    $theme->id,
                    $theme->version,
                );
                $themes[] = $theme;
            } else {
                $theme = $themesmapped[$sectionid];
                $updated = $theme->set_moodledata(
                    new theme_moodle_vo($sectionid, $position),
                    $this->timedateservice->now()
                );
                if ($updated) {
                    $this->themerepo->save($theme);
                    $this->logger->info(
                        "Moodle section updated, sectionid: {$sectionid}",
                        'theme_entity',
                        $theme->id,
                        $theme->version,
                    );
                }
            }
        }
    }

    private function sync_themes_in_deleted_module(int $moduleid): void {
        $themes = $this->themerepo->read_by_moduleid($moduleid);
        foreach ($themes as $theme) {
            if ($theme->deleted) {
                continue;
            }
            $theme->set_deleted($this->timedateservice->now());
            $this->themerepo->save($theme);
            /** @var int $themeid*/
            $themeid = $theme->id;
            $this->sync_activities_in_deleted_section(null, null, $themeid);
        }
    }

    private function sync_activities(
        int $streamid,
        ?int $blockid,
        ?int $moduleid,
        ?int $themeid,
        int $courseid,
        int $sectionid,
        array $sections
    ): void {
        $mods = $this->moodleservice->get_activities($courseid, $sectionid);
        $assessmentsections = array_filter(
            $sections,
            fn($s) => core_text::strpos(core_text::strtolower($s['name']), 'аттестация') !== false,
        );
        $assessmentmods = [];
        foreach ($assessmentsections as $section) {
            $assessmentmods += $this->moodleservice->get_activities($courseid, $section['sectionid']);
        }
        if ($themeid) {
            $activities = $this->activityrepo->read_by_themeid($themeid);
            $activitiesmapped = [];
            foreach ($activities as $activity) {
                $activitiesmapped[$activity->moodle->modid] = $activity;
            }
            foreach ($activities as $activity) {
                if (!$activity->override->ismanual && !$activity->deleted && !isset($mods[$activity->moodle->modid])) {
                    $activity->set_deleted($this->timedateservice->now());
                    $this->activityrepo->save($activity);
                    $this->logger->info(
                        "Activity deleted, modid: {$activity->moodle->modid}",
                        'activity_entity',
                        $activity->id,
                        $activity->version,
                    );
                }
            }
            $position = 0;
            foreach ($mods as $modid => $mod) {
                $position++;
                if (!isset($activitiesmapped[$modid])) {
                    // TODO: Add other types of activity
                    $activitytype = null;
                    if ($mod->activitytype == 'resource') {
                        if (strpos($mod->fileinfo->mimetype ?? '', 'video') === 0) {
                            $activitytype = 'video';
                        }
                        if (($mod->fileinfo->mimetype ?? '') == 'application/pdf') {
                            $activitytype = 'article';
                        }
                    } else if ($mod->activitytype == 'page') {
                        $activitytype = 'article';
                    }
                    if ($activitytype) {
                        $admittanceform = $activitytype == 'practice' ? 'offline' : null;
                        $activity = new activity_entity(
                            null,
                            null,
                            $themeid,
                            $activitytype,
                            new activity_moodle_vo($modid, $position),
                            new activity_config_vo(true, false, 0, 0, 0, 1, $admittanceform),
                            $this->timedateservice->now(),
                        );
                        $activity = $this->activityrepo->save($activity);
                        $this->logger->info(
                            "Activity created, modid: {$modid}",
                            'activity_entity',
                            $activity->id,
                            $activity->version,
                        );
                    } else {
                        $this->logger->warning(
                            "Activity not created, modid: {$modid}",
                        );
                    }
                }
            }
            $assessments = $this->assessmentrepo->read_by_themeid($themeid);
            $parentobject = 'theme_entity';
            $parentobjectid = $themeid;
        } else if ($moduleid) {
            $assessments = $this->assessmentrepo->read_by_moduleid($moduleid);
            $parentobject = 'module_entity';
            $parentobjectid = $moduleid;
        } else if ($blockid) {
            $assessments = $this->assessmentrepo->read_by_blockid($blockid);
            $parentobject = 'block_entity';
            $parentobjectid = $blockid;
        } else {
            $assessments = $this->assessmentrepo->read_by_streamid($streamid);
            $parentobject = 'stream_entity';
            $parentobjectid = $streamid;
        }
        $assessmentsmapped = [];
        foreach ($assessments as $assessment) {
            $assessmentsmapped[$assessment->moodle->modid] = $assessment;
        }
        foreach ($assessments as $assessment) {
            if (!$assessment->override->ismanual && !$assessment->deleted && !isset($assessmentmods[$assessment->moodle->modid])) {
                $assessment->set_deleted($this->timedateservice->now());
                $this->assessmentrepo->save($assessment);
                $this->logger->info(
                    "Assessment deleted, modid: {$assessment->moodle->modid}",
                    'assessment_entity',
                    $assessment->id,
                    $assessment->version,
                );
            }
        }
        $position = 0;
        foreach ($assessmentmods as $modid => $mod) {
            $position++;
            if (!isset($assessmentsmapped[$modid])) {
                // TODO: Add other types of assessment
                if ($mod->activitytype == 'quiz') {
                    $assessment = new assessment_entity(
                        null,
                        null,
                        $parentobject,
                        $parentobjectid,
                        new assessment_moodle_vo($modid),
                        new assessment_config_vo(0, 0, 0, 1, false, null),
                        $this->timedateservice->now(),
                    );
                    $assessment = $this->assessmentrepo->save($assessment);
                    $this->logger->info(
                        "Assessment created, modid: {$modid}",
                        'assessment_entity',
                        $assessment->id,
                        $assessment->version,
                    );
                } else {
                    $this->logger->warning(
                        "Assessment not created, modid: {$modid}",
                    );
                }
            }
        }
    }

    private function sync_activities_in_deleted_section(
        ?int $blockid,
        ?int $moduleid,
        ?int $themeid
    ): void {
        $activities = [];
        if ($themeid) {
            $activities = $this->activityrepo->read_by_themeid($themeid);
            $assessments = $this->assessmentrepo->read_by_themeid($themeid);
        } else if ($moduleid) {
            $assessments = $this->assessmentrepo->read_by_moduleid($moduleid);
        } else if ($blockid) {
            $assessments = $this->assessmentrepo->read_by_blockid($blockid);
        } else {
            throw new \InvalidArgumentException();
        }
        foreach ($activities as $activity) {
            if (!$activity->deleted) {
                $activity->set_deleted($this->timedateservice->now());
                $this->activityrepo->save($activity);
                $this->logger->info(
                    "Activity deleted(section deleted), modid: {$activity->moodle->modid}",
                    'activity_entity',
                    $activity->id,
                    $activity->version,
                );
            }
        }
        foreach ($assessments as $assessment) {
            if (!$assessment->deleted) {
                $assessment->set_deleted($this->timedateservice->now());
                $this->assessmentrepo->save($assessment);
                $this->logger->info(
                    "Assessment deleted(section deleted), modid: {$assessment->moodle->modid}",
                    'assessment_entity',
                    $assessment->id,
                    $assessment->version,
                );
            }
        }
    }
}
