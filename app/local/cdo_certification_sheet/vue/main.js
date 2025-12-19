import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

// Установка динамического publicPath для Webpack
if (typeof M !== 'undefined' && M.cfg && M.cfg.wwwroot) {
  __webpack_public_path__ = `${M.cfg.wwwroot}/local/cdo_certification_sheet/amd/build/`;
}

import { useCertificationStore } from './stores/certification.js';
import sheets from './components/version3/sheets.vue';

function init({
    user_id,
    show_BRS,
    division_for_BRS,
    absence_guid,
    show_download_button = false,
    enable_vue_components = true
}) {

    const app = createApp(sheets);
    const pinia = createPinia();

    app.use(pinia);
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

    // Инициализируем store
    const certificationStore = useCertificationStore();

    // Загружаем данные
    certificationStore.loadComponentStrings();
    certificationStore.loadCurrentUserID(user_id);
    certificationStore.loadSetShowBRS(show_BRS);
    certificationStore.loadAbsenceGuid(absence_guid);
    certificationStore.loadShowDownloadButton(show_download_button);

    // Монтируем приложение
    app.mount('#local_cdo_certification_sheet');
}

export {init};
