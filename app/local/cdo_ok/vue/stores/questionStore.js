import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import utility from '../utils/utility';
import { useAppStore } from './app';

/**
 * Store для управления вопросами
 */
export const useQuestionStore = defineStore('questions', () => {
    // State
    const questions = ref([]);
    const selectedTab = ref(0);
    const isCreateLoaderOn = ref(false);
    
    const types = ref([
        {
            value: 1,
            text: 'Шкала'
        },
        {
            value: 2,
            text: 'Строка'
        }
    ]);
    
    // Получаем доступ к app store для langStrings, years, reports
    const appStore = useAppStore();
    
    // Computed для доступа к данным из app store
    const langStrings = computed(() => appStore.langStrings);
    const years = computed(() => appStore.adminConfig.years);
    const reports = computed(() => appStore.adminConfig.reports);

    // Getters
    const disciplineQuestions = computed(() => {
        return (group) => {
            return questions.value.filter(question => question.group_tab === group);
        };
    });

    const activeQuestions = computed(() => {
        return questions.value.filter(q => q.visible);
    });

    const archivedQuestions = computed(() => {
        return questions.value.filter(q => !q.visible);
    });

    // Actions
    /**
     * Инициализация состояния
     */
    const initializeState = (data) => {
        if (data.questions) questions.value = data.questions;
    };
    
    /**
     * Загрузка вопросов из API
     */
    const loadQuestions = async () => {
        appStore.setLoading(true);
        try {
            let loadedQuestions = await utility.ajaxMoodleCall('local_cdo_ok_get_questions');
            loadedQuestions.sort((a, b) => {
                if (a.group_tab !== b.group_tab) {
                    return a.group_tab - b.group_tab;
                }
                return a.sort - b.sort;
            });
            questions.value = loadedQuestions;
        } finally {
            appStore.setLoading(false);
        }
    };

    /**
     * Выбор таба
     */
    const selectTab = (newTabIndex) => {
        selectedTab.value = newTabIndex;
    };

    /**
     * Обновление списка вопросов после перетаскивания
     */
    const updateDraggableItems = (data) => {
        const sortedData = [...data].sort((a, b) => a.sort - b.sort);
        questions.value = sortedData;
    };

    /**
     * Обновление одного вопроса
     */
    const updateQuestion = (data) => {
        const index = questions.value.findIndex(q => q.id === data.id);
        if (index !== -1) {
            questions.value[index] = { ...questions.value[index], ...data };
        }
    };

    /**
     * Создание нового вопроса
     */
    const createQuestion = (data) => {
        const exists = questions.value.some(q => q.id === data.id);
        if (!exists) {
            questions.value.push(data);
        }
    };

    /**
     * Удаление вопроса
     */
    const deleteQuestion = (data) => {
        questions.value = questions.value.filter(q => q.id !== data.id);
    };

    /**
     * Обновление порядка сортировки вопросов через API
     */
    const updateQuestionsSortOrder = async (data) => {
        const sortedData = [...data].sort((a, b) => {
            if (a.group_tab !== b.group_tab) {
                return a.group_tab - b.group_tab;
            }
            return a.sort - b.sort;
        });
        
        await utility.ajaxMoodleCall('local_cdo_ok_update_questions', { data: sortedData });
        updateDraggableItems(sortedData);
    };

    /**
     * Обновление вопроса через API
     */
    const updateQuestionAPI = async (data) => {
        await utility.ajaxMoodleCall('local_cdo_ok_update_question', { data: data });
        updateQuestion(data);
    };

    /**
     * Создание вопроса через API
     */
    const createQuestionAPI = async () => {
        isCreateLoaderOn.value = true;
        try {
            const question = await utility.ajaxMoodleCall(
                'local_cdo_ok_create_question',
                {
                    groupTab: selectedTab.value,
                    sort: questions.value.length + 1,
                }
            );
            createQuestion(question);
        } finally {
            isCreateLoaderOn.value = false;
        }
    };

    /**
     * Удаление вопроса через API
     */
    const deleteQuestionAPI = async (item) => {
        const result = await utility.ajaxMoodleCall(
            'local_cdo_ok_delete_question',
            {
                id: item.id
            }
        );
        if (result) {
            deleteQuestion(item);
        }
    };

    return {
        // State
        questions,
        selectedTab,
        isCreateLoaderOn,
        types,
        // Getters
        langStrings,
        years,
        reports,
        disciplineQuestions,
        activeQuestions,
        archivedQuestions,
        // Actions
        initializeState,
        loadQuestions,
        selectTab,
        updateDraggableItems,
        updateQuestion,
        createQuestion,
        deleteQuestion,
        updateQuestionsSortOrder,
        updateQuestionAPI,
        createQuestionAPI,
        deleteQuestionAPI
    };
});

