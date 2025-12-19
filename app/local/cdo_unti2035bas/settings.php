<?php
/**
@var admin_root $ADMIN
@var bool $hassiteconfig
 */

defined('MOODLE_INTERNAL') || die();


if ($hassiteconfig) {
    $page = new admin_settingpage(
        'local_cdo_unti2035bas',
        get_string('pluginsettings', 'local_cdo_unti2035bas'),
    );
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/xapiendpoint',
        get_string('xapiendpoint', 'local_cdo_unti2035bas'),
        get_string('xapiendpointhelp', 'local_cdo_unti2035bas'),
        'https://lrs-external.u2035test.ru/data/xAPI',
        PARAM_URL,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/xapikey',
        get_string('xapikey', 'local_cdo_unti2035bas'),
        get_string('xapikeyhelp', 'local_cdo_unti2035bas'),
        '',
        PARAM_RAW_TRIMMED,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/xapisecret',
        get_string('xapisecret', 'local_cdo_unti2035bas'),
        get_string('xapisecrethelp', 'local_cdo_unti2035bas'),
        '',
        PARAM_RAW_TRIMMED,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/s3endpoint',
        get_string('s3endpoint', 'local_cdo_unti2035bas'),
        get_string('s3endpointhelp', 'local_cdo_unti2035bas'),
        's3.objstor.cloud4u.com:80',
        PARAM_URL,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/s3accesskey',
        get_string('s3accesskey', 'local_cdo_unti2035bas'),
        get_string('s3accesskeyhelp', 'local_cdo_unti2035bas'),
        '',
        PARAM_RAW_TRIMMED,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/s3secretkey',
        get_string('s3secretkey', 'local_cdo_unti2035bas'),
        get_string('s3secretkeyhelp', 'local_cdo_unti2035bas'),
        '',
        PARAM_RAW_TRIMMED,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/s3bucket',
        get_string('s3bucket', 'local_cdo_unti2035bas'),
        get_string('s3buckethelp', 'local_cdo_unti2035bas'),
        '',
        PARAM_RAW_TRIMMED,
    ));
    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/s3baseurl',
        get_string('s3baseurl', 'local_cdo_unti2035bas'),
        get_string('s3baseurlhelp', 'local_cdo_unti2035bas'),
        'https://s3.objstor.cloud4u.com/',
        PARAM_URL,
    ));

    $page->add(new admin_setting_configtext(
        'local_cdo_unti2035bas/unti_user_field',
        'UNTI User Field',
        'Название кастомного поля пользователя для получения UNTI ID (например: bitrix_lead_id). Если не указано, используется ID пользователя Moodle.',
        'bitrix_lead_id',
        PARAM_TEXT,
    ));
    $page->add(
        new admin_setting_configexecutable(
            'local_cdo_unti2035bas/pathtomediainfo',
            get_string('pathtomediainfo', 'local_cdo_unti2035bas'),
            get_string('pathtomediainfodesc', 'local_cdo_unti2035bas'),
            '/usr/bin/mediainfo',
        )
    );
    $testlink = html_writer::link(
        new moodle_url('/local/cdo_unti2035bas/xapitest.php'),
        get_string('xapitest', 'local_cdo_unti2035bas'),
    );
    $page->add(new admin_setting_heading(
        'local_cdo_unti2035bas/xapitest',
        get_string('xapitest', 'local_cdo_unti2035bas'),
        get_string('xapitestdetail', 'local_cdo_unti2035bas', $testlink),
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'testxapiconf',
        'testxapiconf',
        new moodle_url('/local/cdo_unti2035bas/xapitest.php'),
        'moodle/site:config',
        true
    ));
    $testlink = html_writer::link(
        new moodle_url('/local/cdo_unti2035bas/s3test.php'),
        get_string('s3test', 'local_cdo_unti2035bas'),
    );
    $page->add(new admin_setting_heading(
        'local_cdo_unti2035bas/s3test',
        get_string('s3test', 'local_cdo_unti2035bas'),
        get_string('s3testdetail', 'local_cdo_unti2035bas', $testlink),
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'tests3conf',
        'tests3conf',
        new moodle_url('/local/cdo_unti2035bas/s3test.php'),
        'moodle/site:config',
        true
    ));
    $sending_video_link = html_writer::link(
        new moodle_url('/local/cdo_unti2035bas/xapi_send_video_statements.php'),
        get_string('send_video_statements_link', 'local_cdo_unti2035bas'),
    );
    $page->add(new admin_setting_heading(
        'local_cdo_unti2035bas/send_video_statements',
        get_string('send_video_statements_head', 'local_cdo_unti2035bas'),
        get_string('send_video_statements_desc', 'local_cdo_unti2035bas', $sending_video_link),
    ));

    // Ссылка на отдельную страницу отправки оценок
    $grade_page_link = html_writer::link(
        new moodle_url('/local/cdo_unti2035bas/grade_statements.php'),
        get_string('send_grade_statements_link', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-primary']
    );
    $page->add(new admin_setting_heading(
        'local_cdo_unti2035bas/send_grade_statements',
        get_string('send_grade_statements_head', 'local_cdo_unti2035bas'),
        get_string('send_grade_statements_desc', 'local_cdo_unti2035bas', $grade_page_link),
    ));

    // Ссылка на отдельную страницу отправки JSON схем
    $json_scheme_link = html_writer::link(
        new moodle_url('/local/cdo_unti2035bas/send_json_scheme.php'),
        get_string('send_json_scheme_link', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-secondary']
    );
    $page->add(new admin_setting_heading(
        'local_cdo_unti2035bas/send_json_scheme',
        get_string('send_json_scheme_head', 'local_cdo_unti2035bas'),
        get_string('send_json_scheme_desc', 'local_cdo_unti2035bas', $json_scheme_link),
    ));

    $ADMIN->add('localplugins', $page);
    $ADMIN->add('localplugins', new admin_externalpage(
        'cdo_unti2035bas_streams',
        get_string('streamsdetails', 'local_cdo_unti2035bas'),
        new moodle_url('/local/cdo_unti2035bas/streams.php'),
        'moodle/site:config',
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'cdo_unti2035bas_grade_statements',
        get_string('send_grade_statements_head', 'local_cdo_unti2035bas'),
        new moodle_url('/local/cdo_unti2035bas/grade_statements.php'),
        'moodle/site:config',
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'cdo_unti2035bas_send_json_scheme',
        get_string('send_json_scheme_head', 'local_cdo_unti2035bas'),
        new moodle_url('/local/cdo_unti2035bas/send_json_scheme.php'),
        'moodle/site:config',
    ));
}
