<?php

use local_cdo_ag_tools\tasks\sender_notifications_task;

require_once (__DIR__.'/../../config.php');
global $CFG;
require_once ($CFG->libdir.'/gradelib.php');
require_once ($CFG->libdir.'/grade/grade_item.php');
require_once ($CFG->libdir.'/grade/constants.php');
require_once ($CFG->libdir.'/grade/grade_grade.php');
(new sender_notifications_task())->execute();