<?php

use local_cdo_vkr\utility\external_return_types;
use local_cdo_vkr\utility\file_of_vkr;
use local_cdo_vkr\utility\fileinfo;
use local_cdo_vkr\VKR\create_complex_pdf;
use local_cdo_vkr\VKR\vkr_ebs;

require(__DIR__ . "/../../config.php");
$contextid = context_system::instance()->id;

