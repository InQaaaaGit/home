import {defineStore} from 'pinia';
import * as moodleAjax from 'core/ajax';
import notification from "core/notification";
import {useToast} from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import {useLangStore} from "./storeLang";

export const useMainStore = defineStore('main', {
    state: () => ({
        date1c: null, discipline: null, edu_plan: null, lesson_type: null, period_of_study: null,
        attendances: [], training_course: null, time_start: null, time_end: null,
        loading: true, // Add loading state
    }),
    actions: {
        setParams(params) {
            this.date1c = params.date1c;
            this.discipline = params.discipline;
            this.edu_plan = params.edu_plan;
            this.lesson_type = params.lesson_type;
            this.period_of_study = params.period_of_study;
            this.group = params.group;
            this.training_course = params.training_course;
            this.employee = params.employee;
            this.time_start = params.time_start;
            this.time_end = params.time_end;
        },
        async getSetAttendance() {
            this.loading = true; // Set loading to true before the call
            try {
                const responses = await moodleAjax.call([{
                    methodname: 'block_cdo_schedule_get_set_attendance', args: {
                        date1c: this.date1c,
                        discipline: this.discipline,
                        edu_plan: this.edu_plan,
                        lesson_type: this.lesson_type,
                        period_of_study: this.period_of_study,
                        group: this.group,
                        training_course: this.training_course,
                        employee: this.employee,
                        time_start: this.time_start,
                        time_end: this.time_end,
                    },
                }]);
                this.attendances = await responses[0];
                // Initialize loading state for each attendance item
                if (this.attendances && this.attendances.attendance) {
                    this.attendances.attendance.forEach(item => {
                        item.loading = false;
                    });
                }
                this.loading = false; // Set loading to false after success
            } catch (error) {
                this.loading = false; // Set loading to false on error
                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }
        },
        async setGrade(index, attendanceObj) {
            const $toast = useToast();
            const stringsStore = useLangStore();
            const strings = stringsStore.getStrings;
            if (this.attendances && this.attendances.attendance && this.attendances.attendance[index]) {
                this.attendances.attendance[index].loading = true;
            }
            try {
                const responses = await moodleAjax.call([{
                    methodname: 'block_cdo_schedule_set_attendance',
                    args: attendanceObj
                }]);
                const result = await responses[0]; // Don't update the whole attendances array
                if (this.attendances && this.attendances.attendance && this.attendances.attendance[index]) {
                    this.attendances.attendance[index].loading = false;
                    $toast.success(strings.success);
                }
            } catch (error) {
                if (this.attendances && this.attendances.attendance && this.attendances.attendance[index]) {
                    this.attendances.attendance[index].loading = false;
                }

                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }
        },
        toggleAttendance(index, guid_student) {

            if (this.attendances.attendance[index].attendance_status === 'Посетил') {
                this.attendances.attendance[index].attendance_status = 'Отсутствовал';
            } else {
                this.attendances.attendance[index].attendance_status = 'Посетил';
            }

            const attendanceObj = {
                GUIDSheet: this.attendances.guid_attendance,
                GUIDStudent: guid_student,
                GUIDGrade: this.attendances.attendance[index].attendance_status
            }
            this.setGrade(index, attendanceObj);

        }
    },
});
