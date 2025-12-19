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

/**
 * Settings for local_cdo_visuallyimpaired plugin.
 *
 * @package   local_cdo_visuallyimpaired
 * @copyright 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_cdo_visuallyimpaired', 
        get_string('pluginname', 'local_cdo_visuallyimpaired'));
    
    if ($ADMIN->fulltree) {
        // Настройка включения/выключения плагина
        $settings->add(new admin_setting_configcheckbox('local_cdo_visuallyimpaired/enabled',
            get_string('enabled', 'local_cdo_visuallyimpaired'),
            get_string('enabled_desc', 'local_cdo_visuallyimpaired'), 1));
        
        // Настройка размера шрифта по умолчанию
        $settings->add(new admin_setting_configselect('local_cdo_visuallyimpaired/fontsize',
            get_string('fontsize', 'local_cdo_visuallyimpaired'),
            get_string('fontsize_desc', 'local_cdo_visuallyimpaired'), '16',
            array(
                '12' => '12px',
                '14' => '14px',
                '16' => '16px',
                '18' => '18px',
                '20' => '20px',
                '22' => '22px',
                '24' => '24px'
            )));
        
        // Настройка цветовой схемы по умолчанию
        $settings->add(new admin_setting_configselect('local_cdo_visuallyimpaired/theme',
            get_string('colorscheme', 'local_cdo_visuallyimpaired'),
            get_string('colorscheme_desc', 'local_cdo_visuallyimpaired'), 'white',
            array(
                'white' => get_string('blackonwhite', 'local_cdo_visuallyimpaired'),
                'black' => get_string('whiteonblack', 'local_cdo_visuallyimpaired'),
                'blue' => get_string('blueonlightblue', 'local_cdo_visuallyimpaired'),
                'brown' => get_string('brownonbeige', 'local_cdo_visuallyimpaired'),
                'green' => get_string('greenondarkbrown', 'local_cdo_visuallyimpaired')
            )));
        
        // Настройка синтеза речи
        $settings->add(new admin_setting_configcheckbox('local_cdo_visuallyimpaired/speech',
            get_string('audio', 'local_cdo_visuallyimpaired'),
            get_string('speech_desc', 'local_cdo_visuallyimpaired'), 1));
        
        // Настройка отображения изображений
        $settings->add(new admin_setting_configselect('local_cdo_visuallyimpaired/images',
            get_string('images', 'local_cdo_visuallyimpaired'),
            get_string('images_desc', 'local_cdo_visuallyimpaired'), 'grayscale',
            array(
                'true' => get_string('showimages', 'local_cdo_visuallyimpaired'),
                'false' => get_string('hideimages', 'local_cdo_visuallyimpaired'),
                'grayscale' => 'Оттенки серого'
            )));
    }
    
    $ADMIN->add('localplugins', $settings);
}
