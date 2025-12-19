<?php

namespace local_cdo_ag_tools\qr;

use assignfeedback_editpdf\pdf;
use dml_exception;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

class generate_pdf
{
    const plugin_name = 'local_cdo_ag_tools';
    private generate_qr $generate_qr;

    public function __construct(generate_qr $generate_qr)
    {
        $this->generate_qr = $generate_qr;
    }

    /**
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws FilterException
     * @throws dml_exception
     */
    public function generate_pdf_with_qrcode($path_to_work, $qrcode_path, $member, $section_id): array
    {
        $redacting_pdf_class = new pdf();
        $page_count = 0; $insert_image= false;
        if (is_file($path_to_work) && pathinfo($path_to_work, PATHINFO_EXTENSION) === 'pdf') {
            $page_count = $redacting_pdf_class->setSourceFile($path_to_work);
        }
        for ($i = 1; $i <= $page_count; $i++) {
            // Импортируем каждую страницу
            $tplId = $redacting_pdf_class->importPage($i);
            $redacting_pdf_class->AddPage();

            // Используем шаблон страницы
            $redacting_pdf_class->useTemplate($tplId);
            //first page
            if ($i === 1) {
                $params = [
                    'user_id' => $member->id,
                    'item' => $section_id
                ];
                $this->generate_qr->create_qrcode($params, $qrcode_path);
                $x = get_config(self::plugin_name, 'qr_code_x');
                $y = get_config(self::plugin_name, 'qr_code_y');
                $size = get_config(self::plugin_name, 'qr_code_size');
                $insert_image = $redacting_pdf_class->Image($qrcode_path, $x, $y, $size, $size);
                $x_fio = get_config(self::plugin_name, 'fio_x');
                $y_fio = get_config(self::plugin_name, 'fio_y');
                $redacting_pdf_class->SetXY($x_fio, $y_fio);
                #$redacting_pdf_class->setFont('times', '', 14);
                #$redacting_pdf_class->setFontSize(14);
                $redacting_pdf_class->Write(0, fullname($member));

            }
        }
        $files_to_zip = []; // TODO?
        if ($insert_image !== false) {
            $redacting_pdf_class->Output($path_to_work, 'F');
            $files_to_zip[] = $path_to_work;
        } else {
            // TODO error qr code insert
        }
        return $files_to_zip;
    }
}