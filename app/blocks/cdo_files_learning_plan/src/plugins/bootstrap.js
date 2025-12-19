import Vue from 'vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import {
    CardPlugin,
    ButtonPlugin,
    FormFilePlugin,
    FormSelectPlugin,
    IconsPlugin,
    LinkPlugin,
    ModalPlugin,
    SkeletonPlugin,
    TabsPlugin,
    LayoutPlugin,
    FormGroupPlugin,
    FormPlugin,
    SpinnerPlugin,
    FormInputPlugin,
    ToastPlugin,
    FormRadioPlugin,
    FormTextareaPlugin
} from "bootstrap-vue";

Vue.use(CardPlugin);
Vue.use(FormSelectPlugin);
Vue.use(FormFilePlugin);
Vue.use(ModalPlugin);
Vue.use(TabsPlugin);
Vue.use(SkeletonPlugin);
Vue.use(LinkPlugin);
Vue.use(IconsPlugin);
Vue.use(ButtonPlugin);
Vue.use(LayoutPlugin);
Vue.use(FormGroupPlugin);
Vue.use(FormPlugin);
Vue.use(SpinnerPlugin);
Vue.use(FormInputPlugin);
Vue.use(ToastPlugin);
Vue.use(FormRadioPlugin);
Vue.use(FormTextareaPlugin);

export {};
