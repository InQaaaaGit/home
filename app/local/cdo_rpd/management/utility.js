
class utility {
    setStatus(rpd_id) {

        require(['core/ajax', 'core/notification', 'core/loadingicon'],
             function (ajax, notification, LoadingIcon) {
                let promises = ajax.call([
                    {
                        methodname: 'set_status',
                        args: {
                            rpd_id: rpd_id,
                            status: 2 // -
                        }
                    }
                ]);
                promises[0].done((response) => {
                   return response.status;
                }).fail(function (ex) {
                    notification.exception(ex);
                });
            });

    },
}

export default new utility();