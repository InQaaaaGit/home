import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import Notification from "core/notification";

Vue.use(Vuex);
Vue.use(Toast);

export const store = new Vuex.Store({
    state: {
        strings: {},
        courseList: [],
        variations: [],
        userID: 2,
        isAppLoading: false,
        pluginName: 'local_cdo_variations',
        excludedMods: []
    },
    getters: {},
    mutations: {
        setStrings(state, strings) {
            state.strings = strings;
        },
        setVariations(state, variations) {
            state.variations = variations;
        },
        setCurrentUserId(state, userID) {
            state.userID = userID;
        },
        setAppLoader(state, status) {
            state.isAppLoading = status;
        },
        setCourseList(state, courseList) {
            state.courseList = courseList;
        },
        setExcludedMods(state, excludedMods) {
            state.excludedMods = excludedMods;
        }
    },
    actions: {
        setAppLoader(context, status) {
            context.commit('setAppLoader', status);
        },
        loadExcludedMods(context, excludedMods) {
            context.commit('setExcludedMods', excludedMods);
        },
        async loadVariations(context) {
            context.commit('setAppLoader', true);
            let result = await ajax('cdo_get_user_variations',
                {}
            );
            context.commit('setVariations', result);
            context.commit('setAppLoader', false);
        },
        async loadCourseList(context) {
            let result = await ajax('core_course_search_courses',
                {
                    criterianame: 'search',
                    criteriavalue: ''
                }
            );
            context.commit('setCourseList', result);
        },
        async updateModuleInfo(context, {modules, courseid}) {
            return await ajax('cdo_update_module_availability_info',
                {
                    modules: modules,
                    courseid: courseid
                }
            );
        },
        loadCurrentUserID(context, userID) {
            context.commit('setCurrentUserId', userID);
        },
        async loadCourseContent(context, courseid) {
            context.commit('setAppLoader', true);
            const result =
                await ajax('cdo_core_course_get_contents',
                    {
                        courseid: courseid
                    }
                );
            context.commit('setAppLoader', false);

            return result;
        },
        async loadComponentStrings({commit, state}) {
            const lang = document.getElementsByTagName('html')[0].getAttribute('lang').replace(/-/g, '_');
            const cacheKey = state.pluginName + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                commit('setStrings', JSON.parse(cachedStrings));
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': state.pluginName,
                        lang,
                    },
                };
                const loadedStrings = await moodleAjax.call([request])[0];

                let strings = {};
                loadedStrings.forEach((s) => {
                    strings[s.stringid] = s.string;
                });
                commit('setStrings', strings);
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            }
        },
    }
});

export async function ajax(method, args) {
    const request = {
        methodname: method,
        args: args
    };

    try {
        return await moodleAjax.call([request])[0];
    } catch (e) {
        Notification.exception(e);
        throw e;
    }
}