import Vue from 'vue'
import Vuex from 'vuex'

import program from "./program";
import app from "./app";
import messages from "./messages";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        APP: app,
        PROGRAM: program,
        MESSAGES: messages
    }
})