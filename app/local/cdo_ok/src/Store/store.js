import utility from "@/utility";

export const mutations = {
    selectTab(state, newTabIndex) {
        state.selectedTab = newTabIndex;
    },
    updateDraggableItems(state, data) {
        data.sort((a, b)=> {
            return a.sort > b.sort;
        });
        state.questions = _.uniqBy(state.questions, 'id');
        state.questions = data;
    },
    updateQuestion(state, data) {
        state.questions[data.sort] = data
    },
    createQuestion(state, data) {
        //console.log(data)
        state.questions.push(data);

        state.questions = _.uniqBy(state.questions, 'id');
        state.questions = _.compact(state.questions);
    },
    deleteQuestion(state, data) {
        state.questions = _.reject(state.questions, { id: data.id });
    }
}

export const actions = {
    async updateQuestionsSortOrder({commit}, data) {
        data.sort((a, b) => {
            return a.group_tab > b.group_tab && a.sort > b.sort;
        });
        let result = await utility.ajaxMoodleCall('local_cdo_ok_update_questions', {data: data});
        commit('updateDraggableItems', data);
    },
    async updateQuestionAPI({commit}, data) {
        let result = await utility.ajaxMoodleCall('local_cdo_ok_update_question', {data: data});
        commit('updateQuestion', data);
    },
    async createQuestionAPI({commit, state}) {
        state.isCreateLoaderOn = true;
        let question = await utility.ajaxMoodleCall(
            'local_cdo_ok_create_question',
            {
                group_tab: state.selectedTab,
                sort: state.questions.length + 1,
            }
        );
        commit('createQuestion', question);
        state.isCreateLoaderOn = false;
    },
    async deleteQuestionAPI({commit}, item) {
        let result = await utility.ajaxMoodleCall(
            'local_cdo_ok_delete_question',
            {
                id: item.id
            }
        );
        if (result)
            commit('deleteQuestion', item);
        //TODO else toast
    }
}
export default {
    mutations,
    actions
}