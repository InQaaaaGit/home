<?php

namespace tool_imageoptimize\factory;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use stdClass;
use tool_imageoptimize\interfaces\optimizer;

interface OptimizerFactory
{
    public function createOptimizer();
}