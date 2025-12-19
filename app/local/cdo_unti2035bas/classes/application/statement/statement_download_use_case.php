<?php

namespace local_cdo_unti2035bas\application\statement;

use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class statement_download_use_case {
    private xapi_client $xapiclient;

    public function __construct(
        xapi_client $xapiclient
    ) {
        $this->xapiclient = $xapiclient;
    }

    public function execute(string $lrid): string {
        $statementdata = $this->xapiclient->download($lrid);
        return json_encode($statementdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
