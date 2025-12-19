import {api} from "../services/api";

const emptyProgram = {
    isLoad: false,
    files: [],
    discipline_files: [],
    web_links: [],
    academic_plans: [],
    structures: []
}

const state = {
    loadingProgram: false,
    savingProgram: false,
    program: emptyProgram,
    selectedProgram: null,
    structures: [],
    debug: true,
}

const mutations = {
    setLoadingProgram: (state, value) => {
        state.loadingProgram = value
    },
    setProgramDataAgreed: (state, value) => {
        state.program.discipline_files.map(item => {
            if (item.rpd_id === value.rpd_id) {
                item.agreed_date = value.date;
                item.agreed_number = value.number;
                item.agreed_structures = value.structure;
            }
        })
    },
    setProgramData: (state, value) => {
        value.files.map(item => {
            item.path = `/blocks/cdo_files_learning_plan/get_file.php?file_id=${item.id}&name=${item.name}`
        })
        value.discipline_files.map(item => {
            item.description_text = 'Тип: ' + item.type
                + (item.module_name ? '  Модуль: ' + item.module_name : '')
                + (item.discipline_index ? '  Индекс дисциплины: ' + item.discipline_index : '')
        })
        value.discipline_files.forEach(discipline => {
            discipline.files.map(item => {
                item.path = `/blocks/cdo_files_learning_plan/get_file.php?file_id=${item.guidfile}&name=${item.filename}`
            })
        })
        state.program = value
    },
    setSelectedProgram: (state, value) => {
        state.selectedProgram = value;
    },
    setSavingProgram: (state, value) => {
        state.savingProgram = value;
    },
    insertProgramLink: (state, value) => {
        state.program.web_links.push(value)
    },
    updateProgramLink: (state, value) => {
        let findIndex = state.program.web_links.findIndex(item => item.link_guid === value.link_guid);
        state.program.web_links.splice(findIndex, 1, value)
    },
    deleteProgramLink: (state, link_guid) => {
        state.program.web_links =  state.program.web_links.filter(item => item.link_guid !== link_guid)
    },
    insertProgramFile: (state, value) => {
        value.path = `/blocks/cdo_files_learning_plan/get_file.php?file_id=${value.id}&name=${value.name}`
        state.program.files.push(value)
    },
    updateProgramFile: (state, value) => {
        value.path = `/blocks/cdo_files_learning_plan/get_file.php?file_id=${value.id}`
        let findIndex = state.program.files.findIndex(item => item.id === value.id);
        state.program.files.splice(findIndex, 1, value)
    },
    deleteProgramFile: (state, id) => {
        state.program.files =  state.program.files.filter(item => item.id !== id)
    },
    updateDisciplineNotes: (state, data) => {
        const {discipline_id, type, discipline_index, module_id, notes} = data
        const find = state.program.discipline_files.find(item =>
            item.discipline_id === discipline_id &&
            item.type === type &&
            item.discipline_index === discipline_index &&
            item.module_id === module_id
        )
        if(find !== undefined)
            find.notes = notes
    },
    deleteDisciplineFile: (state, data) => {
        const {discipline_id, type, discipline_index, module_id, guidfile} = data
        const find = state.program.discipline_files.find(item =>
            item.discipline_id === discipline_id &&
            item.type === type &&
            item.discipline_index === discipline_index &&
            item.module_id === module_id
        )
        if(find !== undefined)
            find.files = find.files.filter(item => item.guidfile !== guidfile)
    },
    insertDisciplineFiles: (state, data) => {
        const {discipline_id, type, discipline_index, module_id, files} = data
        const find = state.program.discipline_files.find(item =>
            item.discipline_id === discipline_id &&
            item.type === type &&
            item.discipline_index === discipline_index &&
            item.module_id === module_id
        )
        files.map(item => {
            item.path = `/blocks/cdo_files_learning_plan/get_file.php?file_id=${item.guidfile}&name=${item.filename}`
        })
        if(find !== undefined)
            find.files = find.files.concat(files);
    },
    updateFileProgramDescription: (state, {id, description}) => {
        const find = state.program.files.find(item => item.id === id)
        if(find !== undefined)
            find.description = description
    },
}

const actions = {
    changeAgreedInfo: ({commit,dispatch}, data)  => {
        console.log(data);
        commit('setProgramDataAgreed', data);
        return data;
    },
    loadProgramData: ({commit,dispatch}, docNumber) => {
        commit('setLoadingProgram', true);
        api.get.getEducationProgram(docNumber)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('setProgramData', {...data, isLoad: true});
                    commit('setSelectedProgram', docNumber);
                } else {
                    commit('setProgramData', emptyProgram);
                    dispatch('MESSAGES/showError', data.error, {root: true});
                }
            })
            .catch(() => {
                commit('setProgramData', emptyProgram);
                dispatch('MESSAGES/showError', 'Ошибка получения данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setLoadingProgram', false);
            })
    },
    putFileProgram: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        return api.update.putProgramFile(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    if(formData.mode === 'update_file')
                        commit('updateProgramFile', data);
                    else
                        commit('insertProgramFile', data);
                    dispatch('MESSAGES/showSuccess', 'Данные успешно сохранены.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка сохранения данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    deleteFileProgram: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        api.delete.deleteProgramFile(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('deleteProgramFile', formData.file_id);
                    dispatch('MESSAGES/showSuccess', 'Файл успешно удален.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка удаления данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    putLinkProgram: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        return api.update.putProgramLink(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if(typeof data.error === "undefined")
                    if(formData.update_mode === 'new_link')
                        commit('insertProgramLink', data)
                    else
                        commit('updateProgramLink', data)
                else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка сохранения данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    deleteLinkProgram: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        api.delete.deleteProgramLink(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('deleteProgramLink', formData.link_guid);
                    dispatch('MESSAGES/showSuccess', 'Ссылка успешно удалена.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка удаления данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    putNotesProgram: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        return api.update.putDisciplineNotes(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('updateDisciplineNotes', formData);
                    dispatch('MESSAGES/showSuccess', 'Комментарий успешно сохранен.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка сохранения комментария.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    deleteDisciplineFile: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        api.delete.deleteDisciplineFile(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('deleteDisciplineFile', {...formData, guidfile: formData.guidfile});
                    dispatch('MESSAGES/showSuccess', 'Файл успешно удален.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка удаления данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    putDisciplineFile: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        return api.update.putDisciplineFiles(formData)
            .then(({data}) => {
                if(state.debug) console.log(data);
                if (typeof data.error === 'undefined') {
                    commit('insertDisciplineFiles', {...formData, files: data.files});
                    dispatch('MESSAGES/showSuccess', 'Файлы успешно успешно загружены.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка сохранения файла.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
    putFileProgramDescription: ({commit,dispatch}, formData) => {
        commit('setSavingProgram', true);
        return api.update.putProgramFileDescription(formData)
            .then(({data}) => {
                if(state.debug) console.log(data)
                if (typeof data.error === 'undefined') {
                    commit('updateFileProgramDescription', {id: formData.id, description: data.description});
                    dispatch('MESSAGES/showSuccess', 'Данные успешно сохранены.', {root: true});
                } else
                    dispatch('MESSAGES/showError', data.error, {root: true});
            })
            .catch(() => {
                dispatch('MESSAGES/showError', 'Ошибка сохранения данных по образовательной программе.', {root: true});
            })
            .finally(() => {
                commit('setSavingProgram', false);
            })
    },
}

const getters = {
    getLoadingProgram: state => {
        return state.loadingProgram
    },
    getProgramData: state => {
        return state.program
    },
    getSelectedProgram: state => {
        return state.selectedProgram
    },
    getSavingProgram: state => {
        return state.savingProgram
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}