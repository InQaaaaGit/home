<?php

namespace local_cdo_unti2035bas;

use DateTime;
use local_cdo_unti2035bas\domain\fact_result_vo;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_activity;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_block;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_course;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_module;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_theme;
use local_cdo_unti2035bas\infrastructure\xapi\builders\cancel_statement;
use local_cdo_unti2035bas\infrastructure\xapi\builders\connection_check;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_activity_factdef;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_assessment;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_assessment_factdef;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_activity_fact;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_assessment_fact;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_practice_diary;
use local_cdo_unti2035bas\infrastructure\xapi\dtos\s3_file_dto;

final class xapi_schemas_test extends \advanced_testcase
{
    /**
     * @param string $samplename
     * @return array<string, mixed>
     */
    public function get_sample(string $samplename): array {
        global $CFG;
        return json_decode(
            file_get_contents(join(
                DIRECTORY_SEPARATOR,
                [
                    $CFG->dirroot,
                    'local',
                    'cdo_unti2035bas',
                    'tests',
                    'infrastructure',
                    'xapi',
                    'samples',
                    "{$samplename}.json",
                ]
            )),
            true,
        );
    }

    public function test_connection_check(): void {
        $builder = new connection_check();
        $builder->with_timestamp(new DateTime('2025-04-15T12:34:13+00:00'));
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("connection_check_req"), $data);
    }

    public function test_author_course(): void {
        $builder = new author_course();
        $builder->with_timestamp(new DateTime('2023-09-14T17:01:02+00:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_unticourseid(12345);
        $builder->with_coursename('Основы квантовой нумерологии');
        $builder->with_coursedescription('Эксклюзивный курс от мегакоуча и амбассадора гомеопатии');
        $builder->with_courseonline(true);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_course_req"), $data);
    }

    public function test_author_block(): void {
        $builder = new author_block();
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_blockname('Теоретические занятия');
        $builder->with_blockdescription('');
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_block_req"), $data);
    }

    public function test_author_module(): void {
        $builder = new author_module();
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_modulename('Собственные числа гамильтониана как предмет изучения нумерологии');
        $builder->with_moduledescription('Повтор основ квантовой механики. Уравнение Шрёдингера и его кот.');
        $builder->with_moduleposition(1);
        $builder->with_moodlemoduleid(1);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_module_req"), $data);
    }

    public function test_author_theme(): void {
        $builder = new author_theme();
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_moodlemoduleid(1);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_themename('Собственные числа гамильтониана как предмет изучения нумерологии');
        $builder->with_themedescription('Повтор основ квантовой механики. Уравнение Шрёдингера и его кот.');
        $builder->with_themeposition(1);
        $builder->with_moodlethemeid(1);
        $builder->with_untimoduleid(23);
        $builder->with_untithemeid(123);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_theme_req"), $data);
    }

    public function test_author_activity_video(): void {
        $builder = new author_activity();
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_activitytype('video');
        $builder->with_moodlemoduleid(1);
        $builder->with_moodlethemeid(1);
        $builder->with_moodleactivityid(1);
        $builder->with_videolength(5700);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_activityname('Вхождение Урана в Козерога');
        $builder->with_activitydescription('Захватывающее видео');
        $builder->with_untimoduleid(23);
        $builder->with_untithemeid(123);
        $builder->with_academichourminutes(45);
        $builder->with_lectureshours(1);
        $builder->with_workshopshours(1);
        $builder->with_independenthours(1);
        $builder->with_iswebinar(
            'www.example.com',
            new DateTime('2023-09-15T10:00:00+03:00'),
        );
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_activity_video_req"), $data);
    }

    public function test_author_activity_article(): void {
        $builder = new author_activity();
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_activitytype('article');
        $builder->with_moodlemoduleid(1);
        $builder->with_moodlethemeid(1);
        $builder->with_moodleactivityid(1);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_activityname('Sal gemmae');
        $builder->with_activitydescription('Каменная соль: история добычи');
        $builder->with_untimoduleid(23);
        $builder->with_untithemeid(123);
        $builder->with_academichourminutes(45);
        $builder->with_lectureshours(1);
        $builder->with_workshopshours(1);
        $builder->with_independenthours(1);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample("author_activity_article_req"), $data);
    }

    public function test_host_asessment_block(): void {
        $builder = new host_assessment();
        $builder->with_lrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix('https://PROVIDER.ru');
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_THEORETICAL);
        $builder->with_assessmentlevel('block');
        $builder->with_moodleactivityid(15);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_activityname('Тест в блоке');
        $builder->with_activitydescription('Промежуточная аттестация на уровне блока');
        $builder->with_academichourminutes(45);
        $builder->with_lectureshours(1);
        $builder->with_workshopshours(1);
        $builder->with_independenthours(1);
        $builder->with_practice(true);
        $builder->with_resultcomparability(true);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_assessment_block_req'), $data);
    }

    public function test_host_asessment_module(): void {
        $builder = new host_assessment();
        $builder->with_lrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix('https://PROVIDER.ru');
        $builder->with_actorname("11111");
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_PRACTICAL);
        $builder->with_assessmentlevel('module');
        $builder->with_moodlemoduleid(1);
        $builder->with_moodleactivityid(7);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_untimoduleid(8);
        $builder->with_activityname('Промежуточная аттестация по модулю 1');
        $builder->with_activitydescription('Проверка усвоения материала');
        $builder->with_academichourminutes(45);
        $builder->with_lectureshours(1);
        $builder->with_workshopshours(1);
        $builder->with_independenthours(1);
        $builder->with_practice(true);
        $builder->with_resultcomparability(true);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_assessment_module_req'), $data);
    }

    public function test_host_asessment_final(): void {
        $builder = new host_assessment();
        $builder->with_lrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix('https://PROVIDER.ru');
        $builder->with_actorname('11111');
        $builder->with_uniqid('1');
        $builder->with_assessmentlevel('final');
        $builder->with_moodleactivityid(4);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_activityname('Итоговая аттестация');
        $builder->with_activitydescription('Экзамен на звание магистра квантовой нумерологии и пурпурную мантию');
        $builder->with_academichourminutes(45);
        $builder->with_lectureshours(1);
        $builder->with_workshopshours(1);
        $builder->with_independenthours(1);
        $builder->with_practice(true);
        $builder->with_resultcomparability(true);
        $builder->with_documenttype('Диплом');
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_assessment_final_req'), $data);
    }

    public function test_cancel_statement(): void {
        $builder = new cancel_statement();
        $builder->with_lrid('d2e9a6e3-725d-4b84-8a68-950a672ae3f6');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname('22222');
        $builder->with_statementlrid('67c5e4b0-9099-4e1d-ab88-34c1c24e622b');
        $builder->with_objectid('https://PROVIDER.ru/videos/1');
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('cancel_statement_req'), $data);
    }

    public function test_passed_practice_diary(): void {
        $builder = new passed_practice_diary();
        $builder->with_lrid('b5ca43b7-081a-430c-9292-05f56fc34eca');
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_actorname('11111');
        $builder->with_practiceblocklrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_diaryfile(new s3_file_dto(
            'https://s3.dtln.ru/PROVIDER/course/1/practice/media/diary-11111.pdf',
            'application/pdf',
            1567163,
            '71662bd35817bc32c9214911f0c7233f4e415cb8742e95725c54750b1db6b824',
        ));
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('passed_practice_diary_req'), $data);
    }

    public function test_host_activity_factdef(): void {
        $builder = new host_activity_factdef();
        $builder->with_lrid('566af81b-75f3-4540-b802-78e5d653a033');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname('11111');
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_PRACTICAL);
        $builder->with_moodlemoduleid(1);
        $builder->with_moodlethemeid(1);
        $builder->with_moodleactivityid(1);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_resultcomparability(true);
        $builder->with_instructorname('33333');
        $fdcontextextensions = [
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/method_of_fixation',
                'Фиксация в LMS',
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/id_of_responsible_for_fixation',
                ['33333', '44444'],
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/role_of_responsible_for_fixation',
                ['Методист', 'Методист'],
            ),
        ];
        $builder->with_fdcontextextensions($fdcontextextensions);
        $builder->with_fdresultextensions([]);
        $builder->with_admittanceform('online');
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_activity_factdef_req'), $data);
    }

    public function test_host_assessment_block_factdef(): void {
        $builder = new host_assessment_factdef();
        $builder->with_lrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname('11111');
        $builder->with_uniqid('1');
        $builder->with_blocktype(author_block::BLOCK_TYPE_PRACTICAL);
        $builder->with_moodleactivityid(51);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_resultcomparability(true);
        $builder->with_assessmentlevel('block');
        $builder->with_fdcontextextensions([]);
        $builder->with_fdresultextensions([]);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_assessment_block_factdef_req'), $data);
    }

    public function test_host_assessment_final_factdef(): void {
        $builder = new host_assessment_factdef();
        $builder->with_lrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_actorname('11111');
        $builder->with_uniqid('1');
        $builder->with_moodleactivityid(5);
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_resultcomparability(true);
        $builder->with_assessmentlevel('final');
        $builder->with_fdcontextextensions([]);
        $builder->with_fdresultextensions([]);
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('host_assessment_final_factdef_req'), $data);
    }

    public function test_passed_activity_fact(): void {
        $builder = new passed_activity_fact();
        $builder->with_lrid('77941234-af77-487d-b267-c83fa521c918');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_uniqid('1');
        $builder->with_actorname('11111');
        $builder->with_factdeflrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $builder->with_blocktype(author_block::BLOCK_TYPE_PRACTICAL);
        $builder->with_moodlemoduleid(1);
        $builder->with_moodlemodulepos(1);
        $builder->with_moodlethemeid(1);
        $builder->with_moodleactivityid(3);
        $fdcontextextensions = [
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/method_of_fixation',
                'Фиксация в LMS',
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/id_of_responsible_for_fixation',
                ['33333', '44444'],
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/role_of_responsible_for_fixation',
                ['Методист', 'Методист'],
            ),
        ];
        $builder->with_fdcontextextensions($fdcontextextensions);
        $builder->with_fdresultextensions([]);
        $builder->with_result(new fact_result_vo(5, 0, 5, 'max', true, 'PT40S', 1, 1));
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('passed_activity_fact_req'), $data);
    }

    public function test_passed_assessment_fact(): void {
        $builder = new passed_assessment_fact();
        $builder->with_lrid('77941234-af77-487d-b267-c83fa521c918');
        $builder->with_timestamp(new DateTime('2023-09-14T20:01:02+03:00'));
        $builder->with_prefix("https://PROVIDER.ru");
        $builder->with_uniqid('1');
        $builder->with_actorname('11111');
        $builder->with_factdeflrid('16454473-b270-42c2-a407-f37d235c0db7');
        $builder->with_unticourseid(12345);
        $builder->with_untiflowid(234);
        $fdcontextextensions = [
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/method_of_fixation',
                'Фиксация в LMS',
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/id_of_responsible_for_fixation',
                ['33333', '44444'],
            ),
            new factdef_context_extension_vo(
                'https://id.2035.university/xapi/extension/role_of_responsible_for_fixation',
                ['Методист', 'Методист'],
            ),
            new factdef_context_extension_vo(
                'https://api.2035.university/qualification',
                'Оператор БПЛА',
            ),
        ];
        $builder->with_fdcontextextensions($fdcontextextensions);
        $builder->with_fdresultextensions([]);
        $builder->with_result(new fact_result_vo(5, 0, 5, 'max', true, 'PT2M11S', 1, 1));
        $request = $builder->build();
        $data = $request->dump();
        $this->assertEquals($this->get_sample('passed_assessment_fact_req'), $data);
    }
}
