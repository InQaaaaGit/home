import { defineStore } from 'pinia';
import { ref } from 'vue';

/**
 * Главный store приложения
 * Управляет общим состоянием и настройками
 */
export const useAppStore = defineStore('app', () => {
    // Общее состояние
    const appType = ref(''); // 'admin' или 'survey'
    const langStrings = ref({});
    const isLoading = ref(false);
    
    // Состояние для админки
    const adminConfig = ref({
        years: [],
        reports: []
    });
    
    // Состояние для опроса
    const surveyConfig = ref({
        discipline: '',
        disciplineCode: '',
        groupTab: 0
    });

    /**
     * Установка типа приложения
     */
    const setAppType = (type) => {
        appType.value = type;
    };

    /**
     * Инициализация приложения админки
     */
    const initializeAdmin = (config) => {
        appType.value = 'admin';
        langStrings.value = config.langStrings || {};
        adminConfig.value = {
            years: config.years || [],
            reports: config.reports || []
        };
    };

    /**
     * Инициализация приложения опроса
     */
    const initializeSurvey = (config) => {
        appType.value = 'survey';
        langStrings.value = config.langStrings || {};
        surveyConfig.value = {
            discipline: config.discipline || '',
            disciplineCode: config.disciplineCode || '',
            groupTab: config.groupTab || 0
        };
    };

    /**
     * Получение строки локализации по ключу
     */
    const getString = (key, defaultValue = '') => {
        return langStrings.value[key] || defaultValue;
    };

    /**
     * Установка состояния загрузки
     */
    const setLoading = (loading) => {
        isLoading.value = loading;
    };

    return {
        // State
        appType,
        langStrings,
        isLoading,
        adminConfig,
        surveyConfig,
        // Actions
        setAppType,
        initializeAdmin,
        initializeSurvey,
        getString,
        setLoading
    };
});









