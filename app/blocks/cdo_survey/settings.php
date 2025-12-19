<?php

$settings->add(new admin_setting_configtext(
    'block_cdo_survey/api_token',
    get_string('api_token', 'block_cdo_survey'),
    get_string('api_token_desc', 'block_cdo_survey'),
    '7a47bae38496a511b54ca371b0b8ec12b40a457b',
    PARAM_TEXT
));

$settings->add(new admin_setting_configtext(
    'block_cdo_survey/guid_passport_rf',
    get_string('guid_passport_rf', 'block_cdo_survey'),
    get_string('guid_passport_rf_desc', 'block_cdo_survey'),
    '672b75ac-9346-11ee-8c0a-50ebf681124f',
    PARAM_TEXT
));
