<?php

namespace local_cdo_academic_progress\output\academic_progress;

use coding_exception;
use local_cdo_ok\controllers\active_group_controller;
use local_cdo_ok\controllers\confirm_answers;
use renderer_base;
use Throwable;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class renderable implements \renderable, \templatable
{
    private string $template = 'local_cdo_academic_progress/main';

    public function get_file_rpd(string $guid_file)
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["file_id" => $guid_file]);

        try {
            return [
                'file_body' => di::get_instance()
                    ->get_request('get_file_binary')
                    ->request($options)
                    ->get_request_result()
            ];
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    /**
     * Определяет последний семестр для каждой дисциплины
     *
     * @param array $data Массив данных об успеваемости
     * @return array Массив вида [discipline_id => last_semester]
     */
    private function getDisciplineLastSemesters(array $data): array
    {
        $disciplineSemesters = [];

        foreach ($data as $dataGradebook) {
            if (!isset($dataGradebook['progress']) || !is_array($dataGradebook['progress'])) {
                continue;
            }

            foreach ($dataGradebook['progress'] as $progress) {
                if (!isset($progress['attestation']) || !is_array($progress['attestation'])) {
                    continue;
                }

                $currentSemester = $progress['semester'] ?? 0;

                foreach ($progress['attestation'] as $attestation) {
                    if (!isset($attestation['discipline']['id'])) {
                        continue;
                    }

                    $disciplineId = $attestation['discipline']['id'];
                    $attestationSemester = $attestation['semester'] ?? $currentSemester;

                    // Сохраняем максимальный семестр для каждой дисциплины
                    if (!isset($disciplineSemesters[$disciplineId]) 
                        || $disciplineSemesters[$disciplineId] < $attestationSemester) {
                        $disciplineSemesters[$disciplineId] = $attestationSemester;
                    }
                }
            }
        }

        return $disciplineSemesters;
    }

    /**
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public function get_academic_progress(): array
    {
        global $USER;

        $options = di::get_instance()->get_request_options();
        $options->set_properties(["id" => $USER->id]);
        if (is_siteadmin()) {
            $options->set_properties(["id" => 24192078]);
        }
        #$options->set_properties(["user_id" => 5]);

        $data = di::get_instance()
            ->get_request('get_academic_progress')
            ->request($options)
            ->get_request_result()
            ->to_array();

        $showSurveys = (bool)get_config('local_cdo_academic_progress', 'show_surveys_column');
        $showGroups = (bool)get_config('local_cdo_academic_progress', 'show_groups_column');
        $ag = new active_group_controller();
        $OPActive = $ag->get(['group_tab' => 1]);
        $OP = 1; // need getter from 1c TODO
        $OP_Already_answered = (bool) (new confirm_answers())->get(['integration' => $OP, 'user_id' => $USER->id])[0]->status;
        $discipline_active = $ag->get(['group_tab' => 0]);
        
        // Собираем информацию о последнем семестре для каждой дисциплины
        $disciplineLastSemesters = $this->getDisciplineLastSemesters($data);
        
        $i = 1;
        foreach ($data as &$data_gradebook) {
            $data_gradebook['show_surveys'] = $showSurveys && $discipline_active;
            $data_gradebook['show_groups'] = $showGroups && $discipline_active;
            $data_gradebook['OPActive'] = $OPActive && $OP_Already_answered;
            foreach ($data_gradebook['progress'] as &$item) {

                if ($i === 1) {
                    $item['active'] = 'active';
                    $item['show'] = 'show';
                }
                $i++;
                $item['show_surveys'] = $showSurveys && $discipline_active;
                $item['show_groups'] = $showGroups && $discipline_active;;
                foreach ($item['attestation'] as &$attestation) {
                    $ca = new confirm_answers();
                    $confirmed = $ca->get([
                        'user_id' => $USER->id,
                        'integration' => $attestation['discipline']['id']
                    ]);
                    if (!empty($attestation['hours']))
                        $attestation['hours'] = $attestation['hours'] . " / " . round($attestation['hours'] / 36, 2);
                    
                    // Определяем, является ли текущий семестр последним для дисциплины
                    $disciplineId = $attestation['discipline']['id'];
                    $currentSemester = $attestation['semester'] ?? $item['semester'] ?? 0;
                    $isLastSemester = isset($disciplineLastSemesters[$disciplineId]) 
                        && $disciplineLastSemesters[$disciplineId] === $currentSemester;
                    
                    $attestation['show_surveys'] = $showSurveys && $discipline_active && $isLastSemester;
                    $attestation['show_groups'] = $showGroups && $discipline_active && $isLastSemester;
                    $attestation['already_send'] = empty($confirmed);
                }

            }
        }
        try {
            return [
                'progress' => $data
            ];
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    /**
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output): array
    {
        global $USER;
        $array = $this->get_academic_progress();
        $array['template'] = $this->template;
        $array['fio'] = fullname($USER);
        return $array;
    }
}