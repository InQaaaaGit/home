import Vue from 'vue';
import {store} from './store';
import MainApp from "./components/main-app.vue";
import vuetify from './plugins/vuetify'

function init({user_id, excluded_mods}) {


    store.dispatch('loadComponentStrings');
    store.dispatch('loadExcludedMods', excluded_mods);
    store.dispatch('loadCurrentUserID', user_id);
    store.dispatch('loadCourseList');
    store.dispatch('loadVariations');

    new Vue({
        vuetify,
        el: '#local_cdo_variations',
        components: {
            MainApp
        },
        store
    });

}

export {init};
