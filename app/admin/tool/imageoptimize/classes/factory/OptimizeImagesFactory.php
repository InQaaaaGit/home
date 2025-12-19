<?php

namespace tool_imageoptimize\factory;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class OptimizeImagesFactory implements OptimizerFactory
{

    public function createOptimizer(): OptimizerChain
    {
        return OptimizerChainFactory::create();
    }
}