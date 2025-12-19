<?php

namespace local_cdo_vkr\VKR;

interface main_interface
{
    public function get_list_of_VKR($constraint = []): array;

    public function update_data_of_VKR(array $new_data): bool;

    public function delete_data_of_VKR($id_vkr);
}