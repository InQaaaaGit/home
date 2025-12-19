<?php

namespace local_cdo_ok\reports;

interface report_i
{
    public function get_header(): array;

    public function get_data(): array;

    public function get_cells_for_merge(): array;

    public function get_filename(): string;

    public function get_cells_color(): array;
}