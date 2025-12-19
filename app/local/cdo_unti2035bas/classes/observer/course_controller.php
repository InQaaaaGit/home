<?php
namespace local_cdo_unti2035bas\observer;


class course_controller {
    public static function all_events(\core\event\base $event): void {
        $courseid = (int)$event->courseid;
        if ($courseid < 2 || !in_array($event->crud, ['c', 'u', 'd'])) {
            return;
        }
        if (
            in_array(
                $event->eventname,
                ['\core\event\course_section_updated', '\tool_recyclebin\event\course_bin_item_created'],
            )
        ) {
            return;
        }
        
        // Проверяем, что курс привязан к потоку (имеет flow_id)
        $depends = new dependencies();
        $streamRepo = $depends->get_stream_repo();
        $streams = $streamRepo->read_by_courseid($courseid);
        if (empty($streams)) {
            debugging("cdo_unti2035bas: Курс {$courseid} не привязан к потоку, пропускаем обработку события", DEBUG_DEVELOPER);
            return;
        }
        
        $usecase = $depends->get_course_update_use_case();
        $usecase->execute($courseid);
    }

    public static function event_section_updated(\core\event\base $event): void {
        $courseid = $event->courseid;
        
        // Проверяем, что курс привязан к потоку (имеет flow_id)
        $depends = new dependencies();
        $streamRepo = $depends->get_stream_repo();
        $streams = $streamRepo->read_by_courseid($courseid);
        if (empty($streams)) {
            debugging("cdo_unti2035bas: Курс {$courseid} не привязан к потоку, пропускаем обработку события section_updated", DEBUG_DEVELOPER);
            return;
        }
        
        $usecase = $depends->get_section_update_use_case();
        $sectionid = $event->objectid;
        $usecase->execute($courseid, $sectionid);
    }
}
