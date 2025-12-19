<?php

namespace local_cdo_ok\reports;

use bootstrap_renderer;
use dml_exception;
use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\helper\helper;
use local_cdo_ok\services\integration;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class printer
{
    public $answers_controller;

    public $spreadsheet;
    public $table;
    public $report;


    public function __construct(
        report_i $report
    )
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        $this->spreadsheet = new Spreadsheet();
        $this->report = $report;
        $this->table = [];
    }

    public function get_headers_strings($additional = []): array
    {
        $strings = [
            'report:fio', 'report:group', 'report:edu_structure', 'report:edu_spec',
            'report:edu_year', 'report:edu_level', 'report:edu_form'
        ];
        return (array)get_strings(
            array_merge($strings, $additional),
            'local_cdo_ok'
        );
    }

    private function prepare_info()
    {
        $this->table[] = $this->report->get_header();
        $this->table = array_merge($this->table, $this->report->get_data());
    }

    public function print(): printer
    {
        try {
            $worksheet = $this->spreadsheet->getActiveSheet();
            $this->prepare_info();
            $worksheet->fromArray($this->table);
            for ($i = 'A'; $i != $worksheet->getHighestColumn(); $i++) {
                $worksheet->getColumnDimension($i)->setAutoSize(TRUE);
            }
            foreach ($this->report->get_cells_for_merge() as $item) {
                $worksheet->mergeCells($item);
            }

            $cells_range_for_color = $this->report->get_cells_color();
            if (!empty($cells_range_for_color)) {
                foreach ($cells_range_for_color as $range) {
                    $worksheet->getStyle($range['cells'])
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($range['color']);
                    $worksheet->getStyle($range['cells'])->getFont()->setBold(true);
                }
            }

            $this->save();
            $this->spreadsheet->disconnectWorksheets();
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            bootstrap_renderer::early_error('Error ' . $e->getCode(), '', 'q', []);
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    private function save(): printer
    {
        $writer = IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $filename = $this->report->get_filename() . time();
        
        // Очистка всех буферов вывода
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Запуск нового буфера для контроля вывода
        ob_start();
        
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"$filename.xlsx\"");
        header("Cache-Control: max-age=0");
        header("Pragma: public");
        
        $writer->save('php://output');
        
        ob_end_flush();
        return $this;
    }

}