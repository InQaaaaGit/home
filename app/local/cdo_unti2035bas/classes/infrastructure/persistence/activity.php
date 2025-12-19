<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\activity_config_vo;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\activity_moodle_vo;
use local_cdo_unti2035bas\domain\override_vo;


class activity extends persistent {
    const TABLE = 'cdo_unti2035bas_activity';

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function define_properties(): array {
        return [
            'lrid' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'themeid' => [
                'type' => PARAM_INT,
            ],
            'type_' => [
                'type' => PARAM_RAW,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'modid' => [
                'type' => PARAM_INT,
            ],
            'position' => [
                'type' => PARAM_INT,
            ],
            'required' => [
                'type' => PARAM_BOOL,
            ],
            'collaborative' => [
                'type' => PARAM_BOOL,
            ],
            'lectureshours' => [
                'type' => PARAM_FLOAT,
            ],
            'workshopshours' => [
                'type' => PARAM_FLOAT,
            ],
            'independentworkhours' => [
                'type' => PARAM_FLOAT,
            ],
            'resultcomparability' => [
                'type' => PARAM_INT,
            ],
            'admittanceform' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'ismanual' => [
                'type' => PARAM_BOOL,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'deleted' => [
                'type' => PARAM_BOOL,
            ],
            'changed' => [
                'type' => PARAM_BOOL,
            ],
            'version' => [
                'type' => PARAM_INT,
            ],
            'timesent' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    public static function from_domain(activity_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'themeid' => $entity->themeid,
            'type_' => $entity->type,
            'modid' => $entity->moodle->modid,
            'position' => $entity->moodle->position,
            'required' => $entity->config->required,
            'collaborative' => $entity->config->collaborative,
            'lectureshours' => $entity->config->lectureshours,
            'workshopshours' => $entity->config->workshopshours,
            'independentworkhours' => $entity->config->independentworkhours,
            'resultcomparability' => $entity->config->resultcomparability,
            'admittanceform' => $entity->config->admittanceform,
            'timestamp' => $entity->timestamp,
            'ismanual' => $entity->override->ismanual,
            'name' => $entity->override->name,
            'description' => $entity->override->description,
            'deleted' => $entity->deleted,
            'changed' => $entity->changed,
            'version' => $entity->version,
            'timesent' => $entity->timesent,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): activity_entity {
        return new activity_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('themeid'),
            $this->get('type_'),
            new activity_moodle_vo($this->get('modid'), $this->get('position')),
            new activity_config_vo(
                $this->get('required'),
                $this->get('collaborative'),
                $this->get('lectureshours'),
                $this->get('workshopshours'),
                $this->get('independentworkhours'),
                $this->get('resultcomparability'),
                $this->get('admittanceform'),
            ),
            $this->get('timestamp'),
            new override_vo(
                $this->get('ismanual'),
                $this->get('name'),
                $this->get('description'),
            ),
            $this->get('deleted'),
            $this->get('changed'),
            $this->get('version'),
            $this->get('timesent'),
        );
    }

    public static function get_sql_fields($alias, $prefix = null) {
        global $CFG;
        $fields = [];

        if ($prefix === null) {
            $prefix = str_replace('_', '', static::TABLE) . '_';
        }

        // Get the properties and move ID to the top.
        $properties = static::properties_definition();
        $id = $properties['id'];
        unset($properties['id']);
        $properties = ['id' => $id] + $properties;

        foreach ($properties as $property => $definition) {
            if (in_array($property, ['timecreated', 'timemodified', 'usermodified'])) {
                continue;
            }
            $as = $prefix . $property;
            $fields[] = $alias . '.' . $property . ' AS ' . $as;

            // Warn developers that the query will not always work.
            if ($CFG->debugdeveloper && strlen($as) > 30) {
                throw new coding_exception("The alias '$as' for column '$alias.$property' exceeds 30 characters" .
                    " and will therefore not work across all supported databases.");
            }
        }

        return implode(', ', $fields);
    }
}
