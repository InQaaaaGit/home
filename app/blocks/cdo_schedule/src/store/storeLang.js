import {defineStore} from 'pinia';
import * as moodleAjax from 'core/ajax';
import * as moodleStorage from 'core/localstorage';

export const useLangStore = defineStore('lang', {
    state: () => ({
        strings: {},
    }),
    actions: {
        async loadComponentStrings(component) {
            const lang = document.documentElement.lang.replace(/_/g, '-');
            const cacheKey = `lang/${component}/${lang}`;
            const cachedStrings = moodleStorage.get(cacheKey);

            if (cachedStrings) {
                this.strings = JSON.parse(cachedStrings);
                return;
            }

            try {
                const loadedStrings = await moodleAjax.call([{
                    methodname: 'core_get_component_strings',
                    args: { component, lang }
                }])[0];
                
                const strings = {};
                loadedStrings.forEach(s => {
                    strings[s.stringid] = s.string;
                });

                this.strings = strings;
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            } catch (error) {
                console.error('Error loading component strings:', error);
            }
        },
        __(key, a = null) {
            if (this.strings && this.strings[key]) {
                return this.strings[key];
            }
            return key;
        },
    },
});
