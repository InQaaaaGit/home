<?php

namespace local_cdo_unti2035bas\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use local_cdo_unti2035bas\ui\dependencies;


class factdef_extension_delete extends external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'factdefid' => new external_value(PARAM_INT, 'Fartdef id'),
            'extensionname' => new external_value(PARAM_RAW, 'Extension name'),
        ]);
    }

    public static function execute_returns(): void {
        return;
    }

    public static function execute(int $factdefid, string $extensionname): void {
        extract(static::validate_parameters(
            static::execute_parameters(),
            compact(array_keys(static::execute_parameters()->keys)),
        ));
        $context = context_system::instance();
        static::validate_context($context);

        $depends = new dependencies();
        $usecase = $depends->get_factdef_extension_delete_use_case();

        $usecase->execute($factdefid, $extensionname);
    }
}
