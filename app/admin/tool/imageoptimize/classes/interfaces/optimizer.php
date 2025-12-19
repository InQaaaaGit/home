<?php

namespace tool_imageoptimize\interfaces;

interface optimizer
{
    public function optimize(string $from, string $to);
}