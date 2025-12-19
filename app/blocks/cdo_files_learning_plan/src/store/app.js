import {api} from "../services/api";

const state = {
    loading: false,
    settings: {},
    educationPrograms: [],
}

const mutations = {
    setSetting: (state, value) => {
        state.settings = value;
    },
    setEducationPrograms: (state, value) => {
        value.sort((x, y) => ((x.preview < y.preview) ? -1 : ((x.preview > y.preview) ? 1 : 0)))
        state.educationPrograms = value;
    },
    setLoading: (state, value) => {
        state.loading = value;
    },
}

const actions = {
    loadSettings: async ({commit,dispatch}) => {
        commit('setLoading', true);
        let promises = [];
        const promiseSettings = api.get.getSettings()
            .then(({data}) => {
                if(typeof data.error === 'undefined'){
                    commit('setSetting', data);
                    if(!data.isAuditor)
                        promises.push(dispatch('loadEducationPrograms'))
                } else {
                    commit('MESSAGES/IS_SHOW', {
                        message: data.error,
                        type: 'danger'
                    }, {root: true});
                }
            })
            .catch(() => {
                commit('MESSAGES/IS_SHOW', {
                    message: 'Ошибка получения настроек.',
                    type: 'danger'
                }, {root: true});
            })
        promises.push(promiseSettings);

        Promise.all(promises)
            .finally(() => {
                commit('setLoading', false);
            })
    },
    loadEducationPrograms: ({commit}, secretary = '') => {
        commit('setLoading', true);
        return api.get.getEducationPrograms(secretary)
            .then(({data}) => {
                console.log(data)
                if(typeof data.error === 'undefined') {
                    commit('setEducationPrograms', data);
                } else {
                    commit('MESSAGES/IS_SHOW', {
                        message: data.error,
                        type: 'danger'
                    }, {root: true});
                }
            })
            .catch(() => {
                commit('MESSAGES/IS_SHOW', {
                    message: 'Ошибка получения списка образовательных программ.',
                    type: 'danger'
                }, {root: true});
            })
            .finally(() => {
                commit('setLoading', false);
            })
    }
}

const getters = {
    isAuthorized: state => {
        return typeof state.settings.user_id !== 'undefined'
    },
    getLoading: state => {
        return state.loading
    },
    getSettings: state => {
        return state.settings
    },
    getEducationPrograms: state => {
        return state.educationPrograms
    },
    getYears: state => {
        return state.educationPrograms.reduce((years, item) => {
            if(years.find(item2 => item2 === item.year) === undefined)
                years.push(item.year)
            return years;
        }, ['Не выбрано'])
    },
    getEducationTypes: state => {
        return state.educationPrograms.reduce((result, item) => {
            if(result.find(item2 => item2.id === item.education_type_id) === undefined)
                result.push({id: item.education_type_id, name: item.education_type})
            return result;
        }, [{id: null, name: 'Не выбрано'}])
    },
    getEducationLevels: state => {
        return state.educationPrograms.reduce((result, item) => {
            if(result.find(item2 => item2.id === item.education_level_id) === undefined)
                result.push({id: item.education_level_id, name: item.education_level})
            return result;
        }, [{id: null, name: 'Не выбрано'}])
    },
    getProfiles: state => {
        return state.educationPrograms.reduce((result, item) => {
            if(result.find(item2 => item2.id === item.profile_id) === undefined)
                result.push({id: item.profile_id, name: item.profile})
            return result;
        }, [{id: null, name: 'Не выбрано'}])
    },
    getSpecialities: state => {
        return state.educationPrograms.reduce((result, item) => {
            if(result.find(item2 => item2.id === item.specialty_id) === undefined)
                result.push({id: item.specialty_id, name: item.specialty})
            return result;
        }, [{id: null, name: 'Не выбрано'}])
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
