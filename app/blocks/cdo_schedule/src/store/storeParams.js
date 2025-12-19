import {defineStore} from 'pinia';
import utility from '../utility';
import {scheduler} from "dhtmlx-scheduler";

export const useParamsStore = defineStore('params', {
    state: () => ({
        events: [],
        teachers: [],
        selectedTeacherName: null,
    }),
    getters: {
        getTeachers: (state) => state.teachers,
        getFilteredEvents: (state) => {
            if (!state.selectedTeacherName) {
                return state.events;
            }
            return state.events.filter(event => event.teacher === state.selectedTeacherName);
        },
    },
    actions: {
        initializeParams() {
            // Параметры больше не используются
        },
        async getScheduleData(type = '') {
            try {
                const ev = await utility.ajaxMoodleCall('block_cdo_schedule_get_schedule_data', { type });
                this.events = ev || [];
                
                const teachersSet = new Set();
                this.events.forEach(event => {
                    if (event.teacher) {
                        teachersSet.add(event.teacher);
                    }
                });
                this.teachers = Array.from(teachersSet).map(name => ({ id: name, name: name }));
                this.selectedTeacherName = null; 

                scheduler.clearAll();
                scheduler.parse(this.getFilteredEvents);
                return this.events;
            } catch (error) {
                console.error('Ошибка при загрузке данных расписания:', error);
                this.events = [];
                this.teachers = [];
                return [];
            }
        },
        selectTeacher(teacherName) {
            this.selectedTeacherName = teacherName;
            scheduler.clearAll();
            scheduler.parse(this.getFilteredEvents);
        }
    },
});
