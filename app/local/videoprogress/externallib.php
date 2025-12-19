<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_videoprogress;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External video progress API
 */
class external extends \external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function update_progress_parameters() {
        return new \external_function_parameters([
            'videoid' => new \external_value(PARAM_TEXT, 'Video identifier'),
            'cmid' => new \external_value(PARAM_INT, 'Course module ID'),
            'progress' => new \external_value(PARAM_FLOAT, 'Progress percentage (0-100)')
        ]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function update_progress_returns() {
        return new \external_value(PARAM_BOOL, 'Success status');
    }

    /**
     * Update video progress
     *
     * @param string $videoid Video identifier
     * @param int $cmid Course module ID
     * @param float $progress Progress percentage
     * @return bool Success status
     */
    public static function update_progress($videoid, $cmid, $progress) {
        global $USER;

        // Parameter validation
        $params = self::validate_parameters(self::update_progress_parameters(), [
            'videoid' => $videoid,
            'cmid' => $cmid,
            'progress' => $progress
        ]);

        // Context validation
        $context = \context_module::instance($cmid);
        self::validate_context($context);

        // Update progress
        return video_progress::update_progress(
            $USER->id,
            $params['cmid'],
            $params['videoid'],
            $params['progress']
        );
    }

    /**
     * Returns description of method parameters for get_progress
     *
     * @return external_function_parameters
     */
    public static function get_progress_parameters() {
        return new \external_function_parameters([
            'videoid' => new \external_value(PARAM_TEXT, 'Video identifier'),
            'cmid' => new \external_value(PARAM_INT, 'Course module ID')
        ]);
    }

    /**
     * Returns description of method result value for get_progress
     *
     * @return external_description
     */
    public static function get_progress_returns() {
        return new \external_value(PARAM_FLOAT, 'Progress percentage (0-100) or null if not found');
    }

    /**
     * Get video progress
     *
     * @param string $videoid Video identifier
     * @param int $cmid Course module ID
     * @return float|null Progress percentage or null if not found
     */
    public static function get_progress($videoid, $cmid) {
        global $USER;

        // Parameter validation
        $params = self::validate_parameters(self::get_progress_parameters(), [
            'videoid' => $videoid,
            'cmid' => $cmid
        ]);

        // Context validation
        $context = \context_module::instance($cmid);
        self::validate_context($context);

        // Get progress
        return video_progress::get_progress(
            $USER->id,
            $params['cmid'],
            $params['videoid']
        );
    }

    /**
     * Returns description of method parameters for update_segments
     *
     * @return external_function_parameters
     */
    public static function update_segments_parameters() {
        return new \external_function_parameters([
            'videoid' => new \external_value(PARAM_TEXT, 'Video identifier'),
            'cmid' => new \external_value(PARAM_INT, 'Course module ID'),
            'segments' => new \external_multiple_structure(
                new \external_multiple_structure(
                    new \external_value(PARAM_FLOAT, 'Segment time in seconds')
                ),
                'Array of watched segments [[start, end], ...]'
            ),
            'duration' => new \external_value(PARAM_FLOAT, 'Total video duration in seconds')
        ]);
    }

    /**
     * Returns description of method result value for update_segments
     *
     * @return external_description
     */
    public static function update_segments_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success status'),
            'progress' => new \external_value(PARAM_FLOAT, 'Calculated progress percentage')
        ]);
    }

    /**
     * Update video watched segments
     *
     * @param string $videoid Video identifier
     * @param int $cmid Course module ID
     * @param array $segments Array of watched segments
     * @param float $duration Total video duration
     * @return array Result with success status and calculated progress
     */
    public static function update_segments($videoid, $cmid, $segments, $duration) {
        global $USER;

        // Parameter validation
        $params = self::validate_parameters(self::update_segments_parameters(), [
            'videoid' => $videoid,
            'cmid' => $cmid,
            'segments' => $segments,
            'duration' => $duration
        ]);

        // Context validation
        $context = \context_module::instance($cmid);
        self::validate_context($context);

        // Update segments
        $success = video_progress::update_segments(
            $USER->id,
            $params['cmid'],
            $params['videoid'],
            $params['segments'],
            $params['duration']
        );

        // Get updated progress
        $progress = video_progress::get_progress(
            $USER->id,
            $params['cmid'],
            $params['videoid']
        );

        return [
            'success' => $success,
            'progress' => $progress ?: 0.0
        ];
    }
} 