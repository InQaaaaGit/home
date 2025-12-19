M.mod_webinarru = M.mod_webinarru || {};

M.mod_webinarru.get_busy_time_information = {
    init: function (params) {
        var date_picker = Y.one('#fitem_id_webinar_date');
        var picker_duration = Y.one('#id_webinar_duration');

        var date_picker_day = Y.one('#id_webinar_date_day');
        var date_picker_month = Y.one('#id_webinar_date_month');
        var date_picker_year = Y.one('#id_webinar_date_year');
        var date_picker_hour = Y.one('#id_webinar_date_hour');
        var date_picker_minute = Y.one('#id_webinar_date_minute');

        var checkbox_show_selected_date = Y.one('#id_show_selected_date');

        function change_busy_time_information() {
            Y.io(M.cfg.wwwroot + '/mod/webinarru/ajax.php', {
                method: 'GET',
                data: {
                    'action': 'get_busy_time_information',
                    'webinar_date[day]': date_picker_day.get('value'),
                    'webinar_date[month]': date_picker_month.get('value'),
                    'webinar_date[year]': date_picker_year.get('value'),
                    'webinar_date[hour]': date_picker_hour.get('value'),
                    'webinar_date[minute]': date_picker_minute.get('value'),
                    'webinar_duration': picker_duration.get('value'),
                    'update': params['update']
                },
                on: {
                    success: function (id, response) {
                        response = JSON.parse(response.responseText);

                        var busy_time_information = Y.one('.busy_time_information');
                        busy_time_information.setHTML(response.timeline);

                        var busy_time_information_desc = Y.one('#id_busy_time_information_desc');
                        busy_time_information_desc.setHTML(response.desc.data);

                        toggle_submit_buttons(!response.desc.status);

                        toggle_show_selected_date();
                    },
                    failure: function (id, response) {
                        var container = Y.one('.busy_time_information');
                        container.setHTML(response.responseText);
                    }
                }
            });
        }

        function toggle_submit_buttons(status) {
            var submitbutton1 = Y.one('#id_submitbutton'); submitbutton1.set('disabled', status);
            var submitbutton2 = Y.one('#id_submitbutton2'); submitbutton2.set('disabled', status);
        }

        // function enable_submit_buttons() {
        //     var submitbutton1 = Y.one('#id_submitbutton'); submitbutton1.set('disabled', false);
        //     var submitbutton2 = Y.one('#id_submitbutton2'); submitbutton2.set('disabled', false);
        // }
        //
        // function disable_submit_buttons() {
        //     var submitbutton1 = Y.one('#id_submitbutton'); submitbutton1.set('disabled', true);
        //     var submitbutton2 = Y.one('#id_submitbutton2'); submitbutton2.set('disabled', true);
        // }

        function toggle_show_selected_date() {
            var webinar_selected_range = Y.one('.webinar_selected_range');
            var legend_selected = Y.one('.legend_selected');
            var legend_selected_desc = Y.one('.legend_selected_desc');

            if (!checkbox_show_selected_date.get('checked')) {
                webinar_selected_range.hide();
                legend_selected.hide();
                legend_selected_desc.hide();
            }
            else {
                webinar_selected_range.show();
                legend_selected.show();
                legend_selected_desc.show();
            }
        }

        Y.on('domready', change_busy_time_information);

        date_picker.on('change', change_busy_time_information);
        picker_duration.on('change', change_busy_time_information);

        if (checkbox_show_selected_date) { checkbox_show_selected_date.on('change', toggle_show_selected_date); }
    }
}
