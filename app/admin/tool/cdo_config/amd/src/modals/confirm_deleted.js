import notification from 'core/notification';
import {get_strings} from 'core/str';
import {main_config} from 'tool_cdo_config/main_config';

/**
 * @module tool_cdo_config/modals/confirm_deleted
 * @type {function(): *}
 */

export default class confirm_deleted {

    constructor() {
        this.strings = [];
        this.component = main_config.component;
        this.confirm_deleted_class = main_config.confirm_deleted_class;
    }

    get_strings () {
        let strings = [
            {key: 'js_modal_title_deleted', component: this.component},
            {key: 'js_modal_question_deleted', component: this.component},
            {key: 'js_modal_yes_label_deleted', component: this.component},
            {key: 'js_modal_no_label_deleted', component: this.component}
        ];
        return get_strings(strings);
    }

    async init () {
        this.strings = await this.get_strings();
        this.bind_elements_event();
    }

    bind_elements_event () {
        let deleted = document.querySelectorAll(this.confirm_deleted_class);
        if (deleted) {
            for (let item of deleted) {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.active_element = item;
                    this.build_notification();
                });
            }
        }
    }

    build_notification () {
        let _active_element = this.active_element;
        notification.confirm(
            this.strings.slice(0, 1)[0],
            this.strings.slice(1, 2)[0],
            this.strings.slice(2, 3)[0],
            this.strings.slice(3, 4)[0],
            function () {
                window.location.href = _active_element.href;
            }
        );
    }
}
