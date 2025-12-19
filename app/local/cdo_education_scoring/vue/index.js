import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import Main from "./components/main.vue";
import { useAppStore } from './store/app';
import router from './router';

function init({
    user_id,
    capabilities,
    discipline_id,
    discipline_name,
    initial_route
}) {

    const app = createApp(Main, {
        userId: user_id ? Number(user_id) : null,
        capabilities: capabilities || {
            isAdmin: false,
            isStudent: false,
        }
    });
    const pinia = createPinia();

    app.use(pinia);
    app.use(router);

    // Инициализируем store с данными приложения
    const appStore = useAppStore();
    appStore.initializeApp(user_id, capabilities, discipline_id, discipline_name);
    app.use(Toast, {
        position: "top-right",
        timeout: 3000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: "button",
        icon: true,
        rtl: false
    });

    // Монтируем приложение
    app.mount('#cdo_education_scoring');
    
    // Переходим на начальный роут, если указан
    if (initial_route) {
        router.push(initial_route);
    }
}

export {init};
