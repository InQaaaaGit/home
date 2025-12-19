Vue.use(VueToast);

const librarianManager = new Vue({
    el: '#librarian-manager-confirm',
    data: () => ({
        comment: '',
        tableData: [],
        literatureList: {
            mainSelected: [],
            additionalSelected: [],
            methodicalSelected: []
        },
        currentStatus: "0",
        guid: '',
        isLoader: false,
    }),
    created() {
        this.getLiteratureForApproval();
        this.guid = this.findGetParameter('guid');
    },
    computed: {
        alreadySend(){
           return !["1", "3"].includes(this.currentStatus);
        },
        showAlphabetStatus() {
            switch (this.currentStatus) {
                case "1":
                    return "Согласован";
                    break
                case "2":
                    return "На согласовании";
                    break
                case "3":
                    return "Не согласован";
                    break
                case "4":
                    return "В разработке";
                    break

            }

        },
        displayDiscipline() {
            return this.findGetParameter("discipline");
        },
        filteredTable() {
            return this.tableData.filter(item => {
                if (this.selectedFilter.filter === 'all') return item
                else return item.status === this.selectedFilter.filter
            }).filter(item => {
                if (this.selectedYear.value)
                    return item.year === this.selectedYear.value
                else return item.year
            }).filter(item => {
                if (this.selectedDirection.value)
                    return item.direction === this.selectedDirection.value
                else return item.direction
            }).filter(item => {
                if (this.selectedEducationLevel.value)
                    return item.educationLevel === this.selectedEducationLevel.value
                else return item.educationLevel
            }).filter(item => {
                if (this.selectedTrainingLevels.value)
                    return item.trainingLevel === this.selectedTrainingLevels.value
                else return item.trainingLevel
            }).filter(item => {
                if (this.selectedEducationPrograms.value)
                    return item.discipline === this.selectedEducationPrograms.value
                else return item.discipline
            });
        },
        completedDisciplineLength() {
            return this.tableData.filter(item => item.librarianStatus === '1').length;
        },
        approvalDisciplineLength() {
            return this.tableData.filter(item => item.librarianStatus === '2').length;
        },
        developingDisciplineLength() {
            return this.tableData.filter(item => item.librarianStatus === '3').length;
        },
        notAllocatedDisciplineLength() {
            return this.tableData.filter(item => item.librarianStatus === '4').length;
        },
        discardedBooks() {
            return Object.values(this.literatureList).flat().filter(item => item.approval === false);
        },
        isDisabledApproveButton() {
            const allLiteratureArray = Object.values(this.literatureList).flat();
            console.log(this.literatureList)
            return allLiteratureArray.some(item => item.approval === true);
        },
        possibleDiscardLiterature() {
            const allLiteratureArray = Object.values(this.literatureList).flat();
            return allLiteratureArray.some(item => item.approval === true && !item.commentary);
        }
    },
    methods: {
        sendApprovalOrDisapproval(status) {
            this.isLoader = true;
            const vm = this;
            const rpd_id = this.findGetParameter('rpd_id');
            const user_id = this.findGetParameter('user_id');
            const block_control = this.findGetParameter('module');
            let structToSend = {
                rpd_id: rpd_id,
                user_id: user_id,
                blockControl: block_control,
                status: status,
                comment: this.comment,
                literature: this.literatureList,
                guid: this.guid
            };
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'send_literature_for_approve',
                            args: {
                                JSON: JSON.stringify(structToSend)
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.isApproval = true;
                        vm.currentStatus = response.status;
                        vm.$toast.open({
                            message: `Успешно отправлено!`,
                            type: "success",
                            duration: 5000,
                            dismissible: true
                        });
                        vm.isLoader = false;
                    }).fail(function (ex) {
                        vm.isLoader = false;
                        notification.exception(ex);
                    });
                });
        },
        getLiteratureForApproval() {
            const vm = this;
            this.isLoader = true;
            const rpd_id = this.findGetParameter('rpd_id');
            const user_id = this.findGetParameter('user_id');
            const guid = this.guid;
            require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

                var promises = ajax.call([
                    {
                        methodname: 'get_literature_for_approve',
                        args: {
                            user_id: user_id,
                            rpd_id: rpd_id,
                            guid: vm.guid
                        }
                    }
                ]);
                promises[0].done((response) => {
                    if (!!response) {
                        vm.literatureList = response.literature;
                        vm.comment = response.comment;
                        vm.currentStatus = response.status;

                    } else {
                        vm.$toast.open({
                            message: `Литература не найдена!`,
                            type: "error",
                            duration: 5000,
                            dismissible: true
                        });

                    }
                    vm.isLoader = false;
                }).fail(function (ex) {
                    vm.isLoader = false;
                    notification.exception(ex);
                });
            });
        },
        findGetParameter(parameterName) {
            var result = null,
                tmp = [];
            location.search
                .substr(1)
                .split("&")
                .forEach(function (item) {
                    tmp = item.split("=");
                    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
                });
            return result;
        },
        approve() {
            this.sendApprovalOrDisapproval(1);
        },
        discard() {
            if (this.possibleDiscardLiterature) {
                this.$toast.open({
                    message: `Пожалуйста, укажите все причины отказа`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            } else {
                this.sendApprovalOrDisapproval(3);
            }
        },
        deselectBook(book) {
            book.approval = !book.approval;
            if (!book.approval) book.commentary = '';
        }
    }
})