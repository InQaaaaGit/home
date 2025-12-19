M.mod_webinarru = M.mod_webinarru || {};

M.mod_webinarru.check_settings = {
    init: function (params) {

        var calendar = Y.one('#id_webinar_date_calendar');
        var submitbutton1 = Y.one('#id_submitbutton');
        var submitbutton2 = Y.one('#id_submitbutton2');
        var tags_fieldset = Y.one('#id_tagshdr');

        function change_submit_buttons() {
            submitbutton1.set('value', params['submitbutton1']);
            submitbutton2.set('value', params['submitbutton2']);
        }

        function check_calendar_settings() {
            if (!params['show_calendar']) { hide_calendar(); }
        }

        function check_tags_settings() {
            if (params['disable_tags']) { hide_tags(); }
        }

        function check_accounts_exist() {
            if (!params['accounts_exist']) { disable_submit_buttons(); }
        }

        function hide_calendar() {
            calendar.hide();
        }

        function hide_tags() {
            tags_fieldset.hide();
        }

        function disable_submit_buttons() {
            submitbutton1.set('disabled', true);
            submitbutton2.set('disabled', true);
        }

        Y.on('domready', change_submit_buttons);
        Y.on('domready', check_calendar_settings);
        Y.on('domready', check_tags_settings);
        Y.on('domready', check_accounts_exist);
    }
}
