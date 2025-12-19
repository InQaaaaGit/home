/**
 * Обертка для вызова Moodle AJAX функций через core/ajax.
 * Использует AMD require для загрузки core/ajax.
 */
export const ajax = (method, data = {}) => {
    return new Promise((resolve, reject) => {
        // Используем глобальный require из AMD системы Moodle
        if (typeof require !== 'undefined') {
            require(['core/ajax'], (Ajax) => {
                Ajax.call([{
                    methodname: method,
                    args: data
                }])[0].then(resolve).catch(reject);
            });
        } else {
            // Fallback для случая, если require недоступен
            reject(new Error('AMD require не доступен'));
        }
    });
};

