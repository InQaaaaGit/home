import {defineStore} from 'pinia'
import * as moodleAjax from 'core/ajax';

import {useToast} from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import notification from "core/notification";
export const useMainStore = defineStore('main', {
    state: () => (
        {
            users: [],
            userCourses: [],
            log: ''
        }
    ),
    actions: {
        async loadUsers(criteria) {
            const request = {
                methodname: 'local_cdo_ag_tools_get_users',
                args: {
                    'criteria': [{
                        'key': 'lastname',
                        'value': criteria
                    }]
                },
            };
            this.users = await moodleAjax.call([request])[0];
        },
        async loadUserCourses(userid) {
            const request = {
                methodname: 'cdo_enrol_get_users_courses',
                args: {
                    'userid': userid
                },
            };
            this.userCourses = await moodleAjax.call([request])[0];
        },
        async setAvailability(user, quarter_start='', quarter_end ='', grant_access = true, unset=false) {
            const $toast = useToast();
            const request = {
                methodname: 'local_cdo_ag_tools_set_availability',
                args: {
                    'user_id': user,
                    'quarter_start': quarter_start,
                    'quarter_end': quarter_end,
                    'grant_access': grant_access,

                },
            };
            const result = await moodleAjax.call([request])[0];
            this.log = result.message;
            $toast.success(result.message);
            /*await notification.addNotification(
                [{
                    type: 'warning',
                    message: result
                }]
            );*/
        }
    },
    getters: {}
})
