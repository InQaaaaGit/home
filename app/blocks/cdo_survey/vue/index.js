import Main from './components/survey.vue';
import {createPinia} from 'pinia';
import {createApp, onErrorCaptured} from "vue";
import { mask } from 'vue-the-mask';
import ToastPlugin from 'vue-toast-notification';
import {useMainStore} from "./store/store";
import notification from "core/notification";
import {useLangStore} from "./store/storeLang";
import {useUserStore} from "./store/storeUser";

async function init({
                        lastname, firstname,
                        apiToken='7a47bae38496a511b54ca371b0b8ec12b40a457b',
                        guidPassportRF='672b75ac-9346-11ee-8c0a-50ebf681124f'
}
) {
    const pinia = createPinia();
    const app = createApp(Main);

    app.use(ToastPlugin);
    app.use(pinia);

    const langStore = useLangStore();
    const userStore = useUserStore();
    console.log(1231231)
    // Устанавливаем данные пользователя
    userStore.setUserData(lastname, firstname, apiToken, guidPassportRF);
    //await mainStore.loadUserSurvey();
    await langStore.loadComponentStrings('block_cdo_survey');
    app.directive('mask', mask);
    app.config.errorHandler = (err, instance, info) => {
        notification.alert('Error', info, 'Cancel');
        console.log(err);
        console.log(info);
    };
    app.mount('#main_app');
}

export {
    init
};

