import Vue from 'vue'
import App from './App.vue'

import store from "./store/store";
import {} from "./plugins/bootstrap";

// function init(){

    Vue.config.productionTip = false

    new Vue({
        store,
        render: h => h(App)
    }).$mount('#app-files-learning-plan')

// }

// export {init}

