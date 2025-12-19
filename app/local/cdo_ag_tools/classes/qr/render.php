<?php

namespace local_cdo_ag_tools\qr;

use Matrix\Decomposition\QR;
use QRcode;

class render implements render_interface
{

    public function render($params, $path) {
        global $CFG;
        require_once $CFG->dirroot . '/local/cdo_ag_tools/phpqrcode-git/lib/full/qrlib.php';

        return QRcode::png(json_encode($params), $path);
    }
}