<?php
require_once(__DIR__ . "/../../config.php");

try {
    redirect('debts.php');
} catch (moodle_exception $e) {
    die();
}