<?php
namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\application\stream\stream_fd_sync_service;
use local_cdo_unti2035bas\application\stream\stream_sync_service;
use local_cdo_unti2035bas\application\stream\stream_update_use_case;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_activity_dto;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_fileinfo_dto;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\log_record_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


final class stream_use_cases_test extends \advanced_testcase {
    public function test_stream_update_use_case(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_cdo_unti2035bas');
        $timedateservice = new timedate_service();
        $logrepo = new log_record_repository();
        $streamrepo = new stream_repository();
        $blockrepo = new block_repository();
        $modulerepo = new module_repository();
        $themerepo = new theme_repository();
        $activityrepo = new activity_repository();
        $assessmentrepo = new assessment_repository();

        $transaction = $DB->start_delegated_transaction();
        /** @var stream_entity $stream */
        $stream = $streamrepo->save($generator->get_stream_entity());
        /** @var int $streamid */
        $streamid = $stream->id;
        /** @var block_entity $block */
        $block = $blockrepo->save($generator->get_block_entity(['streamid' => $streamid]));
        /** @var int $blockid */
        $blockid = $block->id;
        /** @var module_entity $module */
        $module = $modulerepo->save($generator->get_module_entity(['blockid' => $blockid]));
        /** @var int $moduleid */
        $moduleid = $module->id;
        $theme = $themerepo->save($generator->get_theme_entity(['moduleid' => $moduleid]));
        /** @var int $themeid */
        $themeid = $theme->id;
        $assessment = $assessmentrepo->save($generator->get_assessment_entity([
            'parentobject' => 'module_entity',
            'parentobjectid' => $moduleid,
            'changed' => false,
            'version' => 1,
        ]));
        /** @var int $assessmentid */
        $assessmentid = $assessment->id;
        $activityrepo->save($generator->get_activity_entity([
            'themeid' => $themeid,
            'moodle' => $generator->get_activity_moodle_vo(501, 1),
            'changed' => false,
            'version' => 1,
        ]));
        $activityrepo->save($generator->get_activity_entity([
            'themeid' => $themeid,
            'moodle' => $generator->get_activity_moodle_vo(502, 2),
            'changed' => true,
            'version' => 1,
        ]));
        $transaction->allow_commit();

        $transaction = $DB->start_delegated_transaction();
        $logger = new log_service($timedateservice, $logrepo);
        $usecase = new stream_update_use_case(
            $logger,
            $timedateservice,
            $streamrepo,
            $activityrepo,
            $assessmentrepo,
        );
        $usecase->execute($streamid, 50, true, 'new comment');
        $transaction->allow_commit();

        $transaction = $DB->start_delegated_transaction();
        $activities = array_values($activityrepo->read_by_streamid($streamid));
        $this->assertEquals(2, count($activities));
        $this->assertTrue($activities[0]->changed);
        $this->assertEquals(2, $activities[0]->version);
        $this->assertTrue($activities[1]->changed);
        $this->assertEquals(1, $activities[1]->version);
        $assessments = array_values($assessmentrepo->read_all_by_streamid($streamid));
        $this->assertEquals(1, count($assessments));
        $this->assertTrue($assessments[0]->changed);
        $this->assertEquals(2, $assessments[0]->version);
        $transaction->allow_commit();
    }

    public function test_stream_sync_service(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_cdo_unti2035bas');
        $timedateservice = new timedate_service();
        $moodleservicestub = $this->createStub(moodle_service::class);
        $moodleservicestub->method('get_sections')->willReturn([
            15 => [
                'name' => 'Block #1',
                'summary' => 'Test Theoretical Block',
                'sectionid' => 15,
                'number' => 1,
                'subsections' => [
                    151 => [
                        'name' => 'Module #1-1',
                        'summary' => 'Test Module 1-1',
                        'sectionid' => 151,
                        'number' => 2,
                        'subsections' => [
                            1511 => [
                                'name' => 'Theme #1-1-1',
                                'summary' => 'Test Theme 1-1-1',
                                'sectionid' => 1511,
                                'number' => 3,
                                'subsections' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $getactivitiesreturns = [
            [
                1001 => new moodle_activity_dto(
                    'resource',
                    1001,
                    'Video Lesson 1001',
                    'Video Desc 1001',
                    new moodle_fileinfo_dto(
                        'Lesson.mp4',
                        'video/mp4',
                        9000100,
                        '/some/dir/moodledata/filedir/151515',
                    ),
                    1,
                ),
                1002 => new moodle_activity_dto(
                    'resource',
                    1002,
                    'Pdf Article 1002',
                    'Pdf Desc 1002',
                    new moodle_fileinfo_dto(
                        'SomeArticle.pdf',
                        'application/pdf',
                        9001002,
                        '/some/dir/moodledata/filedir/1011002',
                    ),
                    2,
                ),
                1003 => new moodle_activity_dto(
                    'page',
                    1003,
                    'Page mod 1003',
                    'Page mod desc 1003',
                    null,
                    3,
                ),
            ],
        ];
        $moodleservicestub->method('get_activities')->willReturnCallback(fn() => array_shift($getactivitiesreturns));

        $transaction = $DB->start_delegated_transaction();
        /** @var stream_entity $stream */
        $stream = (new stream_repository())->save($generator->get_stream_entity());
        /** @var int $streamid */
        $streamid = $stream->id;
        $transaction->allow_commit();

        $transaction = $DB->start_delegated_transaction();
        $streamrepo = new stream_repository();
        $stream = $streamrepo->read($streamid);
        assert(!is_null($stream));
        $service = new stream_sync_service(
            new log_service($timedateservice, new log_record_repository()),
            $timedateservice,
            new block_repository(),
            new module_repository(),
            new theme_repository(),
            new activity_repository(),
            new assessment_repository(),
            $moodleservicestub,
        );
        $service->execute($stream);
        $transaction->allow_commit();

        $transaction = $DB->start_delegated_transaction();
        $stream = (new stream_repository())->read($streamid);
        $blocks = (new block_repository())->read_by_streamid($stream->id);
        $this->assertEquals(1, count($blocks));
        $modules = (new module_repository())->read_by_blockid($blocks[0]->id);
        $this->assertEquals(1, count($modules));
        $themes = (new theme_repository())->read_by_moduleid($modules[0]->id);
        $this->assertEquals(1, count($themes));
        $activities = (new activity_repository())->read_by_themeid($themes[0]->id);
        $this->assertEquals(3, count($activities));
        $transaction->allow_commit();
    }

    public function test_stream_fd_sync_service(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_cdo_unti2035bas');
        $timedateservice = new timedate_service();
        $logrepo = new log_record_repository();
        $logger = new log_service($timedateservice, $logrepo);

        $transaction = $DB->start_delegated_transaction();
        /** @var stream_entity $stream */
        $stream = (new stream_repository())->save($generator->get_stream_entity());
        /** @var int $streamid */
        $streamid = $stream->id;
        /** @var block_entity $block */
        $block = (new block_repository())->save($generator->get_block_entity(['streamid' => $streamid, 'type_' => 'practical']));
        /** @var int $blockid */
        $blockid = $block->id;
        /** @var module_entity */
        $module = (new module_repository())->save($generator->get_module_entity(['blockid' => $blockid]));
        /** @var int $moduleid */
        $moduleid = $module->id;
        /** @var theme_entity $theme */
        $theme = (new theme_repository())->save($generator->get_theme_entity(['moduleid' => $moduleid]));
        /** @var int themeid */
        $themeid = $theme->id;
        /** @var activity_entity */
        $activity = (new activity_repository())->save($generator->get_activity_entity([
            'themeid' => $themeid,
            'type_' => 'practice',
            'config' => $generator->get_activity_config_vo(['admittanceform' => 'offline']),
        ]));
        $transaction->allow_commit();
        (new assessment_repository())->save($generator->get_assessment_entity([
            'parentobject' => 'block_entity',
            'parentobjectid' => $blockid,
        ]));
        (new assessment_repository())->save($generator->get_assessment_entity([
            'parentobject' => 'stream_entity',
            'parentobjectid' => $streamid,
        ]));

        $transaction = $DB->start_delegated_transaction();
        $service = new stream_fd_sync_service(
            new log_service($timedateservice, new log_record_repository()),
            $timedateservice,
            new block_repository(),
            new activity_repository(),
            new assessment_repository(),
            new factdef_repository(),
        );
        $service->execute($streamid);
        $transaction->allow_commit();

        $transaction = $DB->start_delegated_transaction();
        $factdefs = (new factdef_repository())->read_all_by_streamid($streamid);
        $this->assertCount(3, $factdefs);
        $transaction->allow_commit();
    }
}
