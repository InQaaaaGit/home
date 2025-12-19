Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
});
Vue.component('multiselect', window.VueMultiselect.default);
Vue.use(VueToast);

const appPersonalZav = new Vue({
        el: '#app-zav-kaf',
        data: () => ({
            loadingDevelopers: false,
            newGuid: '',
            isExecutiveSecretary: false,
            modalInfo: [],
            showEventsModal: false,
            currentItem: {},
            reasonDisagreed: '',
            showDisagreedModal: false,
            modules: [],
            isAdmin: true,
            modalTabs: [
                {id: 1, name: 'Разработчики', filter: 1},
                {id: 2, name: 'Соразработчики', filter: 2},
            ],
            hideCodev: false,
            selectedModalTab: null,
            selectedDeveloperID: null,
            showFilters: false,
            showDeveloperModal: false,
            years: [],
            directions: [],
            educationLevels: [],
            trainingLevels: [],
            educationPrograms: [],
            profiles: [],
            selectedYear: '',
            selectedDirection: '',
            selectedEducationLevel: '',
            selectedTrainingLevels: '',
            selectedEducationPrograms: '',
            selectedProfiles: '',
            optionsFilters: [
                {id: 1, name: 'Все', filter: 'all'},
                {id: 2, name: 'Утвержденные', filter: '1'},
                {id: 3, name: 'На согласовании', filter: '2'},
                {id: 4, name: 'В разработке', filter: '3'},
                {id: 5, name: 'Не распеределенные', filter: '4'},
            ],
            selectedFilter: '',
            tableColumns: [
                {
                    text: 'Дисциплина',
                    value: 'discipline',
                    sortable: true
                },
                {
                    text: 'Направление',
                    value: 'direction',
                    sortable: true
                },
                {
                    text: 'Профиль',
                    value: 'profile',
                    sortable: true
                },
                {
                    text: 'Тип/Модуль',
                    value: 'typeAndModule',
                    sortable: true
                },
                {
                    text: 'Год',
                    value: 'year',
                    align: 'center',
                    sortable: true
                },
                {
                    text: 'Разработчик / Соразработчик',
                    value: 'developers',
                    sortable: true,
                    sortFun: function (data, sortOrder) {
                        console.log(data, sortOrder);
                        const order = sortOrder['developers'] || 1;
                        return _.cloneDeep(data).sort((a, b) => {

                            a = a.developers.mainDeveloper[0]?.user.toLowerCase() || '';
                            b = b.developers.mainDeveloper[0]?.user.toLowerCase() || '';
                            return (a === b ? 0 : a > b ? 1 : -1) * order;
                        });
                    }
                },
                {
                    text: 'Статус',
                    value: 'status',
                    sortable: true
                },
                {
                    text: '',
                    value: 'id',
                    sortable: false
                },
                {
                    text: '',
                    value: 'info',
                    sortable: false
                },
                {
                    text: '',
                    value: 'discipline_index',
                    sortable: false
                }
            ],
            tableData: [],
            searchDevelopersField: '',
            searchCoDevelopersField: '',
            changeDisciplineID: null,
            changeDisciplineDevelopersObj: {},
            selectedCoDeveloperToChange: {},
            selectedCoDeveloperToChangeIDX: '',
            isLoader: false,
            isDisciplineHasDeveloper: false,
            disciplineOldID: null,
            type: '',
        }),
        created() {
            this.selectedModalTab = {id: 1, name: 'Разработчики', filter: 1};
            /*if (this.isAdmin) {
                this.selectedModalTab = {id: 1, name: 'Разработчики', filter: 1};
            } else {
                this.selectedModalTab = {id: 2, name: 'Соразработчики', filter: 2};
            }*/
            this.selectedFilter = this.optionsFilters[0];
            this.getFilters();
            this.getUserInfo();
        },
        watch: {
            filteredTable: {
                handler: function (old, newVal) {
                    this.years = this.createFuckingFilter('year', old);
                    this.trainingLevels = this.createFuckingFilter('trainingLevel', old);
                    this.educationPrograms = this.createFuckingFilter('discipline', old);
                    this.directions = this.createFuckingFilter('direction', old);
                    this.educationLevels = this.createFuckingFilter('educationLevel', old);
                    this.profiles = this.createFuckingFilter('profile', old);
                    this.educationPrograms.sort(function (a, b) {
                            if (a.value.toLowerCase() > b.value.toLowerCase()) {
                                return 1;
                            }
                            if (a.value.toLowerCase() < b.value.toLowerCase()) {
                                return -1;
                            }
                            return 0;

                        }
                    );
                    this.profiles.sort(function (a, b) {
                            if (a.value.toLowerCase() > b.value.toLowerCase()) {
                                return 1;
                            }
                            if (a.value.toLowerCase() < b.value.toLowerCase()) {
                                return -1;
                            }
                            return 0;

                        }
                    );
                    //this.createFuckingFilter();
                },
            }
        },
        computed: {
            filterModals() {
                /*if (!this.hideCodev) {
                    return [this.modalTabs[0]];
                }
*/
                return this.modalTabs;
            },
            filteredDevelopers() {
                if (Object.keys(this.changeDisciplineDevelopersObj).length) {
                    let cloneDevs = _.cloneDeep(this.changeDisciplineDevelopersObj.allDisciplineDevelopers);
                    cloneDevs = cloneDevs = _.uniqBy(cloneDevs, 'id');
                    cloneDevs.sort((a, b) => {
                        const nameA = a.user.toLowerCase(), nameB = b.user.toLowerCase()
                        if (nameA < nameB)
                            return -1
                        if (nameA > nameB)
                            return 1
                        return 0
                    });
                    return cloneDevs
                        .filter(item => item.user.toLowerCase().includes(this.searchDevelopersField.toLowerCase().trim()));
                }
            },
            filteredCoDevelopers() {
                if (Object.keys(this.changeDisciplineDevelopersObj).length) {
                    const cloneDevs = _.cloneDeep(this.changeDisciplineDevelopersObj.allDisciplineDevelopers);
                    cloneDevs.sort((a, b) => {
                        const nameA = a.user.toLowerCase(), nameB = b.user.toLowerCase()
                        if (nameA < nameB)
                            return -1
                        if (nameA > nameB)
                            return 1
                        return 0
                    });
                    return cloneDevs
                        .filter(item => item.user.toLowerCase().includes(this.searchCoDevelopersField.toLowerCase().trim()));
                }


            },
            filteredTable() {
                return this.tableData.filter(item => {
                    if (this.selectedFilter.filter === 'all') return item
                    else return item.status === this.selectedFilter.filter
                }).filter(item => {
                    if (this.selectedYear !== null && this.selectedYear.value)
                        return item.year === this.selectedYear.value
                    else return item
                }).filter(item => {
                    if (this.selectedDirection !== null && this.selectedDirection.value)
                        return item.direction === this.selectedDirection.value
                    else return item
                }).filter(item => {
                    if (this.selectedEducationLevel !== null && this.selectedEducationLevel.value)
                        return item.educationLevel === this.selectedEducationLevel.value
                    else return item
                }).filter(item => {
                    if (this.selectedTrainingLevels !== null && this.selectedTrainingLevels.value)
                        return item.trainingLevel === this.selectedTrainingLevels.value
                    else return item
                }).filter(item => {
                    if (this.selectedProfiles !== '' && this.selectedProfiles !== null && this.selectedProfiles.value)
                        return item.profile === this.selectedProfiles.value
                    else return item
                }).filter(item => {
                    if (this.selectedEducationPrograms !== null && this.selectedEducationPrograms.value)
                        return item.discipline === this.selectedEducationPrograms.value
                    else return item

                });
            },
            completedDisciplineLength() {
                return this.tableData.filter(item => item.status === '1').length;
            }
            ,
            approvalDisciplineLength() {
                return this.tableData.filter(item => item.status === '2').length;
            }
            ,
            developingDisciplineLength() {
                return this.tableData.filter(item => item.status === '3').length;
            }
            ,
            notAllocatedDisciplineLength() {
                return this.tableData.filter(item => item.status === '4').length;
            }
            ,
        },
        methods: {
            getUserInfo() {
                const vm = this;
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {
                        //notification.init(1);
                        var promises = ajax.call([
                            {
                                methodname: 'get_current_user_info',
                                args: {}
                            }
                        ], false);
                        promises[0].done((response) => {
                            vm.type = response.type;
                        }).fail(function (ex) {
                            notification.exception(ex);
                        });

                    })
            },
            closeModal() {
                this.showEventsModal = false;
            },
            showInfo(eventsList) {
                this.modalInfo = eventsList;
                this.showEventsModal = true;
            },
            disagree(item) {
                if (item.info.length) {
                    return item.info[item.info.length - 1].event.includes('Отклонено') ? 'red' : '#2F80ED';
                }
                return '#2F80ED';
            },
            openModalDisagreed(item = {}) {
                this.showDisagreedModal = true;
                this.currentItem = item;
            },
            closeModalDisagreed() {
                this.showDisagreedModal = false;
            },
            setDisagree(item, status) {
                let phrase = '';
                this.setStatus(item, status);
                if (status == '3') {
                    if (this.type === 'headofdepartment') {
                        phrase = 'Отклонено заведующим кафедрой: ';
                    }
                    if (this.type === 'ExecutiveSecretary') {
                        phrase = 'Отклонено руководителем ОПОП: '
                    }
                    item.info.push({
                        date: dayjs().format('DD.MM.YYYY HH:mm:ss'),
                        event: phrase + this.reasonDisagreed,
                    });
                }

            },
            setStatus(item, status) {
                const vm = this;
                vm.isLoader = true;
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {
                        let promises = ajax.call([
                            {
                                methodname: 'set_status',
                                args: {
                                    rpd_id: item.id,
                                    status: status, // -
                                    reason_disagreed: vm.reasonDisagreed,
                                    type: vm.type
                                }
                            }
                        ]);
                        promises[0].done((response) => {
                            item.status = response.status.toString();
                            if (vm.type === 'headofdepartment') {
                                item.isHODAgreed = true;
                            }

                            if (vm.type === 'ExecutiveSecretary') {
                                item.isOPOPAgreed = true;
                            }
                            vm.isLoader = false;
                        }).fail(function (ex) {
                            vm.isLoader = false;
                            notification.exception(ex);
                        });
                    });
            },
            chooseIsAdmin() {
                if (this.isAdmin) {
                    this.selectedModalTab = {id: 1, name: 'Разработчики', filter: 1};
                } else {
                    this.selectedModalTab = {id: 2, name: 'Соразработчики', filter: 2};
                }
            },
            selectDeveloper(developerID) {
                if (this.selectedDeveloperID) {
                    this.selectedDeveloperID = null;
                } else {
                    this.selectedDeveloperID = developerID;
                }
            },
            sendDevelopers(coDeveloper) {
                //if (coDeveloper?.coDevelopers.length) {
                const vm = this;
                let discipline = this.tableData.find(item => item.id === this.changeDisciplineID);
                discipline.developers = _.cloneDeep(this.changeDisciplineDevelopersObj);
                let sendObj = {};
                sendObj.developers = _.cloneDeep(this.changeDisciplineDevelopersObj);
                sendObj.rpd_id = discipline.id;
                require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

                    let promises = ajax.call(
                        [
                            {
                                methodname: 'set_developer_on_rpd_1c_post',
                                args: {
                                    JSON: JSON.stringify(sendObj)
                                }
                            }
                        ]
                    );
                    promises[0].done((response) => {
                        console.log(response);
                        discipline.developers.coDevelopers = response.coDevelopers;
                        vm.isLoader = false;
                    }).fail(function (ex) {
                        vm.isLoader = false;
                        notification.exception(ex);
                    });
                });
                // }
            },
            createFuckingFilter(nameFilter, dataFrom) {
                let iteratee = _.uniqBy(dataFrom, nameFilter);
                let returnArray = [];
                let i = 0;
                iteratee.forEach(el => {
                    let obj = {};
                    obj.id = i++;

                    obj.value = el[nameFilter];
                    returnArray.push(obj);
                });
                returnArray.sort((el, el2) =>
                    el2.value - el.value
                );
                return returnArray;
            },

            writeStatus(status) {
                switch (status) {
                    case '4':
                        return 'НЕ НАЗНАЧЕН РАЗРАБОТЧИК';
                        break;
                    case '3':
                        return 'Разработка';
                        break;
                    case '2':
                        return 'Согласование';
                        break;
                    case '1':
                        return 'Согласовано';
                        break;
                }
            },
            writeStatusColor(color) {
                switch (color) {
                    case '4':
                        return 'text-danger';
                    case '3':
                        return 'text-primary';
                    case '2':
                        return 'text-warning';
                    case '1':
                        return 'text-success';
                }
            },
            getFilters() {
                const vm = this;
                vm.isLoader = true;
                require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

                    var promises = ajax.call([
                        {
                            methodname: 'get_rpd_on_department_1c',
                            args: {
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.isLoader = false;

                        vm.tableData = _.uniqBy(response, 'id');
                        vm.tableData.forEach(item => {
                            item.developers.allDisciplineDevelopers = _.uniqBy(item.developers.allDisciplineDevelopers, 'id');
                        });
                        vm.isExecutiveSecretary = vm.tableData.every(item => {
                            return item.isExecutiveSecretary;
                        });
                        vm.years = vm.createFuckingFilter('year', vm.tableData);
                        vm.trainingLevels = vm.createFuckingFilter('trainingLevel', vm.tableData);
                        vm.educationPrograms = vm.createFuckingFilter('discipline', vm.tableData);
                        vm.directions = vm.createFuckingFilter('direction', vm.tableData);
                        vm.educationLevels = vm.createFuckingFilter('educationLevel', vm.tableData);
                        vm.profiles = vm.createFuckingFilter('profile', vm.tableData);
                        vm.educationPrograms.sort(function (a, b) {
                                if (a.value > b.value) {
                                    return 1;
                                }
                                if (a.value < b.value) {
                                    return -1;
                                }
                                return 0;

                            }
                        );

                    }).fail(function (ex) {
                        vm.isLoader = false;
                        notification.exception(ex);
                    });
                });
            },
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
            },
            setDeveloperInRPD(args, cloneCoDeveloper={}) {
                const vm = this;
                //vm.isLoader = true;
                let guid = '';

                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                      function (ajax, notification, LoadingIcon) {
                        // vm.isLoader = true;
                        let promises = ajax.call([
                            {
                                methodname: 'set_developer_on_rpd_1c',
                                args: args
                            }
                        ]);
                        promises[0].done((response) => {
                            vm.isLoader = false;
                            if (args.CRUD === 'add') {
                                cloneCoDeveloper.guid = response.guid;
                                vm.changeDisciplineDevelopersObj.coDevelopers.push(cloneCoDeveloper);
                                vm.loadingDevelopers = false;
                            }
                        }).fail(function (ex) {
                            vm.isLoader = false;
                            notification.exception(ex);
                        });
                    });

            },
            changeBlockCoDeveloper(coDeveloper) {

                let discipline = this.tableData.find(item => item.id === this.changeDisciplineID);
                let args = {
                    CRUD: 'update',
                    user_id: coDeveloper.id,
                    rpd_id: discipline.id,
                    is_primary_dev: '2',
                    blockControl: coDeveloper.blockControl,
                    guid: coDeveloper.guid
                };
                this.setDeveloperInRPD(args);
                discipline.developers = _.cloneDeep(this.changeDisciplineDevelopersObj);

            },
            async addCoDeveloper(coDeveloper, CRUD = 'add') {
                this.loadingDevelopers = true;
                let discipline = this.tableData.find(item => item.id === this.changeDisciplineID);
                const cloneCoDeveloper = {...coDeveloper};
                let args = {};
                if (Object.keys(this.selectedCoDeveloperToChange).length) {
                    const coDeveloperIndex = this.changeDisciplineDevelopersObj.coDevelopers
                        .findIndex(item => item.id === this.selectedCoDeveloperToChange.id);
                    const {blockControl} = this.selectedCoDeveloperToChange;
                    cloneCoDeveloper.blockControl = blockControl;
                    cloneCoDeveloper.guid = this.selectedCoDeveloperToChange.guid;
                    this.changeDisciplineDevelopersObj.coDevelopers.splice(coDeveloperIndex, 1, cloneCoDeveloper)
                    this.selectedCoDeveloperToChangeIDX = '';
                    args = {
                        CRUD: 'update',
                        user_id: coDeveloper.id,
                        rpd_id: discipline.id,
                        is_primary_dev: '2',
                        blockControl: this.selectedCoDeveloperToChange.blockControl,
                        guid: this.selectedCoDeveloperToChange.guid
                    };
                    this.setDeveloperInRPD(args);
                } else {
                    args = {
                        CRUD: CRUD,
                        user_id: coDeveloper.id,
                        rpd_id: discipline.id,
                        is_primary_dev: '2',
                        blockControl: coDeveloper.blockControl
                    };
                    this.setDeveloperInRPD(args, cloneCoDeveloper);
                   /* cloneCoDeveloper.guid = this.newGuid;
                    this.changeDisciplineDevelopersObj.coDevelopers.push(cloneCoDeveloper);*/
                }
            },
            coDeveloperToChange(coDeveloper, idx) {
                if (idx === this.selectedCoDeveloperToChangeIDX) {
                    this.selectedCoDeveloperToChange = {};
                    this.selectedCoDeveloperToChangeIDX = '';
                } else {
                    this.selectedCoDeveloperToChangeIDX = idx;
                    this.selectedCoDeveloperToChange = {...coDeveloper};
                }
            },
            deleteCoDeveloper(coDeveloper, idx, CRUD = 'delete') {
                if (coDeveloper.id === this.selectedCoDeveloperToChange.id) {
                    this.selectedCoDeveloperToChange = {}
                }
                this.changeDisciplineDevelopersObj.coDevelopers.splice(idx, 1);
                let discipline = this.tableData.find(item => item.id === this.changeDisciplineID);
                let args = {
                    CRUD: CRUD,
                    user_id: coDeveloper.id,
                    rpd_id: discipline.id,
                    is_primary_dev: '2',
                    blockControl: coDeveloper.blockControl
                };
                this.setDeveloperInRPD(args);
                discipline.developers = _.cloneDeep(this.changeDisciplineDevelopersObj);
            },

            selectMainDeveloper(newDeveloper) {
                if (this.selectedDeveloperID || newDeveloper.id) {
                    if (Object.keys(this.changeDisciplineDevelopersObj.mainDeveloper).length) {
                        const {id, user} = newDeveloper
                        const currentDeveloper = {...this.changeDisciplineDevelopersObj.mainDeveloper[0]}
                        currentDeveloper.id = id;
                        currentDeveloper.user = user;
                        this.changeDisciplineDevelopersObj.mainDeveloper.splice(0, 1, currentDeveloper);
                    }
                    this.selectedDeveloperID = null;
                }
                if (!this.selectedDeveloperID && !Object.keys(this.changeDisciplineDevelopersObj.mainDeveloper).length) {
                    this.changeDisciplineDevelopersObj.mainDeveloper.push(_.cloneDeep(newDeveloper))
                }
                let discipline = this.tableData.find(item => item.id === this.changeDisciplineID);
                discipline.developers = _.cloneDeep(this.changeDisciplineDevelopersObj);
                discipline.status = '3';
                const vm = this;
                require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {
                    vm.isLoader = true;
                    let args = {
                        user_id: newDeveloper.id,
                        rpd_id: discipline.id,
                        is_primary_dev: '1',
                        blockControl: newDeveloper.blockControl
                    };
                    let promises = ajax.call([
                        {
                            methodname: 'set_developer_on_rpd_1c',
                            args: args
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.isLoader = false;

                    }).fail(function (ex) {
                        vm.isLoader = false;
                        notification.exception(ex);
                    });
                });

            },
            editDevelopers(developers, disciplineID, mainDepartment, excluded) {
                this.isAdmin = mainDepartment;
                this.modalTabs
                this.hideCodev = excluded;
                this.chooseIsAdmin();
                this.showDeveloperModal = true;
                this.changeDisciplineID = disciplineID;
                this.changeDisciplineDevelopersObj = _.cloneDeep(developers);
                this.isDisciplineHasDeveloper = !!this.changeDisciplineDevelopersObj.mainDeveloper.length;
                if (this.isDisciplineHasDeveloper)
                    this.disciplineOldID = this.changeDisciplineDevelopersObj.mainDeveloper[0].id;
            }
            ,
            closeDevelopersModal() {
                this.showDeveloperModal = false;
                this.changeDisciplineID = null;
                this.changeDisciplineDevelopersObj = {};
                this.searchDevelopersField = '';
                this.searchCoDevelopersField = '';
                this.selectedCoDeveloperToChangeIDX = '';
                this.selectedCoDeveloperToChange = {};
            }
            ,
            saveDiscipline() {
                this.closeDevelopersModal();
            },
            closeDevelopersModal() {
                this.showDeveloperModal = false;
                this.changeDisciplineID = null;
                this.changeDisciplineDevelopersObj = {};
                this.searchDevelopersField = '';
                this.searchCoDevelopersField = '';
                this.selectedCoDeveloperToChangeIDX = '';
                this.selectedCoDeveloperToChange = {};
            },
            getStatusType(status) {
                switch (status) {
                    case '1':
                        return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#27AE60"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                        break
                    case '2':
                        return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#F2994A"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                        break
                    case '3':
                        return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#2F80ED"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                        break
                    case '4':
                        return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#EB5757"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                        break
                }
            }
        }
        ,
    })
;