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

    async uploadFile(blobFile, filepath = '/', id_vkr) {
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
            let arg = {
                binary_string: base64result,
                filename: blobFile.name,
                itemid: 1, //only one?
                filepath: filepath,
                id_vkr: id_vkr
            };
            return await this.ajaxMoodleCall('local_cdo_vkr_save_file', arg);
        }
        return [];

    }

    async getFilesVKR(id_vkr) {
        let FilesVKR = {};
        let arg = {
            id_vkr: id_vkr
        };
        let response = await this.ajaxMoodleCall('local_cdo_vkr_get_files', arg);
        if (Array.isArray(response)) {
            response = {
                comment: {},
                review: {},
                work: {},
                work_archive: []
            };
        }
        FilesVKR.comment = response.comment ?? {};
        FilesVKR.work = response.work ?? {};
        FilesVKR.work_archive = response.work_archive ?? [];
        FilesVKR.review = response.review ?? {};
        return FilesVKR;
    }

    async changeVKRStatus(id_vkr, newStatus, acquainted) {
        return await this.ajaxMoodleCall(
            'local_cdo_vkr_change_status_vkr',
            {
                id: id_vkr,
                status_id: newStatus,
                acquainted: acquainted
            }
        );
    }

    showPartOfString(string) {
        return string.substr(0,17) + '...';
    }
    writeVKRStatusName(status_id, statuses) {
        for (let statusKey in statuses) {
            if (statuses[statusKey].id === status_id) {
                return statuses[statusKey].name;
            }
        }
        return 'Неопределено';
    }

}

export default new utility();