<?php

namespace tool_imageoptimize\factory;

use tool_imageoptimize\convert_api_pdf_compressor;


class OptimizePDFFactory implements OptimizerFactory
{

    public function createOptimizer(): convert_api_pdf_compressor
    {
        return new convert_api_pdf_compressor();
    }
}