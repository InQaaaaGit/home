<?php
namespace local_cdo_unti2035bas\form;

use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;


trait add_extension_trait {
    private function add_elements(): void {
        $mform = $this->_form;
        $extensions = [];
        $fdcontextextensionstr = get_string('fdcontextextension', 'local_cdo_unti2035bas');
        $fdresultextensionstr = get_string('fdresultextension', 'local_cdo_unti2035bas');
        foreach ($this->contextexts as $ext) {
            $extensions[$ext->name] = "{$ext->description} [{$ext->name}] [{$fdcontextextensionstr}] {{$ext->type}}";
        }
        foreach ($this->resultexts as $ext) {
            $extensions[$ext->name] = "{$ext->description} [{$ext->name}] [{$fdresultextensionstr}] {{$ext->schemaref}}";
        }
        $mform->addElement(
            'autocomplete',
            'extensionname',
            get_string('fdextension', 'local_cdo_unti2035bas'),
            $extensions,
            null,
            null,
        );
        $mform->addElement('selectyesno', 'notapplicable', get_string('fdextensionnotapplicable', 'local_cdo_unti2035bas'));
        $mform->addElement('text', 'textvalue', get_string('fdcontextextensionvalue', 'local_cdo_unti2035bas'));
        $mform->setType('textvalue', PARAM_RAW);
        $mform->addElement('text', 'score', get_string('fdresultextensionscore', 'local_cdo_unti2035bas'));
        $mform->setType('score', PARAM_RAW);
        $mform->addElement('text', 'min', get_string('fdresultextensionmin', 'local_cdo_unti2035bas'));
        $mform->setType('min', PARAM_RAW);
        $mform->addElement('text', 'max', get_string('fdresultextensionmax', 'local_cdo_unti2035bas'));
        $mform->setType('max', PARAM_RAW);
        $mform->disabledIf('textvalue', 'notapplicable', 'eq', true);
        $mform->disabledIf('score', 'notapplicable', 'eq', true);
        /** @var array<string, mixed> */
        $unitselectorsadded = [];
        /** @var array<string, mixed> */
        $resultselectorsadded = [];
        foreach ($this->resultexts as $ext) {
            $schemaid = strtolower($ext->schemaref);
            if (isset($unitselectorsadded[$schemaid])) {
                continue;
            }
            $classname = fd_result_extension_schema_vo::ALLOWED_SCHEMAREF_MAP[$ext->schemaref];
            $classname = "local_cdo_unti2035bas\\domain\\fd_extensions\\{$classname}";
            $allowedunits = $classname::ALLOWED_UNITS;
            if (!$allowedunits) {
                $unitselectorsadded[$schemaid] = false;
            } else {
                $mform->addElement(
                    'select',
                    "unit{$schemaid}",
                    get_string('fdresultextensionunit', 'local_cdo_unti2035bas'),
                    array_combine($allowedunits, $allowedunits),
                );
                $unitselectorsadded[$schemaid] = true;
            }
            $allowedresultsel = $classname::ALLOWED_RESULT_SELECTORS;
            if (!$allowedresultsel) {
                $resultselectorsadded[$schemaid] = false;
            } else {
                $mform->addElement(
                    'select',
                    "resultsel{$schemaid}",
                    get_string('fdresultextensionresultselector', 'local_cdo_unti2035bas'),
                    array_combine($allowedresultsel, $allowedresultsel),
                );
                $resultselectorsadded[$schemaid] = true;
            }
        }
        foreach ($this->contextexts as $ext) {
            $mform->hideIf('score', 'extensionname', 'eq', $ext->name);
            $mform->hideIf('min', 'extensionname', 'eq', $ext->name);
            $mform->hideIf('max', 'extensionname', 'eq', $ext->name);
            foreach ($unitselectorsadded as $schemaid => $unitsel) {
                if ($unitsel) {
                    $mform->hideIf("unit{$schemaid}", 'extensionname', 'eq', $ext->name);
                }
            }
            foreach ($resultselectorsadded as $schemaid => $resultsel) {
                if ($resultsel) {
                    $mform->hideIf("resultsel{$schemaid}", 'extensionname', 'eq', $ext->name);
                }
            }
        }
        foreach ($this->resultexts as $ext) {
            $schemaid = strtolower($ext->schemaref);
            $mform->hideIf('textvalue', 'extensionname', 'eq', $ext->name);
            foreach ($unitselectorsadded as $schemaidcur => $unitsel) {
                if ($unitsel && ($schemaidcur != $schemaid)) {
                    $mform->hideIf("unit{$schemaidcur}", 'extensionname', 'eq', $ext->name);
                }
            }
            foreach ($resultselectorsadded as $schemaidcur => $resultsel) {
                if ($resultsel && ($schemaidcur != $schemaid)) {
                    $mform->hideIf("resultsel{$schemaidcur}", 'extensionname', 'eq', $ext->name);
                }
            }
        }
        $mform->addElement('submit', 'addextension', get_string('add'));
    }
}
