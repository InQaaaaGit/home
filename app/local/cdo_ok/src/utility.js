import moodleAjax from 'core/ajax';
import notification from 'core/notification';
class utility {
    async ajaxMoodleCall(methodname, args = {}) {
        let promises = moodleAjax.call([
            {
                methodname: methodname,
                args: args
            }

        ]);
        return await promises[0].done((response) => {
            return response;
        }).fail(function (ex) {
            notification.exception(ex);
            return false;
        });
    }
}

export default new utility();