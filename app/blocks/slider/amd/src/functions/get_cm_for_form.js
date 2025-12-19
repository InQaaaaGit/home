import ajax from "core/ajax";
import Notification from "core/notification";

/**
 * @module block_slider/functions/get_cm_for_form
 */

const initOnchangeCourseFunc = (course_id) => {
    window.console.log(course_id);
    ajax.call([{
        methodname: 'block_slider_get_course_module', args: {courseid: course_id}, done: response => {
            window.console.log(response);

            let modList = document.getElementsByName('modList')[0];
            for (let i = 0; i < modList.options.length; i++) {
                modList.options[i].classList.add('d-none');

                window.console.log(String(modList.options[i].value));
                if (response.includes(String(modList.options[i].value))) {
                    modList.options[i].classList.remove('d-none');
                }
            }
            modList.options.selectedIndex = 0;
            // removeOptions(modList);
            /* window.console.log(modList);
             for (let i = 0; i <= response.length - 1; i++) {
                 const opt = document.createElement('option');
                 opt.value = response[i].id;
                 opt.innerHTML = response[i].name;
                 modList.appendChild(opt);
             }*/
            //modList.change();
        }, fail: Notification.exception,
    }]);
};

/*const removeOptions = (select) => {
    while (select.options.length > 0) {
        select.remove(0);
    }
};*/

const bindEvent = () => {
    document.getElementsByName('courseList')
        .forEach((item) => {
            item.addEventListener('change', (event) => {
                initOnchangeCourseFunc(event.target.value);
            });
        });
};
export default {bindEvent};