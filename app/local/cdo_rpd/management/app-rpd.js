Vue.use(VueToast);
Vue.use(VueQuillEditor, {
    theme: 'snow',
    placeholder: 'Введите текст...',
});
Vue.component('vue-draggable-resizable', window.VueDraggableResizable);
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
Vue.directive('tooltip', VTooltip.VTooltip);
Vue.directive('close-popover', VTooltip.VClosePopover);

Vue.component('v-popover', VTooltip.VPopover);
Vue.component('multiselect', window.VueMultiselect.default);

Vue.use(VueToast);

window.Vue.use(VuejsDialog.main.default, {
    html: true,
    okText: 'Да',
    cancelText: 'Отмена',
});


const rpd = new Vue({
    el: '#rpd',
    components: {
        targetAndTasks,
        competences,
        themes,
        typesOfTasks,
        control,
        literature,
        mto
    },
    data: () => ({
        agreedStatus: {
            status: '4',
            text: 'Ответ от библиотеки'
        },
        isApproval: false,
        listValidation: {},
        remainingH: 1,
        literatureStatus: '2',
        guidO: 'fd0ee189-ffbd-11ed-85f5-00155d055200',
        guidZ: '8e9e6559-77e7-11e5-aaf6-00237dcf6128',
        guidOZ: '8e9e655b-77e7-11e5-aaf6-00237dcf6128',
        consolidateModules: [],
        currentGuid: '',
        modules: [],
        globalImportLoader: false,
        importedData: [],
        isLoader: true,
        isAdmin: true,
        currentUserID: '0',
        showUserBlock: '',
        polling: null,
        developers: [],
        optionsFilters: [
            {id: 1, name: 'ЦЕЛЬ И ЗАДАЧИ', filter: 'targetAndTasks'},
            {id: 2, name: 'КОМПЕТЕНЦИИ', filter: 'competences'},
            {id: 3, name: 'ТЕМЫ', filter: 'themes',},
            {id: 4, name: 'ВИДЫ ЗАДАНИЙ', filter: 'typesOfTasks'},
            {id: 5, name: 'СОДЕРЖАНИЕ ЗАДАНИЙ', filter: 'control'},
            {id: 6, name: 'ЛИТЕРАТУРА', filter: 'literature'},
            {id: 7, name: 'МТО', filter: 'mto'},
        ],
        selectedFilter: '',
        competences: [],
        dataTargetDiscipline: '',
        dataTasksDiscipline: '',
        forms: [],
        parts: [],
        selectedBooksList: {},
        INFO: {
            developers: {
                mainDeveloper: [
                    {
                        id: '',
                        blockControl: ''
                    }
                ]
            }
        },
        MTO: [],
        controls: [
            {
                enroleTypes: []
            }
        ],
        appraisalTools: [],
        controlsList: [],
        controlsList_: {},
        questionsList: [],
        questionsForAllThemes: [],
        isDeveloper: false,
        questionsForDiscipline: {},
        criteriaList: {},
        auditWork: '',
        outwork: '',
        globalImportModal: false,
        year: '',
        direction: '',
        profile: '',
        discipline: '',
        trainingLevel: '',
        educationLevel: '',
        years: [],
        directions: [],
        educationLevels: [],
        trainingLevels: [],
        disciplines: [],
        profiles: [],
        selectedArchiveDiscipline: {},
        selectedImport: {
            targetAndTasks: false,
            knowAbleOwn: false,
            themes: false,
            hours: false,
            control: false,
            distribution: false,
            inventory: false,
            software: false,
            literature: false,
        },
        itemsPerPage: null,
        isActivePaginationSelect: false,
        listPerPage: [4, 8, 12, 'Все'],
        currentPage: 0,
        importedInfoOfRPD: {},
        rpd_name: "",
        chosenDev: {},
        coDev: false,
        fosBody: [],
        listTextValidation: {
            target: "Не заполнены цели (требуется более 50 символов)",
            task: "Не заполнены задачи (требуется более 50 символов)",
            competence: "Не заполнены компетенции (требуется более 20 символов на пункты знать, уметь, владеть)",
            themes: "Не распределены все часы по темам",
            edu: "Не запонлены образовательные технологии для аудиторной работы и самостоятельной работы (требуется более 20 символов на каждую) ",
            test: "Не внесен ни один вопрос по оценочному виду тест",
            //bodyTasks: "Не заполнены критерии и шкала оценивания для каждого из оценочных средства",
            literature: "Литература не согласована"
        },
        listFOSValidation: {},
        listFOSTextValidation: {}
    }),
    created() {
        this.getRPDInfo();
        this.saveRpd();
        this.itemsPerPage = this.listPerPage[0];
        this.checkApprovalForRPD();
    },
    beforeDestroy() {
        clearInterval(this.polling)
    },
    computed: {
        disableControl() {
            return !(this.selectedImport.control && this.selectedImport.themes);
        },
        disableHours() {
            return !this.selectedImport.themes;
        },
        filteredArchive() {

            return this.importedData
                .filter(item => {
                    if (this.year !== null && this.year.value)
                        return item.year === this.year.value
                    else return true
                })
                .filter(item => {
                    if (this.direction !== null && this.direction.value)
                        return item.direction === this.direction.value
                    else return true
                })
                .filter(item => {
                    if (this.trainingLevel !== null && this.trainingLevel.value)
                        return item.trainingLevel === this.trainingLevel.value
                    else return true
                })
                .filter(item => {
                    if (this.profile !== null && this.profile.value)
                        return item.profile === this.profile.value
                    else return true
                })
                .filter(item => {
                    if (this.educationLevel !== null && this.educationLevel.value)
                        return item.educationLevel === this.educationLevel.value
                    else return true
                })
                .filter(item => {
                    if (this.discipline !== null && this.discipline.value)
                        return item.discipline == this.discipline.value
                    else return true
                })

        },
        currentUserDeveloper() {

        },
        paginatedArchive() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.filteredArchive.length : this.itemsPerPage;
            const start = this.currentPage * itemsPerPage,
                end = start + itemsPerPage;
            return this.filteredArchive.slice(start, end);
        },
        pageCount() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.filteredArchive.length : this.itemsPerPage;
            let l = this.filteredArchive.length,
                s = itemsPerPage;
            return Math.ceil(l / s);
        },
        fromPage() {
            return this.currentPage * this.itemsPerPage || 1;
        },
        toPage() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.filteredArchive.length : this.itemsPerPage;
            return this.currentPage * itemsPerPage + itemsPerPage
        },
        accessAdminFilters() {
            if (!this.isAdmin) {
                return this.optionsFilters.map((item) => {
                    if ([1, 2].includes(item.id)) {
                        return {
                            ...item,
                            disabled: true
                        }
                    } else {
                        return item
                    }
                })
            } else return this.optionsFilters

        },
        validationRPD(v) {

            let validationList = {
                target: false,
                task: false,
                competence: false,
                themes: false,
                edu: false,
                test: false,
                //bodyTasks: false,
                literature: false
            };
            this.listValidation = validationList;
            let validator = [];
            if (this.dataTargetDiscipline.length > 50) {
                validationList.target = true;
                validator.push(true);
            } else {
                validator.push(true);
            }
            if (this.dataTasksDiscipline.length > 50) {
                validationList.task = true;
                validator.push(true);
            } else {
                validator.push(true);
            }
            let allCompetence = [];
            this.competences.forEach(competence => {
                let r_beAbleTo = competence.requirement.beAbleTo.length > 20;
                let r_know = competence.requirement.know.length > 20;
                let r_own = competence.requirement.own.length > 20;

                if (r_beAbleTo && r_own && r_know) {
                    allCompetence.push(true);
                }
            });
            validationList.competence = allCompetence.length === this.competences.length;
            validator.push(validationList.competence);
            let all_o = 0;
            let all_z = 0;
            let all_oz = 0;
            let form_o = ['lection', 'practice', 'lab', 'outwork', 'interactive', 'practicePrepare'];
            let form_z = ['lection_za', 'practice_za', 'lab_za', 'outwork_za', 'interactive_za', 'practicePrepare_za'];
            let form_oz = ['lection_oza', 'practice_oza', 'lab_oza', 'outwork_oza', 'interactive_oza', 'practicePrepare_oza'];

            this.parts.forEach(category => {
                category.data.forEach(themes => {
                    form_o.forEach(form => {
                        all_o += parseFloat(themes[form]);
                    });
                    form_z.forEach(form => {
                        all_z += parseFloat(themes[form]);
                    });
                    form_oz.forEach(form => {
                        all_oz += parseFloat(themes[form]);
                    });
                });
            });
            let need_o = 0;
            let need_z = 0;
            let need_oz = 0;
            this.forms.forEach(form => {
                form.load.forEach(load => {
                    switch (form.guidform) {
                        case this.guidO:
                            need_o += load.value;
                            break;
                        case this.guidZ:
                            need_z += load.value;
                            break;
                        case this.guidOZ:
                            need_oz += load.value;
                            break;
                    }
                })
            });
            validationList.themes = all_o === need_o && need_z === all_z && need_oz === all_oz;
            validator.push(validationList.themes);
            validationList.edu = this.auditWork.length > 20 && this.outwork.length > 20;
            validator.push(validationList.edu);
            let addedTest = this.controlsList.filter(item => {
                return item.code === 'tests';
            }).length;
            validationList.test = addedTest > 0;
            validator.push(validationList.test);

            let fosCriterias = [];
            this.fosBody = [];
            for (item in this.criteriaList) {
                this.controlsList.forEach(controlItem => {
                    if (controlItem.code === item) {
                        fosCriterias.push(this.criteriaList[item].length > 0);
                        this.fosBody.push({
                            valid: this.criteriaList[item].length > 0,
                            fos: item,
                            name: controlItem.name
                        })
                    }
                })
            }

            let fosCriteriasFill = fosCriterias.every(value => value === true);
            let literatureMainChosen = this.selectedBooksList.mainSelected.length > 1;
            let control = [];
            let listFOSValidation = [];
            this.controlsList.forEach(item => {
                let controlFill = [];

                if (item.code === 'course_work' || item.code === 'lab_work') {
                    if (item.code === 'course_work') {
                        let c = this.questionsForAllThemes.filter(value => value.code === 'course_work');
                        if (!c.length) {
                            listFOSValidation.push(
                                {
                                    fos: item.code,
                                    valid: false,
                                    name: item.name
                                });
                        }
                    }

                    this.questionsForAllThemes.forEach(questionItem => {
                        if (questionItem.code === item.code) {
                            let questionFill = 0; let tasksFill = 0; let themesFill;
                            if (questionItem.competences.hasOwnProperty('questions')) {
                                 questionFill = questionItem.competences.questions.length > 0;
                            }
                            if (questionItem.competences.hasOwnProperty('tasks')) {
                                tasksFill = questionItem.competences.tasks.length > 0;
                            }
                            if (questionItem.competences.hasOwnProperty('themes')) {
                                themesFill = questionItem.competences.themes.length > 0;
                            }
                            if (item.code === 'lab_work') {
                                control.push(questionFill && themesFill);
                                listFOSValidation.push(
                                    {
                                        fos: item.code,
                                        valid: questionFill && themesFill,
                                        name: item.name
                                    });
                            } else {
                                control.push(questionFill && themesFill && tasksFill);
                                listFOSValidation.push(
                                    {
                                        fos: item.code,
                                        valid: questionFill && themesFill && tasksFill,
                                        name: item.name
                                    });
                            }
                            ;
                        }
                    });
                } else {

                    this.parts.forEach(category => {
                        category.data.forEach(theme => {
                            if (!!theme.data[item.code]) {
                                let moreThanOne = theme.data[item.code].length > 0;
                                if (moreThanOne) {
                                    controlFill.push(moreThanOne);
                                }

                            }
                        });
                    });
                    listFOSValidation.push(
                        {
                            fos: item.code,
                            valid: controlFill.length > 0,
                            name: item.name
                        }
                    )
                    control.push(controlFill.length > 0);
                }
            });
            this.listFOSValidation = listFOSValidation;
            let fosValidation = this.listFOSValidation.every(value => value.valid === true);
            // let controlFill = control.every(value => value === true);
            let fosBodyValidation = this.fosBody.every(value => value.valid === true);
            validator.push(fosValidation);
            validator.push(fosBodyValidation);
            //console.log(controlFill, fosCriteriasFill);
            //validationList.bodyTasks = controlFill && fosCriteriasFill;
            //validator.push(validationList.bodyTasks);
            validationList.literature = this.INFO.librarianStatus === '1' && literatureMainChosen;
            validator.push(validationList.literature);
            // console.log(validationList);

            return validator.every(value => value === true);
        },
    },
    methods: {
        unCheck() {
            if (!this.selectedImport.themes || !this.selectedImport.control) {
                this.selectedImport.hours = false;
                this.selectedImport.distribution = false;
            }
        },
        setStatusLiterature(status) {
            this.sendToApproval(status);
        },
        async checkApprovalForRPD() {
            const vm = this;
            this.loading = true;
            const rpd_id = this.findGetParameter('rpd_id');
            const user_id = this.currentUserID.id;
            require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {
                let promises = ajax.call([
                    {
                        methodname: 'get_literature_for_approve',
                        args: {
                            user_id: user_id,
                            rpd_id: rpd_id,
                            guid: vm.currentGuid == null ? "" : vm.currentGuid,
                        }
                    }
                ]);
                promises[0].done((response) => {
                    vm.isLoader = false;
                    if (response.result)  {
                        vm.literatureStatus = response.status;
                        vm.agreedStatus.status = response.status;
                    }

                    vm.loading = false;
                }).fail(function (ex) {
                    vm.isLoader = false;
                    notification.exception(ex);
                    vm.loading = false;
                });
            });
        },
        getStatusApproval(status) {
            console.log(status);
        },
        sendToApproval(status = "4") {
            //console.log(this.mainLiteratureSelected, this.additionalLiteratureSelected, this.methodicalLiteratureSelected);
            const selectedBooks = {
                mainSelected: this.selectedBooksList.mainSelected,
                additionalSelected: this.selectedBooksList.additionalSelected,
                methodicalSelected: this.selectedBooksList.methodicalSelected,
            };

            const vm = this;
            const rpd_id = this.findGetParameter('rpd_id');
            // const guid = this.findGetParameter('guid');
            const guid = this.currentGuid == null ? "" : this.currentGuid;
            //  let statusToSend = this.literatureStatus === "1" ? "1" : "2";
            let structToSend = {
                literature: selectedBooks,
                rpd_id: rpd_id,
                user_id: vm.currentUserID.id,
                blockControl: vm.showUserBlock,
                guid: guid.length > 0 ? guid : "",
                status: status
            }

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
                        vm.agreedStatus.status = response.status;
                        // this.agreedStatus.text = response.comment;

                    }).fail(function (ex) {
                        notification.exception(ex);
                    });
                });
        },
        writeStatus(status) {
            switch (status) {
                case '3' :
                case 3:
                    return 'Разработка';
                    break;
                case '2':
                case 2:
                    return 'Согласование';
                    break;
                case '1':
                case 1:
                    return 'Согласовано';
                    break;
            }
        },
        setStatus() {
            if (!this.validationRPD) {
                let messageValidation = '';

                for (const [key, value] of Object.entries(this.listValidation)) {
                    if (!value) // если валидация не прошла
                        messageValidation += this.listTextValidation[key] + '<br>';
                }

                this.listFOSValidation.forEach(item => {
                    if (!item.valid)
                        messageValidation += "В разделe 'содержание заданий' не заполнены задания для блока " + item.name + "<br>";
                });

                this.fosBody.forEach(item => {
                    if (!item.valid)
                        messageValidation += "В разделe 'содержание заданий' не заполнены критерии и шкалы оценивания  для блока " + item.name + "<br>";
                });

                this.$toast.open({
                    message: messageValidation,
                    type: "warning",
                    duration: 6000,
                    dismissible: true
                });

            } else {
                const vm = this;
                vm.isLoader = true;
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {

                        const rpd_id = vm.findGetParameter('rpd_id');
                        var promises = ajax.call([
                            {
                                methodname: 'set_status',
                                args: {
                                    rpd_id: rpd_id,
                                    status: 2 // - На согласование
                                }
                            }
                        ]);
                        promises[0].done((response) => {
                            vm.INFO.status = response.status;
                            vm.isLoader = false;
                        }).fail(function (ex) {
                            vm.isLoader = false;
                            notification.exception(ex);
                        });
                    });
            }

        },
        statusLoader(status) {
            this.isLoader = status;
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
        async getMyRPD() {
            const vm = this;
            // vm.isLoader = true;
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    //notification.init(1);

                    let promises = ajax.call([
                        {
                            methodname: 'get_rpd_list_by_user_id_from_1c',
                            args: {}
                        }
                    ]);
                    promises[0].done((response) => {

                        vm.importedData = response.filter(el => el.id !== vm.INFO.id);
                        vm.years = vm.createFuckingFilter('year', vm.importedData);
                        vm.trainingLevels = vm.createFuckingFilter('trainingLevel', vm.importedData);
                        vm.disciplines = vm.createFuckingFilter('discipline', vm.importedData);
                        vm.directions = vm.createFuckingFilter('direction', vm.importedData);
                        vm.educationLevels = vm.createFuckingFilter('educationLevel', vm.importedData);
                        vm.profiles = vm.createFuckingFilter('profile', vm.importedData);
                        vm.globalImportLoader = false;
                    }).fail(function (ex) {
                        //vm.isLoader = false;
                        notification.exception(ex);
                    });
                });
        },
        handleImportQuestionList(data) {
            const {tab, questions} = data;
            const getControlItem = this.questionsForAllThemes.findIndex(item => item.code === tab);
            if (getControlItem === -1) {
                this.questionsForAllThemes.push({
                    code: tab,
                    questions: questions
                });
            } else {
                this.questionsForAllThemes[getControlItem].questions = this.questionsForAllThemes[getControlItem].questions.concat(questions);
                this.$toast.open({
                    message: `Успешно импортировано!`,
                    type: "success",
                    duration: 5000,
                    dismissible: true
                });
            }
            //тут надо отправить что-то, например
            const selectedArchiveOptions = Object.entries(this.selectedImport).filter(item => item.includes(true));

            const obj = {
                archiveDiscipline: this.selectedArchiveDiscipline,
                toImport: Object.fromEntries(selectedArchiveOptions)
            }

        },
        async openGlobalImportModal() {
            // await axios.get()
            this.globalImportLoader = true;
            this.getMyRPD();
            this.globalImportModal = true;

        },
        importFromArchive() {
            if (!Object.values(this.selectedArchiveDiscipline).length) {
                this.$toast.open({
                    message: `Пожалуйста, выберите дисциплину`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            const arrSelectedImport = Object.values(this.selectedImport).every(item => item === false);
            if (arrSelectedImport) {
                this.$toast.open({
                    message: `Пожалуйста, выберите хотя бы один элемент для импорта`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            this.getRPDInfoForImport();


        },
        selectPage(page) {
            this.currentPage = 0;
            this.itemsPerPage = page;
            this.isActivePaginationSelect = false;
        },
        selectArchiveItem(item) {

            this.selectedArchiveDiscipline = _.cloneDeep(item);

        },
        handleAddImportedList(data) {
            console.log(data)

            const {items, type, tab} = data;

            this.questionsForDiscipline[tab][type] = this.questionsForDiscipline[tab][type].concat(items);
        },
        handleOnCriteriaContentChange(data) {
            const {text, tab} = data;
            this.criteriaList[tab] = text;
        },
        handleDeleteFromUnallocatedPart(data) {
            const {item, tab, type} = data;
            this.questionsForDiscipline[tab][type] = this.questionsForDiscipline[tab][type]
                .filter(curItem => curItem.id !== item.id);
        },
        handleAddFromUnallocatedPart(data) {
            const {tab, item, type} = data;
            const getControlItem = this.questionsForAllThemes.findIndex(item => item.code === tab);
            if (getControlItem === -1) {
                this.questionsForAllThemes.push({
                    code: tab,
                    competences: {
                        questions: [],
                        tasks: [],
                        themes: [],
                    }
                });
                const getControlItem = this.questionsForAllThemes.findIndex(item => item.code === tab);
                this.questionsForAllThemes[getControlItem].competences[type].push(_.cloneDeep(item));
            } else {
                const findItemIdx = this.questionsForAllThemes[getControlItem].competences[type]
                    .findIndex(curItem => curItem.id === item.id);
                if (findItemIdx === -1)
                    this.questionsForAllThemes[getControlItem].competences[type].push(_.cloneDeep(item));
                else
                    this.questionsForAllThemes[getControlItem].competences[type].splice(findItemIdx, 1, _.cloneDeep(item));
            }

            const findItemIdx = this.questionsForDiscipline[tab][type].findIndex(curItem => curItem.id === item.id);

            if (findItemIdx === -1)
                this.questionsForDiscipline[tab][type].push(_.cloneDeep(item));
            else
                this.questionsForDiscipline[tab][type].splice(findItemIdx, 1, _.cloneDeep(item));

        },
        handleDeleteCompetenceQuestion(data) {
            const {competence, questionId, tab} = data;
            const indexControl = this.questionsForAllThemes.findIndex(item => item.code === tab);
            const questionsListInControl = this.questionsForAllThemes[indexControl].questions;
            const findIndexTheme = questionsListInControl.findIndex(item => item.id === questionId);

            this.questionsForAllThemes[indexControl].questions[findIndexTheme].selectedValue =
                this.questionsForAllThemes[indexControl].questions[findIndexTheme].selectedValue.filter(item => item.id !== competence.id)
        },
        handleSelectCompetenceQuestion(data) {
            const {competence, questionId, tab} = data;
            const indexControl = this.questionsForAllThemes.findIndex(item => item.code === tab);
            const questionsListInControl = this.questionsForAllThemes[indexControl].questions;
            const findIndexTheme = questionsListInControl.findIndex(item => item.id === questionId);

            this.questionsForAllThemes[indexControl].questions[findIndexTheme].selectedValue.push(competence);
        },
        handleOnEditorChange(data) {
            const {questionId, tab, text} = data;
            const indexControl = this.questionsForAllThemes.findIndex(item => item.code === tab);
            const questionsListInControl = this.questionsForAllThemes[indexControl].questions;
            const findIndexTheme = questionsListInControl.findIndex(item => item.id === questionId);

            this.questionsForAllThemes[indexControl].questions[findIndexTheme].questionDescription = text;
        },
        handleOnAnswerChange(data) {
            const {questionId, tab, text} = data;
            const indexControl = this.questionsForAllThemes.findIndex(item => item.code === tab);
            const questionsListInControl = this.questionsForAllThemes[indexControl].questions;
            const findIndexTheme = questionsListInControl.findIndex(item => item.id === questionId);

            this.questionsForAllThemes[indexControl].questions[findIndexTheme].questionAnswers = text;
        },
        handleRemoveQuestionFromList(data) {
            const {question, tab} = data;
            const indexControl = this.questionsForAllThemes.findIndex(item => item.code === tab);
            const questionsListInControl = this.questionsForAllThemes[indexControl].questions;
            this.questionsForAllThemes[indexControl].questions = questionsListInControl.filter(item => item.id !== question)
        },
        handleAddQuestionToList(data) {
            const {tab, question} = data;
            const getControlItem = this.questionsForAllThemes.findIndex(item => item.code === tab);
            if (getControlItem === -1) {
                this.questionsForAllThemes.push({
                    code: tab,
                    questions: [_.cloneDeep(question)]
                })
            } else {
                this.questionsForAllThemes[getControlItem].questions.push(_.cloneDeep(question));
            }
        },
        changeMto(MTO) {
            this.MTO = MTO;
        },
        saveSelectedBooks(objBooks) {
            this.selectedBooksList = objBooks;
        },
        onTargetDiscipline(e) {
            this.dataTargetDiscipline = e;
        },
        onTasksDiscipline(e) {
            this.dataTasksDiscipline = e;
        },
        saveRPDAll() {
            //this.$refs.litera.sendToApproval();
            if (!this.coDev) {

                const discipline = this.findGetParameter('discipline');
                const rpd_id = this.findGetParameter('rpd_id');
                const vm = this;
                require(['core/ajax', 'core/notification', 'core/loadingicon', 'jquery'], function (ajax, notification, LoadingIcon, $) {
                    vm.$toast.open({
                        message: 'Сохранение...',
                        type: "warning",
                        duration: 5000,
                        dismissible: true
                    });
                    let clearControlList = _.cloneDeep(vm.controlsList);
                    clearControlList.forEach(el => (
                            delete el.$isDisabled
                        )
                    );
                    let rpd_all = {
                        guid_module: vm.currentGuid,
                        user_id: vm.currentUserID.id,

                        title: {
                            rpd_id: rpd_id,
                            discipline: discipline,
                            validaton_passed: vm.validationRPD
                        },
                        part1: {
                            target: vm.dataTargetDiscipline,
                            taskfordisc: vm.dataTasksDiscipline,
                        },
                        part3: {
                            list: vm.competences
                        },
                        part11: {
                            books: vm.selectedBooksList
                        },
                        MTO: vm.MTO,
                        parts: vm.parts,
                        moduleControl: vm.showUserBlock,
                        controlsList: clearControlList,
                        questionsForAllThemes: vm.questionsForAllThemes,
                        questionsForDiscipline: vm.questionsForDiscipline,
                        auditWork: vm.auditWork,
                        outwork: vm.outwork,
                        criteriaList: vm.criteriaList
                    };
                    var promises = ajax.call([
                        {
                            methodname: 'save_rpd_to_1c',
                            args: {
                                JSON: JSON.stringify(rpd_all)
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        console.log(response);
                        vm.$toast.open({
                            message: 'Успешно!',
                            type: "success",
                            duration: 5000,
                            dismissible: true
                        });
                        vm.INFO.status = response.status;
                    }).fail(function (ex) {
                        notification.exception(ex);
                    });
                });

            }
        },
        saveRpd() {
            const vm = this;
            this.polling = setInterval(() => {
                vm.saveRPDAll();
            }, 130000)
        },
        getRPDInfoForImport() {
            const vm = this;
            if (vm.currentUserID.id.length) {
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {
                        var promises = ajax.call(
                            [
                                {
                                    methodname: 'get_competencies_for_rpd',
                                    args: {
                                        edu_plan: vm.selectedArchiveDiscipline.edu_plan.id,
                                        discipline: vm.selectedArchiveDiscipline.discipline_code,
                                        rpd_id: vm.selectedArchiveDiscipline.id,
                                        user_id: vm.currentUserID.id
                                    }
                                }
                            ], false
                        );
                        promises[0].done((response) => {
                            vm.isLoader = false;
                            let successTree = [];
                            let negativeImport = [];
                            vm.importedInfoOfRPD = response;

                            // Литература импоритуется 1 к 1 независимо от стадии согласования +
                            if (vm.selectedImport.literature) {
                                vm.selectedBooksList = response.books;
                                vm.sendToApproval();
                                successTree.push("Литература");
                            }

                            // Цель и задачи переносятся 1 к 1 +
                            if (vm.selectedImport.targetAndTasks) {
                                vm.dataTargetDiscipline = response.part1.target;
                                vm.dataTasksDiscipline = response.part1.taskfordisc;
                            }

                            // Компетенции переносятся только при условии совпадения комптенций в РПД +
                            if (vm.selectedImport.knowAbleOwn) {
                                let intersectCompetences = _.intersectionBy(response.competencies, vm.competences, "id");
                                vm.competences.forEach((competence, index) => {
                                    intersectCompetences.forEach(iCompetence => {
                                        if (competence.id === iCompetence.id) {
                                            vm.competences[index] = iCompetence;
                                        }
                                    })
                                });

                                successTree.push("Компетенции");
                            }
                            // МТО +
                            if (vm.selectedImport.inventory || vm.selectedImport.software) {
                                //заполнено ли вообще мто
                                if (vm.MTO.length === 0) {
                                    vm.MTO = response.MTO;
                                } else {
                                    // Если в текущем РПД отсутвует импортируемый корпус - просто добавляем его полностью.
                                    let currentBuilding = _.differenceBy(response.MTO, vm.MTO, "uid");
                                    if (currentBuilding.length !== 0) {
                                        if (!vm.selectedImport.inventory && !vm.selectedImport.software) {
                                            if (vm.selectedImport.inventory) {
                                                currentBuilding.forEach(el => {
                                                    el.auditorium.forEach(element => {
                                                        element.software = [];
                                                    });
                                                });

                                            }
                                            if (vm.selectedImport.software) {
                                                currentBuilding.forEach(el => {
                                                    el.auditorium.forEach(element => {
                                                        element.inventory = [];
                                                    });
                                                });
                                            }
                                        }
                                        vm.MTO = vm.MTO.concat(currentBuilding);
                                    }

                                    //если корпус имеется
                                    let sameBuilding = _.intersectionBy(response.MTO, vm.MTO, "uid");
                                    if (sameBuilding.length === 0) {

                                    } else {
                                        sameBuilding.forEach(auditorium => {
                                            let currentBuildingIteratee = vm.MTO.filter(el => el.uid === auditorium.uid);
                                            let currentBuildingIterateeIndex = vm.MTO.findIndex(el => el.uid === auditorium.uid);
                                            // разные аудитории
                                            let differentAuditorium = _.differenceBy(auditorium.auditorium, currentBuildingIteratee[0].auditorium, "uid");
                                            if (differentAuditorium.length !== 0) {
                                                if (!vm.selectedImport.inventory && !vm.selectedImport.software) {
                                                    if (vm.selectedImport.inventory) {
                                                        differentAuditorium.forEach(el => {
                                                            el.software = [];
                                                        });
                                                    }
                                                    if (vm.selectedImport.software) {
                                                        differentAuditorium.forEach(el => {
                                                            el.inventory = [];
                                                        });
                                                    }
                                                }
                                                vm.MTO[currentBuildingIterateeIndex].auditorium = vm.MTO[currentBuildingIterateeIndex].auditorium.concat(differentAuditorium);
                                            }
                                            // одинаковые аудитории
                                            let sameAuditory = _.intersectionBy(auditorium.auditorium, currentBuildingIteratee[0].auditorium, "uid");
                                            if (sameAuditory.length === 0) {

                                            } else {
                                                sameAuditory.forEach(auditoriumIteratee => {

                                                    let currentAuditory = vm.MTO[currentBuildingIterateeIndex].auditorium.filter(el => el.uid === auditoriumIteratee.uid);
                                                    let currentAuditoryIndex = vm.MTO[currentBuildingIterateeIndex].auditorium.findIndex(el => el.uid === auditoriumIteratee.uid);

                                                    if (vm.selectedImport.inventory) {
                                                        let inventoryMerge = _.differenceBy(auditoriumIteratee.inventory, currentAuditory[0].inventory, "uid");
                                                        vm.MTO[currentBuildingIterateeIndex].auditorium[currentAuditoryIndex].inventory = vm.MTO[currentBuildingIterateeIndex].auditorium[currentAuditoryIndex].inventory.concat(inventoryMerge);
                                                    }
                                                    if (vm.selectedImport.software) {
                                                        let softwareMerge = _.differenceBy(auditoriumIteratee.software, currentAuditory[0].software, "uid");
                                                        vm.MTO[currentBuildingIterateeIndex].auditorium[currentAuditoryIndex].software = vm.MTO[currentBuildingIterateeIndex].auditorium[currentAuditoryIndex].software.concat(softwareMerge);
                                                    }
                                                });

                                            }

                                        })

                                    }
                                }
                                successTree.push("МТО и ПО");
                            }

                            //Виды заданий?
                            if (vm.selectedImport.control) {

                                let requiredControl = []; // Обязательные ФОС для каждого РПД свои
                                vm.controls.forEach(item => {
                                    requiredControl.push(item.enroleTypes.filter(el => el.required === true));
                                });
                                requiredControl = (_.flatten(requiredControl));

                                let requiredControlImport = [];
                                response.controls.forEach(item => {
                                    requiredControlImport.push(item.enroleTypes.filter(el => el.required === true));
                                });
                                requiredControlImport = (_.flatten(requiredControlImport)); // обязательные фос в ипортируемом РПД

                                // обязательный контроль в импорте который имеем право импортировать
                                let requiredControlToImport = _.intersectionBy(requiredControlImport, requiredControl, "code");
                                // необязательные выбранные ФОС в импортируемом РПД
                                let notRequiredControlImport = response.controlsList.filter(el => el.required === false);

                                // добавляем все необязательные ФОС если они еще не добавлены
                                notRequiredControlImport.forEach(item => {
                                    let alreadyChoose = vm.controlsList.filter(i => item.code === i.code);
                                    if (alreadyChoose.length === 0) {
                                        vm.controlsList_ = item; // буферная зона для выставления
                                    }

                                });

                                // формируем массивы с ключами обязательным и нет
                                const keyCode = requiredControlToImport.map(item => item.code);
                                const keyCodeNot = notRequiredControlImport.map(item => item.code);

                                // ищем совпадения по ключам в импортируемом списке вопросов для шаблонов
                                const questionsForAllThemesImport = response.questionsForAllThemes.filter(el => (keyCode.includes(el.code) || keyCodeNot.includes(el.code)));

                                // устанавливаем какие компетенции совпадают в двух РПД
                                let sameCompetences = _.intersectionBy(vm.competences, response.competencies, "id");

                                // работаем с данными по шаблонам №3 - полностью перезатираем совпадения
                                _.forOwn(response.questionsForDiscipline, (item, key) => {
                                    if (vm.questionsForDiscipline.hasOwnProperty(key)) {
                                        response.questionsForDiscipline[key].questions.forEach(iteratee => {
                                            iteratee.competences = _.intersectionBy(iteratee.competences, sameCompetences, "id");
                                        });

                                        vm.questionsForDiscipline[key] = response.questionsForDiscipline[key];

                                    }
                                });

                                // обходим весь массив вопросов по кжадому из ФОС
                                try {
                                    questionsForAllThemesImport.forEach(el => {

                                        let findControl = vm.questionsForAllThemes.findIndex(item => (el.code === item.code)); // получаем индекс итерируемого контроля
                                        if (findControl >= 0) {
                                            el.questions.forEach(q => {
                                                // делаем пересечение компетенций для полного или частичного перегруза их
                                                let sameCOmpetenecesForTemplates = _.intersectionBy(q.selectedValue, sameCompetences, "id");
                                                q.selectedValue = sameCOmpetenecesForTemplates;
                                            });

                                            vm.questionsForAllThemes[findControl] = el;
                                        }
                                    });
                                } catch (e) {
                                    vm.$toast.open({
                                        message: `Ошибка при выгрузке вопросов по каждому ФОС`,
                                        type: "danger",
                                        duration: 5000,
                                        dismissible: true
                                    });
                                }

                            }

                            //Темы +
                            if (vm.selectedImport.themes) {
                                let sameHoursOnO = false;
                                let sameHoursOnZA = false;
                                let sameHoursOnOZA = false;
                                //Часы
                                if (vm.selectedImport.hours) {
                                    let formsHaveDifferentHours = true;
                                    let sameForms = _.intersectionBy(response.forms, vm.forms, 'guidform');
                                    sameForms.forEach(form => {
                                        let currentForm = vm.forms.filter(el => el.guidform === form.guidform);

                                        if (currentForm[0].load[0].value == form.load[0].value &&
                                            currentForm[0].load[1].value == form.load[1].value &&
                                            currentForm[0].load[2].value == form.load[2].value &&
                                            currentForm[0].load[3].value == form.load[3].value &&
                                            currentForm[0].load[4].value == form.load[4].value &&
                                            currentForm[0].load[5].value == form.load[5].value) {
                                            // formsHaveDifferentHours = false;
                                            switch (form.guidform) {
                                                case vm.guidO:
                                                    sameHoursOnO = true;
                                                    break
                                                case vm.guidZ:
                                                    sameHoursOnZA = true;
                                                    break
                                                case vm.guidOZ:
                                                    sameHoursOnOZA = true;
                                                    break
                                            }
                                        } else {
                                            negativeImport.push("Часы не могут быть перенесены ");
                                        }
                                    });
                                    //console.log(sameForms);
                                }

                                let cleanThemes = [];
                                cleanThemes = _.cloneDeep(response.parts);
                                cleanThemes.forEach(themes => {
                                    themes.data.forEach(clonedTheme => {
                                        // console.log(clonedTheme);
                                        clonedTheme.id = uuidv4();
                                        if (!vm.selectedImport.distribution && !vm.selectedImport.control) {
                                            clonedTheme.data = [];
                                        }
                                        if (!sameHoursOnO) {
                                            clonedTheme.lection = '0';
                                            clonedTheme.practice = '0';
                                            clonedTheme.lab = '0';
                                            clonedTheme.outwork = '0';
                                            clonedTheme.interactive = '0';
                                            clonedTheme.practicePrepare = '0';
                                        }
                                        if (!sameHoursOnZA) {
                                            clonedTheme.lection_za = '0';
                                            clonedTheme.practice_za = '0';
                                            clonedTheme.lab_za = '0';
                                            clonedTheme.outwork_za = '0';
                                            clonedTheme.interactive_za = '0';
                                            clonedTheme.practicePrepare_za = '0';
                                        }
                                        if (!sameHoursOnOZA) {
                                            clonedTheme.lection_oza = '0';
                                            clonedTheme.practice_oza = '0';
                                            clonedTheme.lab_oza = '0';
                                            clonedTheme.outwork_oza = '0';
                                            clonedTheme.interactive_oza = '0';
                                            clonedTheme.practicePrepare_oza = '0';

                                        }
                                    })
                                });
                                vm.parts = (cleanThemes);

                                successTree.push("Темы");

                            }

                            //Контроль
                            if (vm.selectedImport.control) {
                                let cl = _.uniqBy(response.controlsList, "code");
                                cl = cl.filter(item => {
                                    return !item.required;
                                });
                                vm.controlsList_ = cl;
                                //vm.$refs.tot.insertControlsList(cl);
                                vm.questionsList = response.questionsList;
                                vm.questionsForAllThemes = response.questionsForAllThemes;
                                if (response.questionsForDiscipline.length === 0) {
                                    vm.questionsForDiscipline = {};
                                } else vm.questionsForDiscipline = response.questionsForDiscipline;
                                vm.auditWork = response.auditWork;
                                vm.outwork = response.outwork;
                                if (response.criteriaList.length === 0) {
                                    vm.criteriaList = {};
                                } else
                                    vm.criteriaList = response.criteriaList ?? {};

                                successTree.push("Контроль");
                            }

                            vm.$toast.open({
                                message: `Импорт успешно осуществлен: ` + successTree.toString(),
                                type: "success",
                                duration: 5000,
                                dismissible: true
                            });
                            /*if (negativeImport.length) {
                                vm.$toast.open({
                                    message: `Импорт успешно осуществлен: ` + negativeImport.toString(),
                                    type: "success",
                                    duration: 5000,
                                    dismissible: true
                                });
                            }*/
                            if (vm.selectedImport.control) {
                                vm.saveRPDAll();
                                //location.reload();
                            }
                        }).fail(function (ex) {
                            notification.exception(ex);
                            vm.isLoader = false;
                        });
                    });
            }
        },
        downloadArchive() {
            const vm = this;
            const rpd_id = this.findGetParameter('rpd_id');


            /*   require(['core/ajax', 'core/notification', 'core/loadingicon'],
                   function (ajax, notification, LoadingIcon) {
                       var promises = ajax.call(
                           [
                               {
                                   methodname: 'download_file_archive',
                                   args: {
                                       rpd_id: rpd_id
                                   }
                               }
                           ], false
                       );
                       promises[0].done((response) => {
                           vm.isLoader = false;
                       }).fail(function (ex) {
                           notification.exception(ex);
                           vm.isLoader = false;
                       });
                   });
           */
        },
        async getRPDInfo() { //rename
            const vm = this;
            const edu_plan = this.findGetParameter('edu_plan');
            const discipline = this.findGetParameter('discipline');
            const rpd_id = this.findGetParameter('rpd_id');
            this.currentGuid = this.findGetParameter('guid');

            // const user_id = this.currentUserID;
            vm.isLoader = true;
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
                        vm.currentUserID = response;
                    }).fail(function (ex) {
                        notification.exception(ex);
                        vm.isLoader = false;
                    });
                    var promises = ajax.call(
                        [
                            {
                                methodname: 'get_competencies_for_rpd',
                                args: {
                                    edu_plan: edu_plan,
                                    discipline: discipline,
                                    rpd_id: rpd_id,
                                    user_id: vm.currentUserID.id,
                                    module_guid: vm.currentGuid
                                }
                            }
                        ], false
                    );
                    promises[0].done((response) => {
                        vm.isLoader = false;

                        // if (vm.currentUserID.length) {
                        let haveMoreThanOne = response.developers.filter(el =>
                            el.user_id == vm.currentUserID.id
                        );
                        //  }

                        vm.consolidateModules = response.developers.filter(el =>
                            el.user_id != vm.currentUserID.id
                        );
                        vm.chosenDev = response.developers.filter(el =>
                            el.guid == vm.currentGuid
                        );
                        if (vm.chosenDev.length) {
                            if (vm.chosenDev[0].user_id !== vm.currentUserID.id) {
                                vm.coDev = true;
                            }
                        }


                        vm.developers = response.developers;
                        vm.modules = haveMoreThanOne.length > 1 ? haveMoreThanOne : [];
                        vm.competences = response.competencies;
                        vm.dataTargetDiscipline = response.part1.target;
                        vm.dataTasksDiscipline = response.part1.taskfordisc;
                        vm.forms = response.forms;
                        vm.selectedBooksList = response.books;
                        vm.INFO = response.info;
                        vm.rpd_name = vm.INFO.discipline.substr(0, 10)
                            + " " +
                            vm.INFO.direction.substr(0, 8)
                            + " " +
                            vm.INFO.profile.substr(0, 10)
                            + " " +
                            vm.INFO.year;
                        if (!!vm.currentGuid) {
                            vm.showUserBlock = response.developers.filter(el =>
                                el.guid == vm.currentGuid
                            )[0].module;


                        } else {
                            let currDev = response.developers.filter(el =>
                                el.user_id == vm.currentUserID.id
                            )[0];
                            vm.showUserBlock = response.developers.filter(el =>
                                el.user_id == vm.currentUserID.id
                            )[0].module;
                            vm.currentGuid = currDev.guid;
                        }
                        vm.MTO = response.MTO;
                        response.controls.forEach(el => {
                            el.enroleTypes = _.uniqBy(el.enroleTypes, "code");
                        });

                        const c = response.controls;
                        vm.controls = c;

                        vm.parts = response.parts;
                        vm.parts.forEach(category => {
                         /*   category.data.forEach(theme => {
                                if (!theme.data.length) {

                                }
                            });*/
                        });

                        vm.appraisalTools = response.appraisalTools;
                        const cl = _.uniqBy(response.controlsList, "code");
                        vm.controlsList = cl;
                        vm.questionsList = response.questionsList;
                        vm.questionsForAllThemes = response.questionsForAllThemes;
                        vm.questionsForDiscipline = response.questionsForDiscipline;
                        vm.auditWork = response.auditWork;
                        vm.outwork = response.outwork;
                        vm.criteriaList = response.criteriaList;
                        vm.selectControls(response.controlsList);
                    }).fail(function (ex) {
                        notification.exception(ex);
                        vm.isLoader = false;
                    });
                });

        },
        getActiveTabContainer(tabName) {
            return tabName === this.selectedFilter.filter;
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
        editParts(partsList) {
            console.log('нужно записать новое', partsList)
            this.parts = partsList;
        },
        handleDeleteQuestionTheme(data) {
            const {editedPartID, editedThemeID, tab, questionId} = data;
            const indexPartToEdit = this.parts.findIndex(item => item.id === editedPartID);
            const indexThemeToEdit = this.parts[indexPartToEdit].data.findIndex(item => item.id === editedThemeID);
            this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab] =
                this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab].filter(item => item.id !== questionId)
        },
        handleAddQuestionToTheme(data) {
            const {editedPartID, editedThemeID, tab, question} = data;
            const indexPartToEdit = this.parts.findIndex(item => item.id === editedPartID);
            const indexThemeToEdit = this.parts[indexPartToEdit].data.findIndex(item => item.id === editedThemeID);
            const editedControl = this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab];
            if (!this.parts[indexPartToEdit].data[indexThemeToEdit].data.hasOwnProperty(tab)) {
                console.log(tab);
            }
            if (Array.isArray(this.parts[indexPartToEdit].data[indexThemeToEdit].data)) {
                let f = Object.assign({}, this.parts[indexPartToEdit].data[indexThemeToEdit].data);
                // console.log(f);
                this.parts[indexPartToEdit].data[indexThemeToEdit].data = f;
            }
            if (editedControl.some(item => item.id === question.id)) {
                this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab] =
                    this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab].filter(item => item.id !== question.id);
            } else {
                this.parts[indexPartToEdit].data[indexThemeToEdit].data[tab].push(_.cloneDeep(question));
            }

        },
        deleteThemesControl(controlCode) {
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    this.$delete(theme.data, controlCode);
                });
            });
        },
        selectControls(controlsArr) {
            //if (controlsArr) {
            console.log(controlsArr);
            this.controlsList = _.cloneDeep(controlsArr);
            controlsArr.forEach(control => {
                if (control.template === 'second') {
                    if (!this.questionsForDiscipline.hasOwnProperty(control.code))
                        this.$set(this.questionsForDiscipline, control.code.toString(),
                            {
                                tasks: [],
                                themes: [],
                                questions: []
                            });
                }
                if (control.template === 'third') {
                    if (!this.questionsForDiscipline.hasOwnProperty(control.code))
                        this.$set(this.questionsForDiscipline, control.code.toString(),
                            {
                                themes: [],
                                questions: []
                            });
                }
                if (!this.criteriaList.hasOwnProperty(control.code)) {
                    console.log(this.criteriaList, control.code.toString());
                    //список критериев и шкал оценивания
                    this.$set(this.criteriaList, control.code.toString(), '');
                }
                this.parts.forEach(part => {
                    part.data.forEach(theme => {
                        if (!theme.data.hasOwnProperty(control.code)) {
                            //списки вопросов и прочее
                            if (control.template === 'first' || control.template === 'fourth' || control.template === 'fifth') {
                                this.$set(theme.data, control.code.toString(), []);
                            }
                        }

                    })
                });
            });
            /*this.parts.forEach(part => {
                part.data.forEach(theme => {
                    controlsArr.forEach(control => {
                        if (!theme.data.hasOwnProperty(control.code)) {
                            //списки вопросов и прочее
                            if (control.template === 'first' || control.template === 'fourth' || control.template === 'fifth') {
                                this.$set(theme.data, control.code.toString(), []);
                            } else if (control.template === 'second') {
                                if (!this.questionsForDiscipline.hasOwnProperty(control.code))
                                    this.$set(this.questionsForDiscipline, control.code.toString(),
                                        {
                                            tasks: [],
                                            themes: [],
                                            questions: []
                                        });
                            } else if (control.template === 'third') {
                                if (!this.questionsForDiscipline.hasOwnProperty(control.code))
                                    this.$set(this.questionsForDiscipline, control.code.toString(),
                                        {
                                            themes: [],
                                            questions: []
                                        });
                            }
                        }
                        if (!this.criteriaList.hasOwnProperty(control.code)) {
                            console.log(this.criteriaList, control.code.toString());
                            //список критериев и шкал оценивания
                            this.$set(this.criteriaList, control.code.toString(), '');
                        }
                    })
                });
            });*/
            //  }

        },
        editParts(partsList) {
            this.parts = partsList;
        },
        getActiveTabContainer(tabName) {
            return tabName === this.selectedFilter.filter;
        },
    },
    watch: {
        accessAdminFilters: {
            handler: function (old, newVal) {
                const activeFilters = old.filter(item => item.disabled !== true)
                this.selectedFilter = activeFilters[0]
            },
            immediate: true
        },
        currentUserID: {
            handler: function (newVal, old) {
                let haveCoDev = [];
                if (this.hasOwnProperty('INFO')) {
                    if (this.INFO.developers.length !== 0) {
                        if (this.INFO.developers.hasOwnProperty('coDevelopers')) {
                            haveCoDev = this.INFO.developers.coDevelopers.filter(el => (
                                el.id === newVal.id
                            ));
                        }
                        if (this.INFO.developers.mainDeveloper.length) {
                            if (newVal.id === this.INFO.developers.mainDeveloper[0].id || haveCoDev.length) {
                                this.isDeveloper = true;
                            }
                        }
                    }
                }
            },
        },
        INFO: {
            handler: function (newVal, old) {
                if (this.hasOwnProperty('INFO')) {
                    if (this.INFO.developers.mainDeveloper.length) {
                        if (this.INFO.developers.mainDeveloper[0].id == this.currentUserID.id) {
                            this.isAdmin = true;
                            //this.showUserBlock = this.INFO.developers.mainDeveloper[0].blockControl;
                        } else {
                            this.isAdmin = false;
                            let codev = this.INFO.developers.coDevelopers.filter(el => el.id === this.currentUserID.id);
                            // this.showUserBlock = codev[0].blockControl;
                        }
                    } else if (this.INFO.developers.coDevelopers.filter(el => el.id === this.currentUserID.id)) {
                        this.isAdmin = false;
                        let codev = this.INFO.developers.coDevelopers.filter(el => el.id === this.currentUserID.id);
                        // this.showUserBlock = codev[0].blockControl;
                        this.isDeveloper = true;
                    }
                }

                /*if (this.INFO.developers.mainDeveloper[0].id === this.currentUserID) {
                    this.showUserBlock = this.INFO.developers.mainDeveloper[0].blockControl;
                } else {
                    this.showUserBlock = this.INFO.developers.coDevelopers.filter( el => el.id === this.currentUserID ).blockControl;
                }*/
            },
        }
    },
})