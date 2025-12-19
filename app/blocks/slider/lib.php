<?php

use block_slider\cdo_controller;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

define('TABLE', "block_slider");

/**
 * Функция-заглушка для совместимости с удаленной темой space
 * Возвращает безопасные значения по умолчанию
 */
function theme_space_get_setting($setting) {
    // Безопасные значения по умолчанию для различных настроек
    $defaults = [
        'block1wrapperalign' => 'center',
        'block1herotitlecolor' => '#000000',
        'block1herotitlesize' => '24px',
        'block1herotitleweight' => 'normal',
        'block1count' => '3',
        'block1class' => '',
        'block1sliderinterval' => '7000',
        'showblock1sliderwrapper' => '0',
        'block1fw' => '0'
    ];
    
    // Проверка настроек слайдов (block1slidetitle1, block1slidesubheading1, и т.д.)
    if (preg_match('/^block1slide(title|subheading|caption|css)\d+$/', $setting)) {
        return '';
    }
    
    return isset($defaults[$setting]) ? $defaults[$setting] : '';
}

function get_all_slider($cols = "id, slide_header, slide_text, section, text_company, email, telephone"): array
{
    global $DB;

    $all = $DB->get_records(TABLE, null, null, $cols);

    return $all;
}

function get_all_slider_with_file(): array
{
    global $DB;

    $SQL = "SELECT * 
            FROM mdl_files f 
                INNER JOIN mdl_block_slider bs ON bs.id = f.itemid AND f.component='slider' AND f.filearea='slide'
            WHERE f.component='slider' AND f.filearea='slide'";
    $all = $DB->get_records_sql($SQL, null, null, "bs.id, bs.slide_header, bs.slide_text");

    return $all;
}

function get_slide_by_id($id): stdClass
{
    global $DB;

    $record = $DB->get_record(TABLE, ["id" => $id]);

    return $record;

}

function create_slide($data): int
{
    global $DB;

    return $DB->insert_record(TABLE, $data);
}

function update_slide($data): int
{
    global $DB;

    return $DB->update_record(TABLE, $data);
}


function delete_slide($id): bool
{
    global $DB;
    return $DB->delete_records(TABLE, ["id" => $id]);
}

function convert_slider_data_to_table(): array
{
    $table_data = [];
    $i = 1;
    foreach (get_all_slider() as $slide) {
        $table_row = [];
        $table_row[] = $i++;
        foreach ($slide as $key => $value) {
            if ($key != "id")
                $table_row[] = $value;
        }
        $table_row[] = add_column_crud($slide->id);
        $table_data[] = $table_row;
    }
    return $table_data;
}

function add_column_crud($id): string
{
    return "<a href='?delete=$id'>Удалить</a><br><a href='edit.php?id=$id'>Редактирвать</a>";
}

function block_slider_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{

    require_login($course, true, $cm);

    $itemid = array_shift($args); // The first item in the $args array.

    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/' . implode('/', $args) . '/'; // $args contains elements of the filepath
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'block_slider', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

function get_categories_with_overview($which_category_type, $categoryid = [], $check_depth_more_1 = false)
{
    global $CFG;
    $fs = get_file_storage();
    $listCategories = [];

    if (empty($categoryid)) {
        $list = core_course_category::get_all();
    } else {
        $list = core_course_category::get_many($categoryid);
    }

    foreach ($list as $item) {
        if ($which_category_type == $item->parent || $check_depth_more_1) {
            unset($url);
            $ccc = context_coursecat::instance($item->id);
            $files = $fs->get_area_files($ccc->id, 'block_slider', 'attachment', 0, 'filename', false);
            foreach ($files as $file) {
                $isimage = $file->is_valid_image();
                $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . '/0/' . $file->get_filename(), !$isimage);
            }
            if (isset($url)) {
                $obj = new stdClass();
                $obj->id = $item->id;
                $obj->url = $url;
                $obj->name = $item->name;
                $obj->href = $CFG->wwwroot . "/course/index.php?categoryid=" . $item->id;
                $listCategories[] = $obj;
            }
        }
    }
    return $listCategories;

}

function block_slider_output()
{
    global $PAGE, $CFG, $OUTPUT;

    //Stupid convertion
    $slides = [];
    $fs = get_file_storage();
    foreach (get_all_slider("id, slide_header, slide_text, file_id, section, text_company, email, telephone") as $slide => $value) {
        $file = $fs->get_file_by_id($value->file_id);
        $img = '';
        if ($file) {
            $pic = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
            $img = $pic->out();

        }
        $value->img = $img;
        $slides[] = $value;
    }

    $slide_section_lib = array_values(array_filter($slides, function ($slide) {
        return $slide->section == "0";
    }));
    $slide_section_company = array_values(array_filter($slides, function ($slide) {
        return $slide->section == "1";
    }));
    //$list_courses = (new cdo_controller)->getCourseWithEnrollmentMethod($CFG->needle_open_courses)->getOpenCoursesWithContent()->preparedCourses;
    $list_all_courses = (new cdo_controller)->getIncludedCourses($CFG->needle_all_courses)->getOpenCoursesWithContent()->preparedCourses;
    $priority_top_courses = property_exists($CFG, "ids_top_courses") ? $CFG->ids_top_courses : "";
    $list_top_courses = [];
    if (!empty($priority_top_courses)) {
        $list_top_courses = (new cdo_controller)->getIncludedCourses($priority_top_courses)->getOpenCoursesWithContent()->preparedCourses;
    }

    $list_courses = get_categories_with_overview($CFG->name_category_open_course_id);
    //$list_all_courses = get_categories_with_overview($CFG->all_courses_link);

    $PAGE->requires->js_call_amd('block_slider/main', 'init');
    $params = [
        "first" => '1',
        "section_library" => $slide_section_lib,
        "section_company" => $slide_section_company,
        "showSliderSection" => count($slides) > 0,
        "tech_biblio_description" => $CFG->tech_biblio_description,
        "tech_biblio_head" => $CFG->tech_biblio_head,
        "filials_head" => $CFG->filials_head,
        "filials_description" => $CFG->filials_description,
        "name_category_open_course" => $CFG->name_category_open_course,
        "name_category_open_course_link" => $CFG->name_category_open_course_link,
        "name_category_open_course_id" => $CFG->name_category_open_course_id,
        "description_open_courses" => $CFG->description_open_courses,
        "all_courses_head" => $CFG->all_courses_head,
        "all_courses_description" => $CFG->all_courses_description,
        "all_courses_link_name" => $CFG->all_courses_link_name,
        "all_courses_link" => $CFG->all_courses_link,
        "tech_biblio_id" => $CFG->tech_biblio_id,
        "tech_biblio_link" => $CFG->tech_biblio_link,
        "all_courses" => ($list_all_courses),
        "open_category" => $list_courses,
        "all_courses_show_link" => $CFG->all_courses_link_show,
        "name_top_courses" => $CFG->name_top_courses,
        "list_top_courses" => $list_top_courses
    ];

    $priority_top_courses = property_exists($CFG, "priority_top_courses") ? $CFG->priority_top_courses : 1;
    $priority_open_courses = property_exists($CFG, "priority_open_courses") ? $CFG->priority_open_courses : 2;
    $priority_tech_biblio = property_exists($CFG, "priority_tech_biblio") ? $CFG->priority_tech_biblio : 3;
    $priority_filials = property_exists($CFG, "priority_filials") ? $CFG->priority_filials : 4;
    $priority_all_courses = property_exists($CFG, "priority_all_courses") ? $CFG->priority_all_courses : 5;

    $check_in_array = [
        $priority_top_courses,
        $priority_open_courses,
        $priority_tech_biblio,
        $priority_filials,
        $priority_all_courses
    ];

    // если есть одинаковые позиции или одна из них 0 рисуем дефолтный порядок
    if (count(array_unique($check_in_array)) < count($check_in_array) || in_array(0, $check_in_array)) {
        $priority = [
            $OUTPUT->render_from_template('block_slider/sections/top', $params),
            $OUTPUT->render_from_template('block_slider/sections/all_courses', $params),
            $OUTPUT->render_from_template('block_slider/sections/category_slider', $params),
            $OUTPUT->render_from_template('block_slider/sections/biblio', $params),
            $OUTPUT->render_from_template('block_slider/sections/filials', $params)
        ];
    } else {
        $priority[(int)$priority_top_courses - 1] = $OUTPUT->render_from_template('block_slider/sections/top', $params);
        $priority[(int)$priority_open_courses - 1] = $OUTPUT->render_from_template('block_slider/sections/category_slider', $params);
        $priority[(int)$priority_tech_biblio - 1] = $OUTPUT->render_from_template('block_slider/sections/biblio', $params);
        $priority[(int)$priority_filials - 1] = $OUTPUT->render_from_template('block_slider/sections/filials', $params);
        $priority[(int)$priority_all_courses - 1] = $OUTPUT->render_from_template('block_slider/sections/all_courses', $params);
        ksort($priority);
    }

    return $OUTPUT->render_from_template('block_slider/main',
        ["templates" => $priority]
    );
}

function get_categories_by_parent($which_category_type)
{
    $data = [];
    //получаем все айди дочерних категорий - сразу всего списка не дают.
    $subcategories = core_course_category::get($which_category_type)->get_all_children_ids();
    foreach ($subcategories as $item) {
        $c = core_course_category::get($item);
        $link = new moodle_url(
            '/blocks/slider/cat_add_img.php',
            ["cat_id" => $c->id]
        );
        $link = html_writer::link($link, "Добавить");
        $data[] = [
            $c->id, $c->name, $link
        ];
    }

    return $data;
}

function create_frontpage_block1(): string
{
    global $PAGE;
    $content = "";
    $block1wrapperalign = theme_space_get_setting('block1wrapperalign');
    $block1titlecolor = theme_space_get_setting('block1herotitlecolor');
    $block1herotitlesize = theme_space_get_setting('block1herotitlesize');
    $block1titleweight = theme_space_get_setting('block1herotitleweight');
    $block1count = theme_space_get_setting('block1count');
    $block1class = theme_space_get_setting('block1class');

    if (!empty(theme_space_get_setting('block1sliderinterval'))) {
        $block1sliderinterval = theme_space_get_setting('block1sliderinterval');
    } else {
        $block1sliderinterval = '7000';
    }

// Start Title - Alignment.
    $block1wrapperalignclass = null;
    if ($block1wrapperalign == 0) {
        $block1wrapperalignclass = 'rui-hero-content-left';
    }

    if ($block1wrapperalign == 1) {
        $block1wrapperalignclass = 'rui-hero-content-centered';
    }

    if ($block1wrapperalign == 2) {
        $block1wrapperalignclass = 'rui-hero-content-right';
    }
// End.

// Start Title - Color.
    $block1titlecolorclass = null;
    if ($block1titlecolor == 0) {
        $block1titlecolorclass = ' rui-text--white';
    }

    if ($block1titlecolor == 1) {
        $block1titlecolorclass = ' rui-text--black';
    }

    if ($block1titlecolor == 2) {
        $block1titlecolorclass = ' rui-text--gradient';
    }
// End.

// Start Title - Weight.
    $block1titleweightclass = null;
    if ($block1titleweight == 0) {
        $block1titleweightclass = ' rui-text--weight-normal';
    }

    if ($block1titleweight == 1) {
        $block1titleweightclass = ' rui-text--weight-medium';
    }

    if ($block1titleweight == 2) {
        $block1titleweightclass = ' rui-text--weight-bold';
    }
// End.

// Start Title - Size.
    $block1herotitlesizeclass = null;
    if ($block1herotitlesize == 0) {
        $block1herotitlesizeclass = '';
    }

    if ($block1herotitlesize == 1) {
        $block1herotitlesizeclass = ' rui-hero-title-lg';
    }

    if ($block1herotitlesize == 2) {
        $block1herotitlesizeclass = ' rui-hero-title-xl';
    }
// End.

    if (theme_space_get_setting('showblock1sliderwrapper') == '1') {
        $class = 'rui-hero-content-backdrop rui-hero-content-backdrop--block1';
    } else {
        $class = 'rui-hero-content-box';
    }

    if (theme_space_get_setting('block1fw') == '1') {
        $content .= '<div id="fbblock1" class="wrapper-fw rui-fp-block--1 rui-fp-margin-bottom ' . $block1class . '">';
    } else {
        $content .= '<div id="fbblock1" class="wrapper-xl rui-fp-block--1 rui-fp-margin-bottom mt-3 ' . $block1class . '">';
    }


    $content .= '<div class="swiper swiper-block--1 pb-0">';
    $content .= '<div class="swiper-wrapper">';

    for ($i = 1; $i <= $block1count; $i++) {

        $title = format_text(theme_space_get_setting("block1slidetitle" . $i), FORMAT_HTML, array('noclean' => true));
        $subheading = format_text(theme_space_get_setting("block1slidesubheading" . $i), FORMAT_HTML, array('noclean' => true));
        $caption = format_text(theme_space_get_setting("block1slidecaption" . $i), FORMAT_HTML, array('noclean' => true));
        $css = theme_space_get_setting("block1slidecss" . $i);
        $img = $PAGE->theme->setting_file_url("block1slideimg" . $i, "block1slideimg" . $i);

        if (!empty($css)) {
            $content .= '<div class="rui-hero-bg swiper-slide">';
        } else {
            $content .= '<div class="rui-hero-bg swiper-slide" style="' . $css . '">';
        }

        if (!empty($caption) || !empty($title)) {
            $content .= '<div class="rui-hero-content rui-hero--slide ' .
                $class .
                ' rui-hero-content-position ' .
                $block1wrapperalignclass .
                '">';
        }

        if (!empty($subheading)) {
            $content .= '<h4 class="rui-hero-subheading' .
                $block1titlecolorclass .
                $block1titleweightclass .
                '">' . $subheading . '</h4>';
        }

        if (!empty($title)) {
            $content .= '<h3 class="rui-hero-title' .
                $block1titlecolorclass .
                $block1titleweightclass .
                $block1herotitlesizeclass .
                '">' . $title . '</h3>';
        }

        if (!empty($caption)) {
            $content .= '<div class="rui-hero-desc ' . $block1titlecolorclass . '">' . $caption . '</div>';
        }

        if (!empty($caption) || !empty($title)) {
            $content .= '</div>';
        }

        $content .= '<img class="d-flex img-fluid w-100" src="' . $img . '" alt="' . $title . '" />';
        $content .= '</div>';
    }

    $content .= '</div>';
    $content .= '<div class="d-none d-md-flex swiper-button-next"></div>';
    $content .= '<div class="d-none d-md-flex swiper-button-prev"></div>';
    $content .= '<div class="swiper-pagination"></div>';
    $content .= '</div>';
    $content .= '</div>';

    $content .= '<script>function reportWindowSize(){for(var e=document.getElementsByClassName("rui-hero--slide"),
o=0,t=0|e.length;o<t;o=o+1|0){var n=e[o].offsetHeight;e[o].style.top="calc(50% - "+n/2+"px)"}}
window.addEventListener("resize",reportWindowSize),
window.onload=reportWindowSize();</script>';

    $content .= '<script>var swiper=new Swiper(".swiper-block--1",{slidesPerView:1,
pagination:{el:".swiper-pagination",type:"progressbar"},
navigation:{nextEl:".swiper-button-next",prevEl:".swiper-button-prev"},
autoplay: {delay: ' . $block1sliderinterval . ',},
keyboard:{enabled:!0},mousewheel:{releaseOnEdges:!0},effect:"creative",
autoHeight:!0,creativeEffect:{prev:{shadow:!0,translate:["-20%",0,-1]},
next:{translate:["100%",0,0]}},breakpoints:{}});</script>';
    return $content;
}
