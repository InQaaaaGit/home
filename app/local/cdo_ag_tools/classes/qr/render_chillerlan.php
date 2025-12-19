<?php

namespace local_cdo_ag_tools\qr;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class render_chillerlan implements render_interface
{
    public function render($params, $path) {
        global $CFG;
        $options = new QROptions;
        $options->version = 7;
        $options->scale = 2;

        $qr = new QRCode($options);
        return $qr->render(json_encode($params), $path);
    }
}