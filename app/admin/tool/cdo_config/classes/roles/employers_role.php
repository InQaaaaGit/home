<?php

namespace tool_cdo_config\roles;

use context;
use context_system;
use dml_exception;
use tool_cdo_config\exceptions\cdo_config_exception;

class employers_role extends base_role implements i_single_role
{
    public function get_name_capability(): string {
        return "employers_teachers";
    }

    public function get_ignore_activity_capability(): array {
        return [];
    }

    public function get_role_name(): string
    {
        return "(ЦДО) Сотрудники (ППС)";
    }

    public function get_role_shortname(): string
    {
        return "cdo_employers_teachers";
    }

    public function get_role_description(): string
    {
        return "Роль для сотрудников (ППС) и их набор прав";
    }

    /**
     * @throws cdo_config_exception
     */
    public function get_role_context(): context
    {
        try {
            return context_system::instance();
        } catch (dml_exception $e) {
            throw new cdo_config_exception(2002);
        }
    }

    public function assign_capability(capability_option $option): bool
    {
        try {
            return assign_capability(
                $option->get_capability(),
                $option->get_permission(),
                $this->get_role_id(),
                $this->get_role_context()
            );
        } catch (\coding_exception $e) {
            throw new cdo_config_exception(2003, $e->getMessage());
        }
    }

    public function un_assign_capability(capability_option $option): bool
    {
        try {
            return unassign_capability(
                $option->get_capability(),
                $this->get_role_id(),
                $this->get_role_context());
        } catch (\coding_exception $e) {
            throw new cdo_config_exception(2003, $e->getMessage());
        }
    }
}