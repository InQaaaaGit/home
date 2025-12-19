import {defineStore} from 'pinia';
import * as moodleStorage from 'core/localstorage';
import * as moodleAjax from 'core/ajax';

export const useMainStore = defineStore('main', {
    state: () => (
        {
            count: 0,
            name: 'Eduardo',
            strings: []
        }
    ),
    actions: {
        async loadComponentStrings() {
            const lang = document.getElementsByTagName('html')[0].getAttribute('lang').replace(/-/g, '_');
            const cacheKey = 'local_cdo_mto/strings/' + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                this.strings = JSON.parse(cachedStrings);
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': 'local_cdo_mto',
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
