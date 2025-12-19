<?php

namespace tool_imageoptimize;

use ConvertApi\ConvertApi;
use dml_exception;
use Exception;
use tool_imageoptimize\interfaces\optimizer;

/*require_once("../vendor/convertapi/convertapi-php/lib/ConvertApi/autoload.php");*/

class convert_api_pdf_compressor implements optimizer
{
    /**
     * @throws dml_exception
     * @throws Exception
     */
    public function __construct()
    {
        $token = get_config('tool_imageoptimize', "pdf_converter_token"); //'secret_P6RpeCk5eqmuqnG7'
        if (empty($token)) {
            throw new Exception('empty token');
        }
        ConvertApi::setApiCredentials($token);
    }

    public function optimize($from, $to): void
    {
        $result = ConvertApi::convert(
            'compress',
            [
                'File' => $from
            ],
            'pdf'
        );
        $result->saveFiles($to);
    }
}