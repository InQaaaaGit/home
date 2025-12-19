import moodleAjax from 'core/ajax';
import notification from 'core/notification';

class utility_debts {
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
    async uploadFile(blobFile) {

        const fileToBase64 = (blobFile) => {
            return new Promise(resolve => {
                let file = blobFile;
                let reader = new FileReader();
                reader.onload = function (event) {
                    resolve(event.target.result);
                };
                reader.readAsDataURL(file);
            });
        };
        let base64result = await fileToBase64(blobFile).then(result => {
            return result;
        });
        base64result = base64result.split(',')[1];

        if (!!base64result) {
            return {
                binaryString: base64result,
                filename: blobFile.name,
                type: blobFile.type
            };
        }
        return {};

    }
}

export default new utility_debts();