import {defineStore} from 'pinia'
import * as moodleAjax from 'core/ajax';
import notification from "core/notification";

export const useMainStore = defineStore('main', {
    state: () => (
        {
            strings: [],
            citizenships: [],
            educationLevels: [],
            userGroups: [],
            courseSchedule: [],
            documentTypes: [],
            surveyData: {},
            component: 'block_cdo_survey',
        }
    ),
    actions: {
        async loadCitizenshipData() {
            const request = {
                methodname: 'blocks_cdo_survey_get_citizenship',
                args: {},
            };
            this.citizenships = await moodleAjax.call([request])[0];
        },
        async loadEducationLevelData() {
            const request = {
                methodname: 'block_cdo_survey_get_education_levels',
                args: {},
            };
            try {
                this.educationLevels = await moodleAjax.call([request])[0];
            } catch (error) {
                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }
        },
        async loadUserGroupsData() {
            const request = {
                methodname: 'block_cdo_survey_get_user_groups',
                args: {},
            };
            try {
                this.userGroups = await moodleAjax.call([request])[0];
            } catch (error) {
                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }
        },
        async loadDocumentTypes() {
            const request = {
                methodname: 'block_cdo_survey_get_identity_document_types',
                args: {},
            };
            try {
                this.documentTypes = await moodleAjax.call([request])[0];
            } catch (error) {
                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }
        },
        async loadCourseSchedule(courseid) {
            const request = {
                methodname: 'block_cdo_survey_get_course_schedule',
                args: { courseid: courseid },
            };
           /* try {*/
            const courseSchedule = await moodleAjax.call([request])[0];
            const _vm = this;
            courseSchedule.forEach( item => {
                let _item = item.scheduleData[0];
                _vm.courseSchedule.push(
                    {
                        name: _item.dataStartSchedule + ' - ' + _item.dataEndSchedule +
                            ' ' +  _item.nameSchedule,
                        value: _item.scheduleGUID
                    }
                )
            });

           /* } catch (error) {
                console.log(error)
                await notification.alert(
                    'Error',
                    error.message,
                    'Cancel'
                );
            }*/
        },
        async loadUserSurvey() {
            const request = {
                methodname: 'block_cdo_survey_get_survey_data',
                args: {},
            };

            this.surveyData = await moodleAjax.call([request])[0];
        }
    },

})
