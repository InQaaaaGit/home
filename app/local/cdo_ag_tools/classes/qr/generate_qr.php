<?php

namespace local_cdo_ag_tools\qr;

class generate_qr
{
    private render_interface $render_qr;

    public function __construct(render_interface $render_qr)
    {
        $this->render_qr = $render_qr;
    }

    public function create_qrcode($params, $qr_path ): void
    {

        $result = $this->render_qr->render($params, $qr_path);

    }
}