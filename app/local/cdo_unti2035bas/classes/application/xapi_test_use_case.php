<?php
namespace local_cdo_unti2035bas\application;

use DateTime;
use local_cdo_unti2035bas\infrastructure\xapi\builders\connection_check;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;


class xapi_test_use_case {
    private xapi_client $xapiclient;

    public function __construct(xapi_client $xapiclient) {
        $this->xapiclient = $xapiclient;
    }

    public function execute(): void {
        $request = (new connection_check())->with_timestamp(new DateTime())->build();
        $this->xapiclient->send([$request]);
    }
}
