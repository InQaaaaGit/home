<?php

require_once(__DIR__ . "/../../../config.php");

$rpd_id = optional_param('rpd_id', '000035417', PARAM_TEXT);
$pdf_c = new \block_rpd\services\pdf_rpd();
$result = $pdf_c->create_agreement_form($rpd_id);
ob_clean();
$result->Output('example_001.pdf', 'I');
