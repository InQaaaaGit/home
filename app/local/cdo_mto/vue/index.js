import Main from './components/main.vue';
import {createPinia} from 'pinia';
import {createApp, onErrorCaptured} from "vue";

function init({user_id}) {
    const pinia = createPinia();
    const app = createApp(Main);
    app.use(pinia);

    app.config.errorHandler = (err, instance, info) => {

        // Handle the error globally
        console.err("Global error:", error);
        console.log("Vue instance:", instance);
        console.log("Error info:", info);

        // Add code for UI notifications, reporting or other error handling logic
    };
    app.mount('#main_app');
}

export {
    init
};

