<?php

namespace local_cdo_vkr\utility;

use assignfeedback_editpdf\pdf;
use coding_exception;
use lang_string;
use local_cdo_vkr\VKR\create_complex_pdf;

class pdf_vkr extends create_complex_pdf
{
    private $pdf;
    private $string_year_template;
    /**
     * @var mixed
     */
    private $parent;

    public function __construct(
        pdf $pdf,
            $parent
    )
    {
        $this->pdf = $pdf;
        $pdf->addPage();

        $this->parent = $parent;
    }

    /**
     * @throws coding_exception
     */
    protected function create_footer()
    {
        $this->pdf->MultiCell(
            0, 0,
            get_string('year_template', 'local_cdo_vkr', $this->parent->year),
            0, 'С', 0, 1, 90, 260
        );
    }

    /**
     * @throws coding_exception
     */
    public function create_title_for_vkr()
    {
        $this->pdf->SetFontSize(12);
        $this->pdf->Cell(
            0, 20,
            strtoupper(get_string('institute', 'local_cdo_vkr')),
            0, 1,
            'C', 0
        );
        $this->pdf->Cell(
            0, 0,
            $this->parent->edu_division,
            0, 1,
            'C', 0,

        );
        $this->pdf->SetFontSize(8);
        $this->pdf->Cell(
            0, 0,
            get_string('name_edu_division', 'local_cdo_vkr'),
            0, 1,
            'C', 0,
            '', 0,
            0,
            'T'
        );
        # $this->pdf
        $this->pdf->SetFontSize(12);
        $this->pdf->SetFont('', 'b');
        $this->pdf->Cell(
            0, 60,
            strtoupper(get_string('VKR', 'local_cdo_vkr')),
            0, 1,
            'C', 0,
            '', 0
        );
        $this->pdf->SetFontSize(12);
        $this->pdf->SetFont('', '');
        $this->pdf->MultiCell(
            150, 0,
            get_string('theme_name', 'local_cdo_vkr', $this->parent->theme_name),
            0, 'C', 0, 1, 30, 85);
        $this->pdf->MultiCell(
            150, 0,
            get_string('edu_specialization', 'local_cdo_vkr', $this->parent->edu_specialization_code . " " . $this->parent->edu_specialization),
            0, 'C', 0, 1, 30, 125);
        $this->pdf->MultiCell(
            150, 0,
            get_string('edu_profile', 'local_cdo_vkr', $this->parent->edu_profile),
            0, 'C', 0, 1, 30, 135);
        $this->pdf->MultiCell(
            0, 0,
            get_string('student_course', 'local_cdo_vkr', $this->parent->student_course),
            0, 'L', 0, 1, 15, 165);
        $this->pdf->MultiCell(
            0, 0,
            get_string('edu_group', 'local_cdo_vkr', $this->parent->edu_group),
            0, 'L', 0, 1, 15, 170);

        $this->pdf->MultiCell(
            0, 0,
            $this->parent->fio,
            0, 'L', 0, 1, 15, 175);
        $this->create_footer();
        return $this->pdf;
    }


    public function create_title_for_review($title_main = '', $title_fio = '')
    {
        if (empty($title_main)) {
            $title_main = get_string('title_review', 'local_cdo_vkr');
        }

        if (empty($title_fio)) {
            $title_fio = get_string('manager_fio_comment', 'local_cdo_vkr', $this->parent->reviewer);
        }

        $this->pdf->SetFontSize(14);
        $this->pdf->SetFont('', 'b');
        $this->pdf->Cell(
            0, 20,
            get_string('institute', 'local_cdo_vkr'),
            0, 1,
            'C', 0
        );
        $this->pdf->SetFont('', '');
        $this->pdf->Cell(
            0, 0,
            $this->parent->edu_division,
            0, 1,
            'C', 0,

        );
        $this->pdf->SetFontSize(8);
        $this->pdf->Cell(
            0, 0,
            get_string('name_edu_division', 'local_cdo_vkr'),
            0, 1,
            'C', 0,
            '', 0,
            0,
            'T'
        );
        $this->pdf->SetFontSize(12);
        $this->pdf->SetFont('', 'b');
        $this->pdf->MultiCell(
            180, 0,
            $title_main,
            0, 'C', 0, 1, 20, 80);
        $this->pdf->SetFont('', '');
        $this->pdf->MultiCell(
            0, 20,
            get_string('theme_name_comment', 'local_cdo_vkr', $this->parent->theme_name),
            0, 'L', 0, 1, 30, 120);
        $this->pdf->MultiCell(
            0, 0,
            get_string('student_comment', 'local_cdo_vkr', $this->parent->fio),
            0, 'L', 0, 1, 30, 141);
        $this->pdf->MultiCell(
            0, 0,
            get_string('edu_specialization', 'local_cdo_vkr', $this->parent->edu_specialization_code . " " . $this->parent->edu_specialization),
            0, 'L', 0, 1, 30, 150);
        $this->pdf->MultiCell(
            0, 0,
            $title_fio,
            0, 'L', 0, 1, 30, 170);
        $this->create_footer();
    }

    public function create_title_for_comment()
    {
        $this->create_title_for_review(
            get_string('title_comment', 'local_cdo_vkr'),
            get_string('manager_fio', 'local_cdo_vkr', $this->parent->fio_manager)

        );
    }
}