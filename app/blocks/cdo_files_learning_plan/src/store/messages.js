
const state = {
    isShow: false,
    type: 'success',
    message: '',
    time: 6000,
    isTop: true,
    isBottom: false,
    isRight: false,
    isLeft: true
}

const mutations = {
    'IS_SHOW': (state,
                {
                    isShow = true,
                    type = 'success',
                    message,
                    time = 6000,
                    isTop = true,
                    isBottom = false,
                    isRight = false,
                    isLeft = true
                }) => {
        state.isShow = isShow;
        state.type = type;
        state.message = message;
        state.time = time;
        state.isTop = isTop;
        state.isBottom = isBottom;
        state.isRight = isRight;
        state.isLeft = isLeft;
    },
    'SET_IS_SHOW': (state, isShow) => {
        state.isShow = isShow;
    }
}

const actions = {
    showSuccess: ({commit}, message) => {
        commit('IS_SHOW', {type: 'success', message: message})
    },
    showError: ({commit}, message) => {
        commit('IS_SHOW', {type: 'danger', message: message})
    }
}

const getters = {

}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}