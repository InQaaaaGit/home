<?php

namespace local_cdo_ok\controllers;

class confirm_answers extends database_controller
{
    public function __construct()
    {
        $this->table = 'local_cdo_ok_confirm_answers';
    }
}