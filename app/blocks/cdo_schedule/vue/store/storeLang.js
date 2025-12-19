import {defineStore} from 'pinia'
import * as moodleStorage from 'core/localstorage';
import * as moodleAjax from 'core/ajax';

export const useLangStore = defineStore('lang', {
    state: () => (
        {
            strings: []
        }
    ),
    getters: {
        getStrings(state) {
            return state.strings;
        }
    },
    actions: {
        async loadComponentStrings(pluginName) {
            const lang = document.getElementsByTagName('html')[0].getAttribute('lang').replace(/-/g, '_');
            const cacheKey = pluginName +'/strings/' + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                this.strings = JSON.parse(cachedStrings);
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': pluginName,
                        lang,
                    },
                };
                const loadedStrings = await moodleAjax.call([request])[0];
                let strings = {};
                loadedStrings.forEach((s) => {
                    strings[s.stringid] = s.string;
                });
                this.strings = strings;
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            }
        }
    }
})
