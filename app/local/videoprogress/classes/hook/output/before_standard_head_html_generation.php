<?php
namespace local_videoprogress\hook\output;

class before_standard_head_html_generation {
    public static function callback(\core\hook\output\before_standard_head_html_generation $event): void {
        global $PAGE;
        
        // Проверяем, находимся ли мы на странице модуля курса
        if ($PAGE->context->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id(null, $PAGE->context->instanceid, 0, false, MUST_EXIST);
            
            // Проверяем, есть ли у пользователя права на отслеживание
            if (has_capability('local/videoprogress:track', $PAGE->context)) {
                $tracker = new \local_videoprogress\output\video_tracker($cm->id);
                echo $PAGE->get_renderer('local_videoprogress')->render($tracker);
            }
        }
    }
} 