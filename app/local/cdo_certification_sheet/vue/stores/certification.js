import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';

// AJAX функция для взаимодействия с Moodle
const ajax = (method, data = {}) => {
    return new Promise((resolve, reject) => {
        require(['core/ajax'], (Ajax) => {
            Ajax.call([{
                methodname: method, // Используем полное название без добавления префикса
                args: data
            }])[0].then(resolve).catch(reject);
        });
    });
};

// Функция для загрузки строк компонента через стандартный Moodle API
const loadComponentStrings = () => {
    return new Promise((resolve, reject) => {
        require(['core/str'], (Str) => {
            const stringKeys = [
                'table_sheet_user_full_name',
                'table_sheet_grade_book',
                'table_sheet_grade',
                'table_sheet_teacher_grade',
                'commission_sheet_agreed',
                'close_sheet_close_button',
                'sheet_download',
                'toast_success',
                'sheet_date',
                'sheet_name_plan',
                'sheet_group',
                'sheet_profile',
                'sheet_semester',
                'sheet_division',
                'sheet_form_education',
                'sheet_level_education',
                'sheet_specialty',
                'sheet_course',
                'sheet_discipline',
                'sheet_type_control',
                'sheet_name_sheet',
                'sheet_theme',
                'sheet_theme_placeholder',
                'sheet_points_semester',
                'sheet_control_event',
                'point_semester',
                'point_control_event',
                'absence',
                'commission_sheet_user_full_name',
                'commission_sheet_activity',
                'commission_sheet_chairman',
                'commission_sheet_agreed_message_yes',
                'commission_sheet_agreed_message_no',
                'list_sheet_not_found_open_sheet',
                'guid_absence_not_set',
                'sheet_guid_not_found',
                'grades_confirm_change_close_title',
                'grades_confirm_change_close_message',
                'grades_confirm_change_close_yes',
                'grades_confirm_change_close_no',
                'grade_unsatisfactory',
                'grade_satisfactory',
                'grade_good',
                'grade_excellent',
                'average_discipline_rating',
                'rating_intermediate_certification_discipline',
                'final_rating_discipline',
                'ysc_competence_level',
                'commission_sheet_title',
                'current_grade',
                'absence_grade',
                'sheet_tab_name',
                'loading'
            ];

            Str.get_strings(stringKeys.map(key => ({
                key: key,
                component: 'local_cdo_certification_sheet'
            }))).then(results => {
                const strings = {};
                stringKeys.forEach((key, index) => {
                    strings[key] = results[index];
                });
                resolve({ strings });
            }).catch(reject);
        });
    });
};

export const useCertificationStore = defineStore('certification', () => {
    // State
    const strings = ref({});
    const sheets = ref([]);
    const userID = ref(null);
    const isAppLoading = ref(true);
    const showBRS = ref([]);
    const absenceGUID = ref('');
    const divisionForBRS = ref([]);
    const typeControlCredit = ref(null);
    const typeControlExamine = ref(null);
    const typeControlDiffCredit = ref(null);
    const showDownloadButton = ref(true);

    // Toast instance
    const toast = useToast();

    // Getters
    const haveSheets = computed(() => sheets.value.length > 0);
    
    const isSheetReachDateStart = computed(() => (sheetGuid) => {
        return true;
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        return sheet ? sheet.reach_date_start : false;
    });
    
    const isAllGradeSet = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        if (!sheet || !sheet.students) return false;
        const emptyGUID = "00000000-0000-0000-0000-000000000000";
        return sheet.students.every(student => {
            const grade = student.grade;
            return grade !== null && 
                   grade !== undefined && 
                   grade !== '' && 
                   grade !== emptyGUID;
        });
    });
    
    const isThemedSheet = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        return sheet ? sheet.is_themed : false;
    });
    
    const isCurrentUserSetAgreed = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        if (!sheet || !sheet.teachers) return false;
        return sheet.teachers.some(teacher => 
            parseInt(teacher.user_id) === parseInt(userID.value) && teacher.agreed
        );
    });
    
    const isAllAgreedSet = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        if (!sheet || !sheet.teachers) return false;
        return sheet.teachers.every(teacher => teacher.agreed);
    });
    
    const isAgreedAllowInSheet = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        return sheet ? sheet.agreed_allow : false;
    });
    
    const isChairmanInSheet = computed(() => (sheetGuid) => {
        const sheet = sheets.value.find(s => s.guid === sheetGuid);
        if (!sheet || !sheet.teachers) return false;
        return sheet.teachers.some(teacher => 
            parseInt(teacher.user_id) === parseInt(userID.value) && teacher.chairman
        );
    });

    // Actions
    const loadComponentStringsAction = async () => {
        try {
            const result = await loadComponentStrings();
            if (result.strings) {
                strings.value = result.strings;
            }
        } catch (error) {
            console.error('Ошибка загрузки строк компонента:', error);
        }
    };

    const loadCurrentUserID = (userId) => {
        userID.value = userId;
    };

    const loadSetShowBRS = (showBrsArray) => {
        showBRS.value = Array.isArray(showBrsArray) ? showBrsArray : [];
    };

    const loadDivisionForBRS = (divisionArray) => {
        divisionForBRS.value = Array.isArray(divisionArray) ? divisionArray : [];
    };

    const loadAbsenceGuid = (guid) => {
        absenceGUID.value = guid || '';
    };

    const loadShowDownloadButton = (showDownload) => {
        showDownloadButton.value = showDownload !== false;
    };


    const getListSheet = async () => {
        try {
            isAppLoading.value = true;
            const result = await ajax('get_list_sheet');
            
            if (result && result.guid) {
                sheets.value = [result];
            } else if (result && Array.isArray(result)) {
                sheets.value = result;
            } else {
                sheets.value = [];
            }
        } catch (error) {
            console.error('Ошибка загрузки списка ведомостей:', error);
            toast.error('Ошибка загрузки списка ведомостей');
            sheets.value = [];
        } finally {
            isAppLoading.value = false;
        }
    };

    const updateSheet = (updatedSheet) => {
        const index = sheets.value.findIndex(sheet => sheet.guid === updatedSheet.guid);
        if (index !== -1) {
            sheets.value[index] = { ...sheets.value[index], ...updatedSheet };
        }
    };

    const insertGrade = async (parameters) => {
        try {
            const result = await ajax('insert_grade', parameters);
            if (result.success) {
                const sheet = sheets.value.find(s => s.guid === parameters.sheet);
                if (sheet && sheet.students) {
                    const student = sheet.students.find(st => st.guid === parameters.student);
                    if (student) {
                        const newGradeData = result.grade;
                        student.grade = newGradeData.grade.GUIDGrade;
                        student.adr = newGradeData.adr.grade;
                        student.ricd = newGradeData.ricd.grade;
                        student.frd = newGradeData.frd.grade;
                        student.ysc = newGradeData.ysc.grade;
                        student.teacher_full_name = newGradeData.teacher.FIO;
                    }
                }
                toast.success(strings.value.toast_success || 'Операция выполнена успешно');
            }
            return result;
        } catch (error) {
            console.error('Ошибка сохранения оценки:', error);
            toast.error('Ошибка сохранения оценки');
            throw error;
        }
    };

    const commissionAgreed = async (parameters) => {
        try {
            const result = await ajax('commission_agreed', parameters);
            if (result.success) {
                const sheet = sheets.value.find(s => s.guid === parameters.sheet_guid);
                if (sheet && sheet.teachers) {
                    const teacher = sheet.teachers.find(t => parseInt(t.user_id) === parseInt(parameters.user_id));
                    if (teacher) {
                        teacher.agreed = true;
                    }
                }
                toast.success(strings.value.toast_success || 'Согласование выполнено успешно');
            }
            return result;
        } catch (error) {
            console.error('Ошибка согласования:', error);
            toast.error('Ошибка согласования');
            throw error;
        }
    };

    const closeSheet = async (sheetGuid) => {
        try {
            const result = await ajax('close_sheet', { sheet_guid: sheetGuid });
            if (result.closed || result.success) {
                const sheet = sheets.value.find(s => s.guid === sheetGuid);
                if (sheet) {
                    sheet.closed = true;
                }
                toast.success(strings.value.toast_success || 'Ведомость закрыта успешно');
            }
            return result;
        } catch (error) {
            console.error('Ошибка закрытия ведомости:', error);
            toast.error('Ошибка закрытия ведомости');
            throw error;
        }
    };

    return {
        // State
        strings,
        sheets,
        userID,
        isAppLoading,
        showBRS,
        absenceGUID,
        divisionForBRS,
        typeControlCredit,
        typeControlExamine,
        typeControlDiffCredit,
        showDownloadButton,
        
        // Getters
        haveSheets,
        isSheetReachDateStart,
        isAllGradeSet,
        isThemedSheet,
        isCurrentUserSetAgreed,
        isAllAgreedSet,
        isAgreedAllowInSheet,
        isChairmanInSheet,
        
        // Actions
        loadComponentStrings: loadComponentStringsAction,
        loadCurrentUserID,
        loadSetShowBRS,
        loadDivisionForBRS,
        loadAbsenceGuid,
        loadShowDownloadButton,
        getListSheet,
        updateSheet,
        insertGrade,
        commissionAgreed,
        closeSheet
    };
});
