<?php
namespace local_cdo_unti2035bas\infrastructure\config;

use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;

class fd_schema_service {
    private string $schemafilepath;

    public function __construct(string $schemafilepath) {
        $this->schemafilepath = $schemafilepath;
    }

    public function execute(): fd_schema_vo {
        $content = file_get_contents($this->schemafilepath);
        if (!$content) {
            throw new \Exception();
        }
        $data = json_decode($content, true);
        $resultexts = [];
        $contextexts = [];
        /** @var array<string, array<string, string>> $props_result_exts */
        $props_result_exts = $data["properties"]["result"]["properties"]["extensions"]["properties"];
        foreach($props_result_exts as $name => $ext) {
            $sscanres = sscanf($ext['$ref'], "#/definitions/%s");
            $schemaref = ($sscanres ? $sscanres[0] : null) ?: "undefined";
            $resultexts[$name] = new fd_result_extension_schema_vo(
                $name,
                $ext['description'],
                $schemaref,
            );
        }
        /** @var array<string, array<string, mixed>> $props_ctx_exts */
        $props_ctx_exts = $data["properties"]["context"]["properties"]["extensions"]["properties"];
        foreach($props_ctx_exts as $name => $ext) {
            $contextexts[$name] = new fd_context_extension_schema_vo(
                $name,
                $ext['description'],
                $ext['type'],
                $ext['items']['type'] ?? null,
                $ext['examples'] ?? null,
                isset($ext['const']) ? [$ext['const']] : ($ext['enum'] ?? null),
                $ext['items']['examples'] ?? null,
                isset($ext['items']['const']) ? [$ext['items']['const']] : ($ext['items']['enum'] ?? null),
            );
        }
        return new fd_schema_vo($resultexts, $contextexts);
    }
}
