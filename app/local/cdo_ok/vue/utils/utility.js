import moodleAjax from 'core/ajax';
import notification from 'core/notification';

/**
 * Класс для работы с AJAX запросами к Moodle
 * @class
 */
class Utility {
    /**
     * Выполняет AJAX вызов к Moodle API
     * @param {string} methodname - Название метода API
     * @param {Object} args - Аргументы для метода
     * @returns {Promise<*>} Результат выполнения метода
     */
    async ajaxMoodleCall(methodname, args = {}) {
        const promises = moodleAjax.call([
            {
                methodname: methodname,
                args: args
            }
        ]);
        
        return await promises[0]
            .done((response) => {
                return response;
            })
            .fail((ex) => {
                notification.exception(ex);
                return false;
            });
    }
}

export default new Utility();









