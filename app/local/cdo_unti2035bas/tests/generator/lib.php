<?php

use local_cdo_unti2035bas\domain\activity_config_vo;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\activity_moodle_vo;
use local_cdo_unti2035bas\domain\assessment_config_vo;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\assessment_moodle_vo;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\block_moodle_vo;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\module_moodle_vo;
use local_cdo_unti2035bas\domain\module_unti_vo;
use local_cdo_unti2035bas\domain\override_vo;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\stream_moodle_vo;
use local_cdo_unti2035bas\domain\stream_unti_vo;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\domain\theme_moodle_vo;
use local_cdo_unti2035bas\domain\theme_unti_vo;


class local_cdo_unti2035bas_generator extends \component_generator_base {
    public function get_override_vo(
        bool $ismanual = false,
        ?string $name = null,
        ?string $description = null
    ): override_vo {
        return new override_vo($ismanual, $name, $description);
    }

    public function get_stream_moodle_vo(
        int $courseid = 100,
        int $groupid = 15,
        int $sectionid = 13
    ): stream_moodle_vo {
        return new stream_moodle_vo($courseid, $groupid, $sectionid);
    }

    public function get_stream_unti_vo(
        ?string $uniqid = null,
        int $programid = 200,
        int $flowid = 30,
        int $methodistid = 14
    ): stream_unti_vo {
        return new stream_unti_vo($uniqid, $programid, $flowid, $methodistid);
    }

    /**
     * @param array<string, mixed> $data
     * @return stream_entity
     */
    public function get_stream_entity(array $data = []): stream_entity {
        return new stream_entity(
            $data['id'] ?? null,
            $data['lrid'] ?? null,
            $data['timestamp'] ?? 1747949063,
            $data['moodle'] ?? $this->get_stream_moodle_vo(),
            $data['unti'] ?? $this->get_stream_unti_vo(),
            $data['academichourminutes'] ?? 45,
            $data['isonline'] ?? true,
            $data['comment'] ?? 'test stream',
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
            $data['fdextensions'] ?? [],
        );
    }

    public function get_block_moodle_vo(int $sectionid = 11): block_moodle_vo {
        return new block_moodle_vo($sectionid);
    }

    /**
     * @param array<string, mixed> $data
     * @return block_entity
     */
    public function get_block_entity(array $data = []): block_entity {
        return new block_entity(
            $data['id'] ?? null,
            $data['streamid'],
            $data['type_'] ?? 'theoretical',
            $data['lrid'] ?? null,
            $data['timestamp'] ?? 1747949064,
            $data['moodle'] ?? $this->get_block_moodle_vo(),
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
        );
    }

    public function get_module_moodle_vo(
        int $sectionid = 12,
        int $position = 1
    ): module_moodle_vo {
        return new module_moodle_vo($sectionid, $position);
    }

    public function get_module_unti_vo(int $moduleid = 1003): module_unti_vo {
        return new module_unti_vo($moduleid);
    }

    /**
     * @param array<string, mixed> $data
     * @return module_entity
     */
    public function get_module_entity(array $data = []): module_entity {
        return new module_entity(
            $data['id'] ?? null,
            $data['lrid'] ?? null,
            $data['blockid'],
            $data['moodle'] ?? $this->get_module_moodle_vo(),
            $data['unti'] ?? $this->get_module_unti_vo(),
            $data['timestamp'] ?? 1747949065,
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
        );
    }

    public function get_theme_moodle_vo(
        int $sectionid = 12,
        int $position = 1
    ): theme_moodle_vo {
        return new theme_moodle_vo($sectionid, $position);
    }

    public function get_theme_unti_vo(int $themeid = 1003): theme_unti_vo {
        return new theme_unti_vo($themeid);
    }

    /**
     * @param array<string, mixed> $data
     * @return theme_entity
     */
    public function get_theme_entity(array $data = []): theme_entity {
        return new theme_entity(
            $data['id'] ?? null,
            $data['lrid'] ?? null,
            $data['moduleid'],
            $data['moodle'] ?? $this->get_theme_moodle_vo(),
            $data['unti'] ?? $this->get_theme_unti_vo(),
            $data['timestamp'] ?? 1747949066,
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
        );
    }

    public function get_activity_moodle_vo(
        int $modid = 17,
        int $position = 1
    ): activity_moodle_vo {
        return new activity_moodle_vo($modid, $position);
    }

    /**
     * @param array<string, mixed> $data
     * @return activity_config_vo
     */
    public function get_activity_config_vo(array $data = []): activity_config_vo {
        return new activity_config_vo(
            $data['required'] ?? true,
            $data['collaborative'] ?? false,
            $data['lectureshours'] ?? 1,
            $data['workshopshours'] ?? 1,
            $data['independentworkhours'] ?? 1,
            $data['resultcomparability'] ?? 1,
            $data['admittanceform'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return activity_entity
     */
    public function get_activity_entity(array $data = []): activity_entity {
        return new activity_entity(
            $data['id'] ?? null,
            $data['lrid'] ?? null,
            $data['themeid'],
            $data['type_'] ?? 'article',
            $data['moodle'] ?? $this->get_activity_moodle_vo(),
            $data['config'] ?? $this->get_activity_config_vo(),
            $data['timestamp'] ?? 1747949066,
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
        );
    }

    public function get_assessment_moodle_vo(
        int $modid = 71
    ): assessment_moodle_vo {
        return new assessment_moodle_vo($modid);
    }

    /**
     * @param array<string, mixed> $data
     * @return assessment_config_vo
     */
    public function get_assessment_config_vo(array $data = []): assessment_config_vo {
        return new assessment_config_vo(
            $data['lectureshours'] ?? 1,
            $data['workshopshours'] ?? 1,
            $data['independentworkhours'] ?? 1,
            $data['resultcomparability'] ?? true,
            $data['haspractice'] ?? false,
            $data['documenttype'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return activity_entity
     */
    public function get_assessment_entity(array $data = []): assessment_entity {
        return new assessment_entity(
            $data['id'] ?? null,
            $data['lrid'] ?? null,
            $data['parentobject'],
            $data['parentobjectid'],
            $data['moodle'] ?? $this->get_assessment_moodle_vo(),
            $data['config'] ?? $this->get_assessment_config_vo(),
            $data['timestamp'] ?? 1747949066,
            $data['override'] ?? $this->get_override_vo(),
            $data['deleted'] ?? false,
            $data['changed'] ?? true,
            $data['version'] ?? 1,
            $data['timesent'] ?? null,
        );
    }
}
