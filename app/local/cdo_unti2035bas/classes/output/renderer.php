<?php
namespace local_cdo_unti2035bas\output;

use local_cdo_unti2035bas\table\table_out_interface;
use plugin_renderer_base;
use renderable;


class renderer extends plugin_renderer_base {
    public function render(renderable $widget) {
        if ($widget instanceof table_out_interface) {
            return $this->render_table($widget);
        }
        return parent::render($widget);
    }

    protected function render_table($table): string {
        ob_start();
        try {
            $table->out();
            return ob_get_contents() ?: '';
        } finally {
            ob_end_clean();
        }
    }
}
