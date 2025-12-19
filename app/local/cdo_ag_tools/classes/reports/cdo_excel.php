<?php

namespace local_cdo_ag_tools\reports;

use core_useragent;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;

class cdo_excel extends \MoodleExcelWorkbook
{
    protected string $files;

    public function add_file($file): void
    {
        $this->files = $file;
    }

    public function get_files(): string
    {
        return $this->files;
    }

    protected int $count_to_block_column = 0;

    public function setCountToBlockColumn($col): void
    {
        $this->count_to_block_column = $col;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function close(): void
    {
        global $CFG;
        $this->objspreadsheet->getActiveSheet()->getProtection()->setSheet(true);
        $this->objspreadsheet->getDefaultStyle()->getProtection()->setLocked(false);
        $protection = new Protection();
        foreach ($this->objspreadsheet->getAllSheets() as $sheet) {
            foreach ($this->objspreadsheet->getActiveSheet()->getColumnIterator() as $column) {
                $this->objspreadsheet->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
            $highestRow = $sheet->getHighestRow();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

            $sheet->getStyle([1, 1, $highestColumnIndex, $highestRow])
                ->getAlignment()
                ->setWrapText(true);
            $sheet->getStyle([1, 1, $highestColumnIndex, $highestRow])
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            $sheet->getStyle([1, 1, $highestColumnIndex, 1])
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
            $sheet->getStyle([1, 1, $this->count_to_block_column, $highestRow])
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

            $sheet->setSelectedCells('A1');

        }
        $this->objspreadsheet->setActiveSheetIndex(0);
        /*$this->objspreadsheet->getActiveSheet()->getProtection()->setSheet(true);*/


        $filename = preg_replace('/\.xlsx?$/i', '', $this->filename);

        $mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $filename = $filename . '.xlsx';

        if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: max-age=10');
            header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
            header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
            header('Pragma: no-cache');
        }

        if (core_useragent::is_ie() || core_useragent::is_edge()) {
            $filename = rawurlencode($filename);
        } else {
            $filename = s($filename);
        }

        /*        header('Content-Type: '.$mimetype);
                header('Content-Disposition: attachment;filename="'.$filename.'"');*/
        global $CFG;
        $objwriter = IOFactory::createWriter($this->objspreadsheet, $this->type);
        $temp_file = $CFG->tempdir . '/grades/' . $filename;
        file_put_contents($temp_file, '');
        $objwriter->save($temp_file);
        $this->add_file($temp_file);
        #$objwriter->save('php://output');
    }
}