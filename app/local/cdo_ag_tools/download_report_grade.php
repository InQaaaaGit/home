<?php
require_once(__DIR__ . "/../../config.php");
require_login();
$c = new \local_cdo_ag_tools\reports\partners_print_grades();
$c->init();
