import { defineStore } from 'pinia';
import { useToast } from 'vue-toastification';
import { ajax } from '../utils/ajax';

export const useSurveysStore = defineStore('surveys', {
    state: () => ({
        surveys: [],
        currentSurvey: null,
        loading: false,
    }),

    getters: {
        activeSurveys: (state) => state.surveys.filter(s => s.isActive),
        inactiveSurveys: (state) => state.surveys.filter(s => !s.isActive),
    },

    actions: {
        async fetchSurveys() {
            this.loading = true;
            try {
                const data = await ajax('local_cdo_education_scoring_get_surveys');
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

        async createSurvey(surveyData) {
            this.loading = true;
            const toast = useToast();
            
            try {
                // Очищаем вопросы от лишних полей (id, sortorder), оставляем text, description и type
                const cleanedQuestions = surveyData.questions.map(q => ({
                    text: q.text,
                    description: q.description || '',
                    type: q.type,
                }));
                
                const newSurvey = await ajax('local_cdo_education_scoring_create_survey', {
                    title: surveyData.title,
                    description: surveyData.description || '',
                    durationdays: surveyData.durationDays,
                    questions: cleanedQuestions,
                });
                
                // Убеждаемся, что surveys является массивом
                if (!Array.isArray(this.surveys)) {
                    this.surveys = [];
                }
                this.surveys.push(newSurvey);
                toast.success('Анкета успешно создана');
                return newSurvey;
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при создании анкеты';
                toast.error(errorMessage);
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateSurvey(surveyId, surveyData) {
            this.loading = true;
            const toast = useToast();
            
            try {
                // Убеждаемся, что surveys является массивом
                if (!Array.isArray(this.surveys)) {
                    this.surveys = [];
                }
                
                // Очищаем вопросы от лишних полей (id, sortorder), оставляем text, description и type
                const cleanedQuestions = surveyData.questions.map(q => ({
                    text: q.text,
                    description: q.description || '',
                    type: q.type,
                }));
                
                const updatedSurvey = await ajax('local_cdo_education_scoring_update_survey', {
                    surveyid: surveyId,
                    title: surveyData.title,
                    description: surveyData.description || '',
                    durationdays: surveyData.durationDays,
                    questions: cleanedQuestions,
                });
                
                const index = this.surveys.findIndex(s => s.id === surveyId);
                if (index !== -1) {
                    this.surveys[index] = updatedSurvey;
                } else {
                    // Если анкета не найдена, добавляем её в список
                    this.surveys.push(updatedSurvey);
                }
                toast.success('Анкета успешно обновлена');
                return updatedSurvey;
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при обновлении анкеты';
                toast.error(errorMessage);
                console.error(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async activateSurvey(surveyId) {
            const toast = useToast();
            
            try {
                // Убеждаемся, что surveys является массивом
                if (!Array.isArray(this.surveys)) {
                    this.surveys = [];
                }
                
                const updatedSurvey = await ajax('local_cdo_education_scoring_activate_survey', {
                    surveyid: surveyId,
                    isactive: true,
                });
                
                const index = this.surveys.findIndex(s => s.id === surveyId);
                if (index !== -1) {
                    this.surveys[index] = updatedSurvey;
                } else {
                    // Если анкета не найдена, добавляем её в список
                    this.surveys.push(updatedSurvey);
                }
                toast.success('Анкета активирована');
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при активации анкеты';
                toast.error(errorMessage);
                console.error(error);
            }
        },

        async deactivateSurvey(surveyId) {
            const toast = useToast();
            
            try {
                // Убеждаемся, что surveys является массивом
                if (!Array.isArray(this.surveys)) {
                    this.surveys = [];
                }
                
                const updatedSurvey = await ajax('local_cdo_education_scoring_activate_survey', {
                    surveyid: surveyId,
                    isactive: false,
                });
                
                const index = this.surveys.findIndex(s => s.id === surveyId);
                if (index !== -1) {
                    this.surveys[index] = updatedSurvey;
                } else {
                    // Если анкета не найдена, добавляем её в список
                    this.surveys.push(updatedSurvey);
                }
                toast.success('Анкета деактивирована');
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при деактивации анкеты';
                toast.error(errorMessage);
                console.error(error);
            }
        },

        setCurrentSurvey(survey) {
            this.currentSurvey = survey;
        },
    },
});

