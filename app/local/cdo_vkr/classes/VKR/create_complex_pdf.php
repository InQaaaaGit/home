<?php

namespace local_cdo_vkr\VKR;

use assignfeedback_editpdf\pdf;
use coding_exception;
use Exception;
use local_cdo_vkr\utility\pdf_vkr;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use stdClass;
use setasign\Fpdi;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;

class create_complex_pdf
{
    protected $temp_dir;
    protected $dir;
    protected $temp_files;
    /**
     * @var mixed
     */
    public $id_vkr;
    public $edu_division;
    public $theme_name;
    public $fio;
    public $edu_specialization;
    public $fio_manager;
    public $year;
    public $edu_profile;
    public $edu_group;
    public $student_course;
    public $edu_specialization_code;
    public $number_document;
    public $edu_lectern;
    public $edu_form;
    public $edu_level;
    public $end_filename;
    public $end_filename_path;
    public $end_filename_type;
    public $end_filename_size;
    public $reviewer;
    public $key;

    /**
     */
    public function __construct(
        $id_vkr,
        $edu_division,
        $theme_name,
        $fio,
        $edu_specialization,
        $fio_manager,
        $year,
        $edu_profile,
        $edu_group,
        $student_course,
        $edu_specialization_code,
        $number_document,
        $edu_lectern = '',
        $edu_form = '',
        $edu_level = '',
        $reviewer = ''
    )
    {
        $this->create_temp_dir($year);
        $this->id_vkr = $id_vkr;
        $this->edu_division = $edu_division;
        $this->theme_name = $theme_name;
        $this->fio = $fio;
        $this->edu_specialization = $edu_specialization;
        $this->fio_manager = $fio_manager;
        $this->year = $year;
        $this->edu_profile = $edu_profile;
        $this->edu_group = $edu_group;
        $this->student_course = $student_course;
        $this->edu_specialization_code = $edu_specialization_code;
        $this->number_document = $number_document;
        $this->edu_lectern = $edu_lectern;
        $this->edu_form = $edu_form;
        $this->edu_level = $edu_level;
        $this->reviewer = $reviewer;
        $this->create_key_column();
    }

    public function create_key_column()
    {
        $key = explode(" ", $this->theme_name);
        foreach ($key as $k => $item) {
            if (strlen($item) <= 3) {
                unset($key[$k]);
            }
        }
        $key_string = implode(", ", $key);
        $this->key = $key_string;
    }

    protected function create_temp_dir($year)
    {
        $this->temp_dir = "/www/vkr/$year/temp/";
        if (!is_dir($this->temp_dir)) {
            mkdir($this->temp_dir);
        }
        $this->dir = "/www/vkr/$year/";
        if (!is_dir($this->dir)) {
            mkdir($this->dir);
        }
    }

    /**
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws FilterException
     * @throws coding_exception
     */
    public function start_process($files): bool
    {

        foreach ($files as $file_type => $file) {
            $this->create_work_pdf($file, $file_type);
        }

        return $this->create_union_file();
    }

    protected function delete_directory($dirname)
    {
        $dir_handle = false;
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    delete_directory($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws FilterException
     * @throws PdfTypeException
     */
    private function create_union_file(): bool
    {
        $pdf = new pdf();
        $pdf->setPrintHeader(false);
        krsort($this->temp_files);
        $new_files = [];
        foreach ($this->temp_files as $file) {
            $new_files[$file->priority] = $file->file;
        }
        foreach ($new_files as $file) {
            $pagecount = $pdf->setSourceFile($file);

            for ($i = 1; $i <= $pagecount; $i++) {
                $tplidx = $pdf->ImportPage($i);
                $s = $pdf->getTemplatesize($tplidx);
                if (is_bool($s)) {
                    continue;
                }
                $pdf->AddPage('P', array($s['w'], $s['h']));
                $pdf->useTemplate($tplidx);
            }
        }
        $this->end_filename = $this->fio . '_' .
            $this->edu_specialization_code . '_' .
            $this->number_document;
        $this->end_filename_type = 'pdf';
        $this->end_filename_path =
            $this->dir .
            $this->end_filename . '.' .
            $this->end_filename_type;
        ob_clean();
        $result = $pdf->Output($this->end_filename_path, 'F');
        $this->end_filename_size = filesize($this->end_filename_path);
        return $this->delete_directory($this->temp_dir);
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws coding_exception
     * @throws PdfParserException
     * @throws FilterException
     * @throws PdfTypeException
     */
    private function create_work_pdf($file, $state = 'work')
    {

        $pdf = new pdf();
        $pdf->setPrintHeader(false);
        $fs = get_file_storage();
        $work_file = $fs->get_file_by_id($file['id']);
        if (!is_bool($work_file)) {
            require_once __DIR__.'/../../vendor/autoload.php';
            $command = new GhostscriptConverterCommand();
            $filesystem = new Filesystem();
            $converter = new GhostscriptConverter($command, $filesystem);

            $filepath = $work_file->copy_content_to_temp();
            $converter->convert($filepath, '1.4');

            $guesser = new RegexGuesser();
            $v = $guesser->guess($filepath);
           /* if ($v==='1.4') {
                throw new coding_exception($v);
            } else {
                throw new coding_exception($v . " > 1.5");
            }*/
            try {
                $number_pages = $pdf->setSourceFile($filepath);

            } catch (PdfParserException $e) {
                throw new coding_exception($e->getLine() . $e->getMessage() . $e->getFile());
            }
            $pdf_vkr = new pdf_vkr($pdf, $this);
            switch ($state) {
                case 'work':
                    $priority = 0;
                    $pdf_vkr->create_title_for_vkr();
                    break;
                case 'comment':
                    $priority = 1;
                    $pdf_vkr->create_title_for_comment();
                    break;
                case 'review':
                    $priority = 2;
                    $pdf_vkr->create_title_for_review();
                    break;
                default:
                    $priority = 0;
                    break;
            }
            for ($i = 1; $i <= $number_pages; $i++) {
                $pageId = $pdf->importPage($i, 'MediaBox');
                $pdf->addPage();
                $pdf->useImportedPage($pageId);
            }
            $filename = $this->temp_dir . $state . $this->id_vkr . '.pdf';
            $std = new stdClass();
            $std->file = $filename;
            $std->priority = $priority;
            $this->temp_files[] = $std;

            ob_clean();
            $pdf->Output($filename, 'F');
        }
    }
}