import { defineStore } from 'pinia';
import { ref } from 'vue';
import utility from '../utils/utility';

/**
 * Store для управления активными опросами по группам
 */
export const useActiveSurveyStore = defineStore('activeSurvey', () => {
    // State
    const activeGroups = ref({});

    // Actions
    /**
     * Получение статуса активности группы
     */
    const getActiveSurvey = async (groupTab) => {
        const isActive = await utility.ajaxMoodleCall(
            'local_cdo_ok_active_groups_get_active_group',
            {
                params: {
                    group_tab: groupTab
                }
            }
        );
        
        activeGroups.value[groupTab] = isActive;
        return isActive;
    };

    /**
     * Переключение активности опроса для группы
     */
    const toggleActiveSurvey = async (groupTab) => {
        const currentStatus = activeGroups.value[groupTab] || false;
        
        const newStatus = await utility.ajaxMoodleCall(
            'local_cdo_ok_active_groups_create_update',
            {
                data: {
                    group_tab: groupTab,
                    active: Number(!currentStatus)
                }
            }
        );
        
        activeGroups.value[groupTab] = newStatus;
        return newStatus;
    };

    /**
     * Проверка, активен ли опрос для группы
     */
    const isGroupActive = (groupTab) => {
        return activeGroups.value[groupTab] || false;
    };

    return {
        // State
        activeGroups,
        // Actions
        getActiveSurvey,
        toggleActiveSurvey,
        isGroupActive
    };
});









