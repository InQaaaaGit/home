import { defineStore } from 'pinia';
import { useToast } from 'vue-toastification';
import { ajax } from '../utils/ajax';
import { useAppStore } from './app';

export const useStudentSurveysStore = defineStore('studentSurveys', {
    state: () => ({
        surveys: [],
        currentSurvey: null,
        loading: false,
    }),

    getters: {
        availableSurveys: (state) => state.surveys.filter(s => !s.isCompleted),
        completedSurveys: (state) => state.surveys.filter(s => s.isCompleted),
    },

    actions: {
        async fetchSurveys() {
            this.loading = true;
            try {
                const data = await ajax('local_cdo_education_scoring_get_active_surveys');
                // Убеждаемся, что это массив
                this.surveys = Array.isArray(data) ? data : [];
            } catch (error) {
                const toast = useToast();
                toast.error('Ошибка при загрузке анкет');
                console.error(error);
                this.surveys = [];
            } finally {
                this.loading = false;
            }
        },

        setCurrentSurvey(survey) {
            this.currentSurvey = survey;
        },

        async submitSurveyResponse(surveyId, answers, teacherId = null) {
            this.loading = true;
            const toast = useToast();
            
            // Получаем discipline_id и discipline_name из appStore
            const appStore = useAppStore();
            const disciplineId = appStore.disciplineId;
            const disciplineName = appStore.disciplineName;
            
            try {
                const requestData = {
                    surveyid: surveyId,
                    answers: answers,
                };
                
                // Добавляем discipline_id, если он есть
                if (disciplineId) {
                    requestData.discipline_id = disciplineId;
                }
                
                // Добавляем discipline_name, если он есть
                if (disciplineName) {
                    requestData.discipline_name = disciplineName;
                }
                
                // Добавляем teacher_id, если он есть
                if (teacherId) {
                    requestData.teacher_id = teacherId;
                }
                
                const result = await ajax('local_cdo_education_scoring_submit_survey_response', requestData);
                
                // Обновляем статус анкеты в списке
                const index = this.surveys.findIndex(s => s.id === surveyId);
                if (index !== -1) {
                    // Если teacher_id передан, добавляем его в список заполненных
                    if (teacherId) {
                        if (!this.surveys[index].completedTeacherIds) {
                            this.surveys[index].completedTeacherIds = [];
                        }
                        if (!this.surveys[index].completedTeacherIds.includes(teacherId)) {
                            this.surveys[index].completedTeacherIds.push(teacherId);
                        }
                    } else {
                        // Если teacher_id не передан, анкета считается полностью заполненной
                        this.surveys[index].isCompleted = true;
                    }
                }
                
                toast.success(result.message || 'Анкета успешно отправлена');
                return result;
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при отправке ответов';
                toast.error(errorMessage);
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});

