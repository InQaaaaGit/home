<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\assessment_config_vo;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\assessment_moodle_vo;
use local_cdo_unti2035bas\domain\override_vo;


class assessment extends persistent {
    const TABLE = 'cdo_unti2035bas_assessment';

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
            'parentobject' => [
                'type' => PARAM_RAW,
                'choices' => ['stream_entity', 'block_entity', 'module_entity', 'theme_entity'],
            ],
            'parentobjectid' => [
                'type' => PARAM_INT,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'modid' => [
                'type' => PARAM_INT,
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
            'haspractice' => [
                'type' => PARAM_BOOL,
            ],
            'documenttype' => [
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

    public static function from_domain(assessment_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'parentobject' => $entity->parentobject,
            'parentobjectid' => $entity->parentobjectid,
            'modid' => $entity->moodle->modid,
            'lectureshours' => $entity->config->lectureshours,
            'workshopshours' => $entity->config->workshopshours,
            'independentworkhours' => $entity->config->independentworkhours,
            'resultcomparability' => $entity->config->resultcomparability,
            'haspractice' => $entity->config->haspractice,
            'documenttype' => $entity->config->documenttype,
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

    public function to_domain(): assessment_entity {
        return new assessment_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('parentobject'),
            $this->get('parentobjectid'),
            new assessment_moodle_vo($this->get('modid')),
            new assessment_config_vo(
                $this->get('lectureshours'),
                $this->get('workshopshours'),
                $this->get('independentworkhours'),
                $this->get('resultcomparability'),
                $this->get('haspractice'),
                $this->get('documenttype'),
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
