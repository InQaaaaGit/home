import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import utility from '../utils/utility';
import { useAppStore } from './app';

/**
 * Store для управления опросами
 */
export const useSurveyStore = defineStore('survey', () => {
    // State
    const questions = ref([]);
    const confirmed = ref(false);
    
    // Получаем доступ к app store
    const appStore = useAppStore();
    
    // Computed для доступа к данным из app store
    const langStrings = computed(() => appStore.langStrings);
    const discipline = computed(() => appStore.surveyConfig.discipline);
    const disciplineCode = computed(() => appStore.surveyConfig.disciplineCode);
    const groupTab = computed(() => appStore.surveyConfig.groupTab);

    // Getters
    const answeredAll = computed(() => {
        return questions.value.every(item => {
            if (item.answer == null) {
                return false;
            }
            return !!item.answer.length || item.answer.toString().length > 0;
        });
    });

    // Actions
    /**
     * Загрузка вопросов с ответами
     */
    const loadQuestions = async () => {
        appStore.setLoading(true);
        try {
            await checkConfirmStatus();
            
            if (!confirmed.value) {
                const loadedQuestions = await utility.ajaxMoodleCall(
                    'local_cdo_ok_get_question_with_answers',
                    {
                        params: {
                            group_tab: groupTab.value,
                            visible: true,
                            integration: disciplineCode.value
                        }
                    }
                );
                questions.value = loadedQuestions || [];
            }
        } finally {
            appStore.setLoading(false);
        }
    };

    /**
     * Проверка статуса подтверждения ответов
     */
    const checkConfirmStatus = async () => {
        const result = await utility.ajaxMoodleCall(
            'local_cdo_ok_confirm_answers_get_confirm_answer',
            {
                params: {
                    integration: disciplineCode.value
                }
            }
        );
        
        let status = false;
        if (result && result.length) {
            status = result[0].status;
        }
        confirmed.value = status;
    };

    /**
     * Отправка ответа на вопрос
     */
    const sendAnswer = async (questionId, answer) => {
        await utility.ajaxMoodleCall('local_cdo_ok_create_answer', {
            data: {
                answer: answer,
                question_id: questionId,
                integration: disciplineCode.value,
                discipline: discipline.value
            }
        });
    };

    /**
     * Подтверждение всех ответов
     */
    const confirmAnswers = async () => {
        await utility.ajaxMoodleCall(
            'local_cdo_ok_confirm_answers_create_update',
            {
                data: {
                    status: 1,
                    integration: disciplineCode.value
                }
            }
        );
        confirmed.value = true;
        
        // Редирект после успешного подтверждения
        window.location.href = "/local/cdo_academic_progress";
    };

    /**
     * Обновление ответа на вопрос в локальном состоянии
     */
    const updateAnswer = (questionId, answer) => {
        const question = questions.value.find(q => q.id === questionId);
        if (question) {
            question.answer = answer;
        }
    };

    return {
        // State
        questions,
        confirmed,
        // Getters
        langStrings,
        discipline,
        disciplineCode,
        groupTab,
        answeredAll,
        // Actions
        loadQuestions,
        checkConfirmStatus,
        sendAnswer,
        confirmAnswers,
        updateAnswer
    };
});

