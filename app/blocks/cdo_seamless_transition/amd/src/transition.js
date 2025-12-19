import notification from 'core/notification';
import ajax from 'core/ajax';
import {get_string} from 'core/str';

export const init = () => {
    let transition = new Transition();
    transition.build().then();
};

class Transition {
    constructor() {
        this.selector = '.link-to-external-service';
        this.transition_data = {};
        this.transition_service = '';
        this.strings = {};
    }

    async build() {
        this.strings = await this.get_strings();
        this.add_events();
    }

    /**
     * @description Устанавливаем обработчик нажатия на кнопку перехода
     */
    add_events() {
        for (let item of document.querySelectorAll(this.selector)) {
            item.addEventListener('click', (e) => {
                this.build_transition(e.target);
                this.show_confirm_transition();
            });
        }
    }

    /**
     * @description Собираем данные по текущему переходу
     * @param item
     */
    build_transition(item) {
        this.transition_service = item.dataset.service;
        this.transition_data.transition_code = item.dataset.transitionCode;
        this.transition_data.transition_params = item.dataset.transitionParams;
    }

    /**
     * @description Показываем уведомление с подтверждением оценки
     */
    show_confirm_transition() {
        notification.confirm(
            "Подтверждение",
            "Уверены что хотите выполнить переход на внешний сервис?",
            "Подтвердить",
            "Отмена",
            function () {
                this.get_transition();
            }.bind(this)
        );
    }

    /**
     * @description Выполняем запрос на сервер
     */
    get_transition() {
        ajax.call([
            {
                methodname: this.transition_service,
                args: this.transition_data,
                done: this.transit,
                fail: notification.exception
            }
        ]);
    }

    /**
     * @description Открываем новую вкладку
     */
    transit(data) {
        window.open(data.transition_to, '_blank');
    }

    /**
     * @description Сообщение об успешном обновление оценки
     */
    show_alert() {
        notification.alert(
            this.strings.grades_alert_change_grade_title,
            this.strings.grades_alert_change_grade_message,
            this.strings.grades_alert_change_grade_yes
        );
    }

    async get_strings() {
        return {
            grades_confirm_change_grade_title: await get_string('grades_confirm_change_grade_title', this.component)
        };
    }
}