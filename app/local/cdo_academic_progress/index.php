<?php
require_once(__DIR__ . "/../../config.php");

try {
    redirect('academic_progress.php');
} catch (moodle_exception $e) {
    die();
}