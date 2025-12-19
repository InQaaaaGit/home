import {defineStore} from 'pinia';
import * as moodleAjax from "core/ajax";

export const useParamsStore = defineStore('params', {
    state: () => ({
        params: null,
        events: [],
        isInitialized: false,
        currentFilters: {
            courseId: '',
            groupId: null,
            startDate: '',
            endDate: ''
        },
        courses: [],
        groups: []
    }),

    getters: {
        getParams: (state) => state.params,
        isParamsInitialized: (state) => state.isInitialized,
        getEvents: (state) => state.events,
        getCurrentFilters: (state) => state.currentFilters,
        getCourses: (state) => state.courses,
        getGroups: (state) => state.groups
    },

    actions: {
        async getScheduleData(type = '') {
            try {
                const request = {
                    methodname: 'block_cdo_schedule_get_schedule_data',
                    args: {
                        'type': type,
                    },
                };
                
                const ev = await moodleAjax.call([request])[0];
                this.events = ev || [];
            } catch (error) {
                console.error('Ошибка при получении данных расписания:', error);
                this.events = [];
            }
        },

        async getScheduleDataWithFilters(filters) {
            try {
                this.currentFilters = filters;
                
                const request = {
                    methodname: 'block_cdo_schedule_get_full_schedule',
                    args: {
                        'type': filters.role || '',
                        'course_id': filters.courseId || '',
                        'group_id': filters.groupId || '',
                        'start_date': filters.startDate || '',
                        'end_date': filters.endDate || ''
                    },
                };
                
                const ev = await moodleAjax.call([request])[0];
                console.log('getScheduleDataWithFilters: получены данные с сервера:', ev);
                
                this.events = ev || [];
                console.log('getScheduleDataWithFilters: события сохранены в store:', this.events);
            } catch (error) {
                console.error('Ошибка при получении данных расписания с фильтрами:', error);
                this.events = [];
            }
        },

        async loadCourses() {
            try {
                const request = {
                    methodname: 'block_cdo_schedule_get_courses',
                    args: {},
                };
                
                const courses = await moodleAjax.call([request])[0];
                this.courses = courses || [];
            } catch (error) {
                console.error('Ошибка при загрузке курсов:', error);
                this.courses = [];
            }
        },

        async loadGroups(courseId = '') {
            try {
                const request = {
                    methodname: 'block_cdo_schedule_get_groups',
                    args: {
                        'courseId': courseId
                    },
                };
                
                const groups = await moodleAjax.call([request])[0];
                this.groups = groups || [];
            } catch (error) {
                console.error('Ошибка при загрузке групп:', error);
                this.groups = [];
            }
        },
        
        initializeParams() {
            this.params = {};
            this.isInitialized = true;
        },

        getParam(key) {
            return this.params?.[key];
        },

        updateParam(key, value) {
            if (this.params) {
                this.params[key] = value;
            }
        },

        resetParams() {
            this.params = null;
            this.isInitialized = false;
            this.events = [];
            this.currentFilters = {
                courseId: '',
                groupId: null,
                startDate: '',
                endDate: ''
            };
        },

        // Методы для работы с событиями
        addEvent(event) {
            this.events.push(event);
        },

        removeEvent(eventId) {
            this.events = this.events.filter(event => event.id !== eventId);
        },

        updateEvent(eventId, updatedEvent) {
            this.events = this.events.map(event =>
                event.id === eventId ? {...event, ...updatedEvent} : event
            );
        },

        clearEvents() {
            this.events = [];
        },

        // Методы для работы с фильтрами
        updateFilters(filters) {
            this.currentFilters = { ...this.currentFilters, ...filters };
        },

        clearFilters() {
            this.currentFilters = {
                courseId: '',
                groupId: null,
                startDate: '',
                endDate: ''
            };
        }
    },
});
