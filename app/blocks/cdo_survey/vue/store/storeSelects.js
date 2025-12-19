import {defineStore, storeToRefs} from 'pinia'
import {useMainStore} from "./store";
import {useLangStore} from "./storeLang";


export const useSelectsStore = defineStore('storeSelects', {
    state: () => ({
        informationAboutPayer: ['independently', 'legalEntity', 'AnotherPayer'],
        userGroups: [
            'employeesOfEnterprisesAndOrganizations',
            'workersInTheEducationSector',
            'individualsHoldingStatePositionsAndPositionsInTheStateCivilService',
            'individualsHoldingMunicipalPositionsAndMunicipalServicePositions',
            'individualsDismissedFromMilitaryService',
            'individualsReferredByEmploymentService',
            'students',
            'others'
        ],
        allSelects: {},

    }),
    actions: {
        async fillSelectsOptions() {
            const stringsStore = useLangStore();
            const strings = stringsStore.getStrings;
            const _vm = this;
            const state = this.$state;
            for (const [key, value] of Object.entries(state)) {
                if (key !== 'allSelects') {
                    let informationAboutPayerOptions = [];
                    value.forEach(option => {
                        if (strings)
                            informationAboutPayerOptions.push({value: option, name: strings[option]});
                    });
                    _vm.allSelects[key] = informationAboutPayerOptions;
                }
            }
        }
    }
})
