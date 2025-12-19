<?php

namespace local_cdo_vkr\VKR;

class layer
{
    private $work_with_VKR;

    public function __construct($work_with_VKR)
    {
        $this->work_with_VKR = $work_with_VKR;
    }

    public function change_status_of_vkr($params): bool
    {
        return $this->work_with_VKR->update_data_of_VKR($params);
    }

    public function get_list_of_vkr($params = []): array
    {
        return $this->work_with_VKR->get_list_of_VKR($params);
    }

    public function delete_vkrs($id_vkr)
    {
        return $this->work_with_VKR->delete_data_of_VKR($id_vkr);
    }
}