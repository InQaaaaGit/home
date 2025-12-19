Vue.component('vue-draggable-resizable', window.VueDraggableResizable.default);
const themes = {
    props: {
        forms: {
            type: Array,
            required: true
        },
        parts: {
            type: Array,
            required: true,
        },
        coDev: {
            type: Boolean,
            default: false,
        }

    },
    data: function () {
        return {
            activeEducationGuidForm: null,
            selectedForms: '',
            selectedParts: [],
            isNewPart: false,
            showModalThemes: false,
            modalPartEdit: false,
            partsList: [],
            partToAddTheme: '',
            defaultItem: {
                "name_segment": "",
                "id": "",
                "type": "",
                "all": "",
                "all_za": "",
                "all_oza": "",
                "data": [
                    {
                        "name_segment": "",
                        "lection": "0",
                        "lection_za": "0",
                        "lection_oza": "0",

                        "practice": "0",
                        "practice_za": "0",
                        "practice_oza": "0",

                        "lab": "0",
                        "lab_za": "0",
                        "lab_oza": "0",

                        "outwork": "0",
                        "outwork_za": "0",
                        "outwork_oza": "0",

                        "interactive": "0",
                        "interactive_za": "0",
                        "interactive_oza": "0",

                        "practicePrepare": "0",
                        "practicePrepare_za": "0",
                        "practicePrepare_oza": "0",

                        "description": "",
                        "seminaryQuestion": "",
                        "seminaryQuestion_za": "",
                        "seminaryQuestion_oza": "",

                        "description_practice": "",
                        "description_practice_za": "",
                        "description_practice_oza": "",
                        "description_outwork": "",
                        "description_outwork_za": "",
                        "description_outwork_oza": "",
                        "id": "",
                        "type": "",
                        "all": "",
                        "data": {}
                    }
                ]
            },
            editedItem: {
                "name_segment": "",
                "id": "",
                "type": "",
                "all": "",
                "all_za": "",
                "all_oza": "",
                "data": [
                    {
                        "name_segment": "",
                        "lection": "0",
                        "lection_za": "0",
                        "lection_oza": "0",

                        "practice": "0",
                        "practice_za": "0",
                        "practice_oza": "0",

                        "lab": "0",
                        "lab_za": "0",
                        "lab_oza": "0",

                        "outwork": "0",
                        "outwork_za": "0",
                        "outwork_oza": "0",

                        "interactive": "0",
                        "interactive_za": "0",
                        "interactive_oza": "0",

                        "practicePrepare": "0",
                        "practicePrepare_za": "0",
                        "practicePrepare_oza": "0",

                        "description": "",
                        "seminaryQuestion": "",
                        "seminaryQuestion_za": "",
                        "seminaryQuestion_oza": "",

                        "description_practice": "",
                        "description_practice_za": "",
                        "description_practice_oza": "",
                        "description_outwork": "",
                        "description_outwork_za": "",
                        "description_outwork_oza": "",
                        "id": "",
                        "type": "",
                        "all": "",
                        "data": {}
                    }
                ]
            },
            isEditTheme: false,
            idxPartEdited: null,
            idxThemeEdited: null,
            defaultPart: {
                name_segment: ""
            },
            editedPart: {
                name_segment: ""
            },
            partIndexEdited: null,
            themesToDelete: [],

            modalThemeInfo: false,
            themeInfo: '',
          //  edu_form_o: '9aa1b4fe-5321-11ef-8adc-9ecc5fe5123c', //fd0ee189-ffbd-11ed-85f5-00155d055200
            edu_form_o: 'fd0ee189-ffbd-11ed-85f5-00155d055200', //fd0ee189-ffbd-11ed-85f5-00155d055200
            edu_form_z: '8e9e6559-77e7-11e5-aaf6-00237dcf6128',
            edu_form_oz: '8e9e655b-77e7-11e5-aaf6-00237dcf6128',

        }
    },
    watch: {
        isNewPart(newVal, oldVal) {
            this.partToAddTheme = ''
        },
        parts(val, oldVal) {
            this.$emit('update:parts', val);
        },
        forms: {
            handler: function (newVal, old) {
                if (this.forms.length) {
                    this.activeEducationGuidForm = this.forms[0].guidform;
                    this.selectedForms = [...this.forms[0].load];
                }
            },

            immediate: true,
            deep: true
        },

    },
    created() {
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: "description",
                disabled: false,
                ghostClass: "ghost"
            };
        },
        isDisabledByPractice() {
            if (this.forms.length) {
                const formType = this.forms.find(item => item.guidform === this.activeEducationGuidForm).name.trim().toLowerCase();
                let practiceForm = ''
                if (formType === 'очная') practiceForm = 'practice'
                else if (formType === 'заочная') practiceForm = 'practice_za'
                else if (formType === 'очно-заочная') practiceForm = 'practice_oza'
                return !Boolean(Number(this.editedItem.data[0][practiceForm])) || !Boolean(this.selectedForms[1].value)
            }
        },
        getPartsSelect() {
            if (this.parts.length) {
                return this.parts.map(item => ({
                    name_segment: item.name_segment,
                    id: item.id
                }))
            }
        },
        getAllLectureHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[0]])
                })
            })
            return hoursCount
        },
        getAllPracticeHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[1]])
                })
            })
            return hoursCount
        },
        getAllLabHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[2]])
                })
            })
            return hoursCount
        },
        getAllOutworkHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[3]])
                })
            })
            return hoursCount
        },
        getAllInteractiveHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[4]])
                })
            })
            return hoursCount
        },
        getAllPracticePrepareHorse() {
            let hoursCount = 0;
            this.parts.forEach(item => {
                item.data.forEach(theme => {
                    hoursCount += Number(theme[this.getEducationFormFields[5]])
                })
            })
            return hoursCount
        },
        //Все часы нагрузки
        getAllHours() {
            if (!this.selectedForms.length) {
                return 0;
            }
            return this.selectedForms.reduce((sum, current) => {

                return sum + Number(current?.value);
            }, 0);
        },
        getEducationFormFields() {
            switch (this.activeEducationGuidForm) {
                case this.edu_form_o:
                    return ['lection', 'practice', 'lab', 'outwork', 'interactive', 'practicePrepare', 'seminaryQuestion']
                    break
                case this.edu_form_z:
                    return ['lection_za', 'practice_za', 'lab_za', 'outwork_za', 'interactive_za', 'practicePrepare_za', 'seminaryQuestion_za']
                    break
                case this.edu_form_oz:
                    return ['lection_oza', 'practice_oza', 'lab_oza', 'outwork_oza', 'interactive_oza', 'practicePrepare_oza', 'seminaryQuestion_oza']
                    break
            }
        },
        setNewHeaders() {
            if (this.selectedForms.length) {
                return this.selectedForms.map(item => item.type).map(header => {
                    if (['интерактивная форма', 'практическая подготовка'].includes(header)) {
                        const wbrHeader = header.split(' ');
                        return `в т.ч <br> ${wbrHeader[0]} <br> ${wbrHeader[1]}`
                    } else {
                        return header
                    }
                });
            }
            return [];

        },
        //остаток часов по дисциплине всего
        getRemainHorse() {
            //this.activeEducationGuidForm;


            let remainingHourse = this.getAllHours -
                this.getAllLectureHorse -
                this.getAllPracticeHorse -
                this.getAllLabHorse -
                this.getAllOutworkHorse -
                this.getAllInteractiveHorse -
                this.getAllPracticePrepareHorse;
            //this.remainingh = remainingHourse;
            return remainingHourse;
        },
        //остаток часов по лекциям
        getRemainLectureHorse() {
            return this.selectedForms[0].value - this.getAllLectureHorse;
        },
        // остаток часов по практике
        getRemainPracticeHorse() {
            return this.selectedForms[1].value - this.getAllPracticeHorse;
        },
        //остаток часов по лаб
        getRemainLabHorse() {
            return this.selectedForms[2].value - this.getAllLabHorse;
        },
        //остаток часов срс
        getRemainOutworkHorse() {
            return this.selectedForms[3].value - this.getAllOutworkHorse;
        },
        //остаток часов интерактиву
        getRemainInteractiveHorse() {
            return this.selectedForms[4].value - this.getAllInteractiveHorse;
        },
        //остаток часов по практической подготовке
        getRemainPracticePrepareHorse() {
            return this.selectedForms[5].value - this.getAllPracticePrepareHorse;
        },
        //Часы по очной форме обучения
        getEducationO() {
            let accumulate = 0;
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    accumulate += (
                        parseInt(theme.lab) +
                        parseInt(theme.lection) +
                        parseInt(theme.practice) +
                        parseInt(theme.outwork) +
                        parseInt(theme.interactive) +
                        parseInt(theme.practicePrepare)
                    )
                })
            })
            return accumulate
        },
        //Часы по заочной форме обучения
        getEducationZA() {
            let accumulate = 0;
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    accumulate += (
                        parseInt(theme.lab_za) +
                        parseInt(theme.lection_za) +
                        parseInt(theme.practice_za) +
                        parseInt(theme.outwork_za) +
                        parseInt(theme.interactive_za) +
                        parseInt(theme.practicePrepare_za)
                    )
                })
            })
            return accumulate
        },
        //Часы по очно-заочной форме обучения
        getEducationOZA() {
            let accumulate = 0;
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    accumulate += (
                        parseInt(theme.lab_oza) +
                        parseInt(theme.lection_oza) +
                        parseInt(theme.practice_oza) +
                        parseInt(theme.outwork_oza) +
                        parseInt(theme.interactive_oza) +
                        parseInt(theme.practicePrepare_oza)
                    )
                })
            })
            return accumulate
        },
    },
    methods: {
        openModalThemeInfo(themeDescription) {
            this.modalThemeInfo = true;
            this.themeInfo = themeDescription;
        },
        closeModalThemeInfo() {
            this.modalThemeInfo = false;
            this.themeInfo = '';
        },
        isPartChecked(partID) {
            const themesInPart = this.parts.find(item => item.id === partID).data.map(item => item.id);
            return themesInPart.filter(item => this.themesToDelete.includes(item)).length === themesInPart.length &&
                themesInPart.filter(item => this.themesToDelete.includes(item)).length !== 0;
        },
        selectAllThemesInPart(partID) {
            const themesInPart = this.parts.find(item => item.id === partID).data.map(item => item.id);
            if (themesInPart.filter(item => this.themesToDelete.includes(item)).length === themesInPart.length) {
                this.themesToDelete = _.difference(this.themesToDelete, themesInPart);
            } else if (themesInPart.filter(item => this.themesToDelete.includes(item)).length !== themesInPart.length) {
                this.themesToDelete = this.themesToDelete.concat(_.difference(themesInPart, this.themesToDelete));
            }

        },
        removeThemes() {
            this.$dialog
                .confirm('Удалить выбранные темы?')
                .then(dialog => {
                    this.parts.forEach(part => {
                        part.data = part.data.filter(theme => !this.themesToDelete.includes(theme.id));
                    });
                })
                .then(() => {
                    this.themesToDelete = [];
                    this.$toast.open({
                        message: `Выбранное удалено`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                })
        },
        selectThemeToDelete(themeID) {
            const isIncludeTheme = this.themesToDelete.includes(themeID);
            if (isIncludeTheme) this.themesToDelete = this.themesToDelete.filter(item => item !== themeID);
            else this.themesToDelete.push(themeID);
        },
        isThemeChecked(themeID) {
            return this.themesToDelete.includes(themeID);
        },
        openModalsTheme() {
            if (!this.parts.length) this.isNewPart = true;
            this.showModalThemes = true;
        },
        changeHorseValue(enterValue, fieldIdxToChange, allHoursPartLoad, currentTypeHours) {
            // isEdited - если мы редактируем тему, то нам нужно докинуть часы
            const isEdited = this.isEditTheme ?
                parseFloat(this.parts[this.idxPartEdited].data[this.idxThemeEdited][this.getEducationFormFields[fieldIdxToChange]]) : 0
            //смторим разницу часов, если уходит в минус, то возвращаем поле в ноль
            if (parseFloat(allHoursPartLoad) - parseFloat(currentTypeHours) - parseFloat(enterValue) + isEdited < 0) {
                this.editedItem.data[0][this.getEducationFormFields[fieldIdxToChange]] = '0';
                this.$toast.open({
                    message: `Превышено количество часов. Доступно часов: ${allHoursPartLoad - currentTypeHours + isEdited}`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                //смторим не оставил ли пустоту, то возвращаем поле в ноль
            } else if (enterValue.length === 0) {
                this.editedItem.data[0][this.getEducationFormFields[fieldIdxToChange]] = '0';
                this.$toast.open({
                    message: `Часы не могут быть пустыми.`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                //записываем введеное значение, если все хорошо
            } else {
                //this.editedItem.data[0][this.getEducationFormFields[fieldIdxToChange]] = parseFloat(enterValue).toString();
                this.editedItem.data[0][this.getEducationFormFields[fieldIdxToChange]] = parseFloat(enterValue);
            }
        },
        confirmModalThemes() {
            // Валидация формы
            const isNameEmpty = this.editedItem.name_segment.trim() ? '' : 'Название раздела';
            const isThemeEmpty = this.editedItem.data[0].name_segment.trim() ? '' : 'Тему';
            const isDescriptionEmpty = this.editedItem.data[0].description.trim() ? '' : 'Содержание';
            const partToAddTheme = Object.values(this.partToAddTheme).length ? '' : 'Выберите раздел';

            if (!this.isEditTheme) {
                if (this.isNewPart) {
                    if (isNameEmpty || isThemeEmpty || isDescriptionEmpty) {
                        this.$toast.open({
                            message: `
                                <div class="error-box">
                                    <span>Заполните:</span> 
                                    <span>${isNameEmpty}</span> 
                                    <span>${isThemeEmpty}</span> 
                                    <span>${isDescriptionEmpty}</span>
                                </div>`,
                            type: "error",
                            duration: 5000,
                            dismissible: true
                        });
                        return;
                    }
                } else {
                    if (partToAddTheme || isThemeEmpty || isDescriptionEmpty) {
                        this.$toast.open({
                            message: `
                            <div class="error-box">
                                <span>Заполните:</span> 
                                <span>${partToAddTheme}</span> 
                                <span>${isThemeEmpty}</span>
                                <span>${isDescriptionEmpty}</span>
                            </div>`,
                            type: "error",
                            duration: 5000,
                            dismissible: true
                        });
                        return;
                    }
                }
            } else {
                if (isThemeEmpty || isDescriptionEmpty) {
                    this.$toast.open({
                        message: `
                            <div class="error-box">
                                <span>Заполните:</span> 
                                <span>${isThemeEmpty}</span> 
                                <span>${isDescriptionEmpty}</span>
                            </div>`,
                        type: "error",
                        duration: 5000,
                        dismissible: true
                    });
                    return
                }
            }
            //если правим тему
            if (this.isEditTheme) {
                let {id} = this.parts[this.idxPartEdited];
                // проверка если хотим переместить тему
                const theme = _.cloneDeep(this.editedItem.data[0]);
                if (id === this.partToAddTheme.id) {
                    this.parts[this.idxPartEdited].data.splice(this.idxThemeEdited, 1, theme);
                } else {
                    const indexPartToInsert = this.parts.findIndex(item => item.id === this.partToAddTheme.id);
                    this.parts[this.idxPartEdited].data.splice(this.idxThemeEdited, 1);
                    this.parts[indexPartToInsert].data.push(theme);
                }
                // если добавляем тему в раздел
            } else if (Object.keys(this.partToAddTheme).length) {
                const partToAdd = this.parts.find(item => item.id === this.partToAddTheme.id);
                const clonedTheme = _.cloneDeep(this.editedItem.data[0])
                clonedTheme.id = uuidv4();
                partToAdd.data.push(clonedTheme);
                // если добавляем тему с разделом
            } else {
                this.editedItem.id = uuidv4();
                this.editedItem.data[0].id = uuidv4();
                this.parts.push(_.cloneDeep(this.editedItem));
            }
            this.closeModalThemes();
        },
        closeModalThemes() {
            this.showModalThemes = false;
            this.partToAddTheme = '';
            this.isEditTheme = false;
            this.isNewPart = false;
            this.idxPartEdited = null;
            this.idxThemeEdited = null;
            this.editedItem = _.cloneDeep(this.defaultItem);
        },
        partAllHours(parts, keyToSum = false) {
            return parts.reduce((sum, cur) => {
                if (keyToSum) {
                    return sum + Number(cur[keyToSum]);
                } else {
                    return sum +
                        Number(cur[this.getEducationFormFields[0]]) +
                        Number(cur[this.getEducationFormFields[1]]) +
                        Number(cur[this.getEducationFormFields[2]]) +
                        Number(cur[this.getEducationFormFields[3]]) +
                        Number(cur[this.getEducationFormFields[4]]) +
                        Number(cur[this.getEducationFormFields[5]]);
                }
            }, 0);
        },
        themeAllHours(part) {
            return Number(part[this.getEducationFormFields[0]]) +
                Number(part[this.getEducationFormFields[1]]) +
                Number(part[this.getEducationFormFields[2]]) +
                Number(part[this.getEducationFormFields[3]]) +
                Number(part[this.getEducationFormFields[4]]) +
                Number(part[this.getEducationFormFields[5]]);
        },
        onlyNumber($event) {
            let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46 && keyCode!==188) {
                $event.preventDefault();
            }
        },
        selectEducationForm(form) {
            this.activeEducationGuidForm = form.guidform;
            this.selectedForms = [...form.load];
            this.selectedParts = _.cloneDeep(form.parts);
        },
        editSection(partIndex) {
            this.partIndexEdited = partIndex;
            this.editedPart.name_segment = this.parts[partIndex].name_segment;

            this.modalPartEdit = true;
        },
        closeModalPartEdit() {
            this.partIndexEdited = null;
            this.modalPartEdit = false;
            this.editedPart = {...this.defaultPart};
        },
        savePart() {
            if (!this.editedPart.name_segment.trim()) {
                this.$toast.open({
                    message: `Название раздела обязательно`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            this.parts[this.partIndexEdited].name_segment = this.editedPart.name_segment;
            this.closeModalPartEdit();
        },
        editTheme(partIndex, themeIndex) {
            if (!this.coDev) {
                let {id, name_segment} = this.parts[partIndex];
                this.partToAddTheme = {id, name_segment};

                this.idxPartEdited = partIndex;
                this.idxThemeEdited = themeIndex;

                const editedTheme = _.cloneDeep(this.parts[partIndex].data[themeIndex]);
                this.editedItem.data.splice(0, 1, editedTheme);
                this.isEditTheme = true;
                this.showModalThemes = true;
            }
        },
        deleteTheme(themeId) {
            this.$dialog
                .confirm('Удалить тему?')
                .then(dialog => {
                    this.parts.forEach(part => {
                        part.data = part.data.filter(item => item.id !== themeId);
                    })
                })
                .then(() => {
                    this.$toast.open({
                        message: `Тема удалена`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                })
        },
        deletePart(partId) {
            this.$dialog
                .confirm('Удалить раздел?')
                .then(dialog => {
                    this.parts = this.parts.filter(part => part.id !== partId);
                })
                .then(() => {
                    this.$toast.open({
                        message: `Раздел удалена`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                })
        },

        getSumFromPartByKey(key, part) {
            let accumulate = 0;
            part.data.forEach(theme => {
                accumulate += parseInt(theme[key])
            })
            return accumulate
        },
        duplicatePart(partId) {
            this.$dialog
                .confirm(`Раздел дублируется пустой при суммароном привышение часов тем хотя бы по одному из планов. <br><br>Дублировать раздел?`)
                .then(dialog => {
                    const clonedPart = _.cloneDeep(this.parts.find(item => item.id === partId));

                    //есть ли очная форма обучения
                    const findO = this.forms.findIndex(item => item.guidform === this.edu_form_o);

                    //есть ли заочная форма обучения
                    const findZ = this.forms.findIndex(item => item.guidform === this.edu_form_z);

                    //есть ли очно-заочная форма обучения
                    const findOZA = this.forms.findIndex(item => item.guidform === this.edu_form_oz);

                    if (findO !== -1) {
                        const arrHoursLoad = this.forms[findO].load.map(item => item.value);

                        const arrTypes = [
                            this.getSumByKey('lection'),
                            this.getSumByKey('practice'),
                            this.getSumByKey('lab'),
                            this.getSumByKey('outwork'),
                            this.getSumByKey('interactive'),
                            this.getSumByKey('practicePrepare')
                        ];

                        const arrAll = [
                            this.getSumFromPartByKey('lection', clonedPart),
                            this.getSumFromPartByKey('practice', clonedPart),
                            this.getSumFromPartByKey('lab', clonedPart),
                            this.getSumFromPartByKey('outwork', clonedPart),
                            this.getSumFromPartByKey('interactive', clonedPart),
                            this.getSumFromPartByKey('practicePrepare', clonedPart)
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrTypes[index] - arrAll[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        const arrData = this.templateForPartDuplicate(clonedPart, this.activeEducationGuidForm)
                            .map(item => Object.values(item));


                        if (someNegativeValue) {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = '0';
                                item.lection_za = '0';
                                item.lection_oza = '0';
                                item.practice = '0';
                                item.practice_za = '0';
                                item.practice_oza = '0';
                                item.lab = '0';
                                item.lab_za = '0';
                                item.lab_oza = '0';
                                item.outwork = '0';
                                item.outwork_za = '0';
                                item.outwork_oza = '0';
                                item.interactive = '0';
                                item.interactive_za = '0';
                                item.interactive_oza = '0';
                                item.practicePrepare = '0';
                                item.practicePrepare_za = '0';
                                item.practicePrepare_oza = '0';
                                item.id = uuidv4();
                            });
                        } else {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = arrData[idx][0];
                                item.lection_za = arrData[idx][0];
                                item.lection_oza = arrData[idx][0];

                                item.practice = arrData[idx][1];
                                item.practice_za = arrData[idx][1];
                                item.practice_oza = arrData[idx][1];

                                item.lab = arrData[idx][2];
                                item.lab_za = arrData[idx][2];
                                item.lab_oza = arrData[idx][2];

                                item.outwork = arrData[idx][3];
                                item.outwork_za = arrData[idx][3];
                                item.outwork_oza = arrData[idx][3];

                                item.interactive = arrData[idx][4];
                                item.interactive_za = arrData[idx][4];
                                item.interactive_oza = arrData[idx][4];

                                item.practicePrepare = arrData[idx][5];
                                item.practicePrepare_za = arrData[idx][5];
                                item.practicePrepare_oza = arrData[idx][5];
                                item.id = uuidv4();
                            });
                        }

                    }
                    if (findZ !== -1) {
                        const arrHoursLoad = this.forms[findZ].load.map(item => item.value);

                        const arrTypes = [
                            this.getSumByKey('lection_za'),
                            this.getSumByKey('practice_za'),
                            this.getSumByKey('lab_za'),
                            this.getSumByKey('outwork_za'),
                            this.getSumByKey('interactive_za'),
                            this.getSumByKey('practicePrepare_za')
                        ];

                        const arrAll = [
                            this.getSumFromPartByKey('lection_za', clonedPart),
                            this.getSumFromPartByKey('practice_za', clonedPart),
                            this.getSumFromPartByKey('lab_za', clonedPart),
                            this.getSumFromPartByKey('outwork_za', clonedPart),
                            this.getSumFromPartByKey('interactive_za', clonedPart),
                            this.getSumFromPartByKey('practicePrepare_za', clonedPart)
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrTypes[index] - arrAll[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        const arrData = this.templateForPartDuplicate(clonedPart, this.activeEducationGuidForm)
                            .map(item => Object.values(item));


                        if (someNegativeValue) {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = '0';
                                item.lection_za = '0';
                                item.lection_oza = '0';
                                item.practice = '0';
                                item.practice_za = '0';
                                item.practice_oza = '0';
                                item.lab = '0';
                                item.lab_za = '0';
                                item.lab_oza = '0';
                                item.outwork = '0';
                                item.outwork_za = '0';
                                item.outwork_oza = '0';
                                item.interactive = '0';
                                item.interactive_za = '0';
                                item.interactive_oza = '0';
                                item.practicePrepare = '0';
                                item.practicePrepare_za = '0';
                                item.practicePrepare_oza = '0';
                                item.id = uuidv4();
                            });
                        } else {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = arrData[idx][0];
                                item.lection_za = arrData[idx][0];
                                item.lection_oza = arrData[idx][0];

                                item.practice = arrData[idx][1];
                                item.practice_za = arrData[idx][1];
                                item.practice_oza = arrData[idx][1];

                                item.lab = arrData[idx][2];
                                item.lab_za = arrData[idx][2];
                                item.lab_oza = arrData[idx][2];

                                item.outwork = arrData[idx][3];
                                item.outwork_za = arrData[idx][3];
                                item.outwork_oza = arrData[idx][3];

                                item.interactive = arrData[idx][4];
                                item.interactive_za = arrData[idx][4];
                                item.interactive_oza = arrData[idx][4];

                                item.practicePrepare = arrData[idx][5];
                                item.practicePrepare_za = arrData[idx][5];
                                item.practicePrepare_oza = arrData[idx][5];
                                item.id = uuidv4();
                            });
                        }
                    }
                    if (findOZA !== -1) {
                        const arrHoursLoad = this.forms[findOZA].load.map(item => item.value);

                        const arrTypes = [
                            this.getSumByKey('lection_oza'),
                            this.getSumByKey('practice_oza'),
                            this.getSumByKey('lab_oza'),
                            this.getSumByKey('outwork_oza'),
                            this.getSumByKey('interactive_oza'),
                            this.getSumByKey('practicePrepare_oza')
                        ];

                        const arrAll = [
                            this.getSumFromPartByKey('lection_oza', clonedPart),
                            this.getSumFromPartByKey('practice_oza', clonedPart),
                            this.getSumFromPartByKey('lab_oza', clonedPart),
                            this.getSumFromPartByKey('outwork_oza', clonedPart),
                            this.getSumFromPartByKey('interactive_oza', clonedPart),
                            this.getSumFromPartByKey('practicePrepare_oza', clonedPart)
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrTypes[index] - arrAll[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        const arrData = this.templateForPartDuplicate(clonedPart, this.activeEducationGuidForm)
                            .map(item => Object.values(item));


                        if (someNegativeValue) {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = '0';
                                item.lection_za = '0';
                                item.lection_oza = '0';
                                item.practice = '0';
                                item.practice_za = '0';
                                item.practice_oza = '0';
                                item.lab = '0';
                                item.lab_za = '0';
                                item.lab_oza = '0';
                                item.outwork = '0';
                                item.outwork_za = '0';
                                item.outwork_oza = '0';
                                item.interactive = '0';
                                item.interactive_za = '0';
                                item.interactive_oza = '0';
                                item.practicePrepare = '0';
                                item.practicePrepare_za = '0';
                                item.practicePrepare_oza = '0';
                                item.id = uuidv4();
                            });
                        } else {
                            clonedPart.data.forEach((item, idx) => {
                                item.lection = arrData[idx][0];
                                item.lection_za = arrData[idx][0];
                                item.lection_oza = arrData[idx][0];

                                item.practice = arrData[idx][1];
                                item.practice_za = arrData[idx][1];
                                item.practice_oza = arrData[idx][1];

                                item.lab = arrData[idx][2];
                                item.lab_za = arrData[idx][2];
                                item.lab_oza = arrData[idx][2];

                                item.outwork = arrData[idx][3];
                                item.outwork_za = arrData[idx][3];
                                item.outwork_oza = arrData[idx][3];

                                item.interactive = arrData[idx][4];
                                item.interactive_za = arrData[idx][4];
                                item.interactive_oza = arrData[idx][4];

                                item.practicePrepare = arrData[idx][5];
                                item.practicePrepare_za = arrData[idx][5];
                                item.practicePrepare_oza = arrData[idx][5];
                                item.id = uuidv4();
                            });
                        }
                    }

                    clonedPart.name_segment = `${clonedPart.name_segment} - дубль`;
                    clonedPart.id = uuidv4();

                    this.parts.push(clonedPart);
                })
                .then(() => {
                    this.$toast.open({
                        message: `Раздел дублирован`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                })
                .catch((e) => {
                    console.error(e);
                })
        },
        getSumByKey(key) {
            let accumulate = 0;
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    accumulate += parseInt(theme[key])
                })
            });
            return accumulate
        },
        duplicateTheme(partIdx, themeId) {
            this.$dialog
                .confirm('Тема дублируется пустой при суммароном привышение часов хотя бы по одному из планов. <br><br> Дублировать тему?')
                .then(dialog => {
                    const partToInsert = this.parts[partIdx];
                    const clonedTheme = _.cloneDeep(partToInsert.data.find(item => item.id === themeId));

                    //есть ли очная форма обучения
                    const findO = this.forms.findIndex(item => item.guidform === this.edu_form_o);

                    //есть ли заочная форма обучения
                    const findZ = this.forms.findIndex(item => item.guidform === this.edu_form_z);

                    //есть ли очно-заочная форма обучения
                    const findOZA = this.forms.findIndex(item => item.guidform === this.edu_form_oz);

                    if (findO !== -1) {
                        //получаем массив часов дублируемой темы
                        const arrHours = Object.values(this.templateForThemeDuplicate(clonedTheme, this.activeEducationGuidForm));

                        //достаем все часы нагрузки
                        const arrHoursLoad = this.forms[findO].load.map(item => item.value);

                        //суммы часов всех типов
                        const arrTypes = [
                            this.getSumByKey('lection'),
                            this.getSumByKey('practice'),
                            this.getSumByKey('lab'),
                            this.getSumByKey('outwork'),
                            this.getSumByKey('interactive'),
                            this.getSumByKey('practicePrepare')
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrHours[index] - arrTypes[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        if (someNegativeValue) {
                            clonedTheme.lection = '0';
                            clonedTheme.practice = '0';
                            clonedTheme.lab = '0';
                            clonedTheme.outwork = '0';
                            clonedTheme.interactive = '0';
                            clonedTheme.practicePrepare = '0';
                        } else {
                            clonedTheme.lection = arrHours[0];
                            clonedTheme.practice = arrHours[1];
                            clonedTheme.lab = arrHours[2];
                            clonedTheme.outwork = arrHours[3];
                            clonedTheme.interactive = arrHours[4];
                            clonedTheme.practicePrepare = arrHours[5];
                        }

                    }

                    if (findZ !== -1) {
                        const arrHours = Object.values(this.templateForThemeDuplicate(clonedTheme, this.activeEducationGuidForm));

                        //достаем все часы нагрузки
                        const arrHoursLoad = this.forms[findZ].load.map(item => item.value);

                        //суммы часов всех типов
                        const arrTypes = [
                            this.getSumByKey('lection_za'),
                            this.getSumByKey('practice_za'),
                            this.getSumByKey('lab_za'),
                            this.getSumByKey('outwork_za'),
                            this.getSumByKey('interactive_za'),
                            this.getSumByKey('practicePrepare_za')
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrHours[index] - arrTypes[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        if (someNegativeValue) {
                            clonedTheme.lection_za = '0';
                            clonedTheme.practice_za = '0';
                            clonedTheme.lab_za = '0';
                            clonedTheme.outwork_za = '0';
                            clonedTheme.interactive_za = '0';
                            clonedTheme.practicePrepare_za = '0';
                        } else {
                            clonedTheme.lection_za = arrHours[0];
                            clonedTheme.practice_za = arrHours[1];
                            clonedTheme.lab_za = arrHours[2];
                            clonedTheme.outwork_za = arrHours[3];
                            clonedTheme.interactive_za = arrHours[4];
                            clonedTheme.practicePrepare_za = arrHours[5];
                        }

                    }

                    if (findOZA !== -1) {
                        const arrHours = Object.values(this.templateForThemeDuplicate(clonedTheme, this.activeEducationGuidForm));

                        //достаем все часы нагрузки
                        const arrHoursLoad = this.forms[findOZA].load.map(item => item.value);

                        //суммы часов всех типов
                        const arrTypes = [
                            this.getSumByKey('lection_oza'),
                            this.getSumByKey('practice_oza'),
                            this.getSumByKey('lab_oza'),
                            this.getSumByKey('outwork_oza'),
                            this.getSumByKey('interactive_oza'),
                            this.getSumByKey('practicePrepare_oza')
                        ];

                        const remainingHours = arrHoursLoad.map((item, index) => {
                            return item - arrHours[index] - arrTypes[index]
                        });

                        const someNegativeValue = remainingHours.some(item => item < 0);

                        if (someNegativeValue) {
                            clonedTheme.lection_oza = '0';
                            clonedTheme.practice_oza = '0';
                            clonedTheme.lab_oza = '0';
                            clonedTheme.outwork_oza = '0';
                            clonedTheme.interactive_oza = '0';
                            clonedTheme.practicePrepare_oza = '0';
                        } else {
                            clonedTheme.lection_oza = arrHours[0];
                            clonedTheme.practice_oza = arrHours[1];
                            clonedTheme.lab_oza = arrHours[2];
                            clonedTheme.outwork_oza = arrHours[3];
                            clonedTheme.interactive_oza = arrHours[4];
                            clonedTheme.practicePrepare_oza = arrHours[5];
                        }

                    }
                    clonedTheme.id = uuidv4();
                    clonedTheme.name_segment = `${clonedTheme.name_segment} - дубль`

                    partToInsert.data.push(clonedTheme);
                })
                .then(() => {
                    this.$toast.open({
                        message: `Тема дублирована`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                })
                .catch((e) => {
                    console.log(e)
                })
        },
        templateForThemeDuplicate(theme, activeEducationGuidForm) {
            //очная
            if (activeEducationGuidForm === this.edu_form_o) {
                return {
                    lection: theme.lection,
                    practice: theme.practice,
                    lab: theme.lab,
                    outwork: theme.outwork,
                    interactive: theme.interactive,
                    practicePrepare: theme.practicePrepare,
                }
            }
            //заочная
            else if (activeEducationGuidForm === this.edu_form_z) {
                return {
                    lection_za: theme.lection_za,
                    practice_za: theme.practice_za,
                    lab_za: theme.lab_za,
                    outwork_za: theme.outwork_za,
                    interactive_za: theme.interactive_za,
                    practicePrepare_za: theme.practicePrepare_za,
                }
            }
            //очно-заочная
            else if (activeEducationGuidForm === this.edu_form_oz) {
                return {
                    lection_oza: theme.lection_oza,
                    practice_oza: theme.practice_oza,
                    lab_oza: theme.lab_oza,
                    outwork_oza: theme.outwork_oza,
                    interactive_oza: theme.interactive_oza,
                    practicePrepare_oza: theme.practicePrepare_oza,
                }
            }
        },
        templateForPartDuplicate(part, activeEducationGuidForm) {
            if (activeEducationGuidForm === this.edu_form_o) {
                return part.data.map(item => ({
                    lection: item.lection,
                    practice: item.practice,
                    lab: item.lab,
                    outwork: item.outwork,
                    interactive: item.interactive,
                    practicePrepare: item.practicePrepare,
                }))
            }
            //заочная
            else if (activeEducationGuidForm === this.edu_form_z) {
                return part.data.map(item => ({
                    lection_za: item.lection_za,
                    practice_za: item.practice_za,
                    lab_za: item.lab_za,
                    outwork_za: item.outwork_za,
                    interactive_za: item.interactive_za,
                    practicePrepare_za: item.practicePrepare_za,
                }))
            }
            //очно-заочная
            else if (activeEducationGuidForm === this.edu_form_oz) {
                return part.data.map(item => ({
                    lection_oza: item.lection_oza,
                    practice_oza: item.practice_oza,
                    lab_oza: item.lab_oza,
                    outwork_oza: item.outwork_oza,
                    interactive_oza: item.interactive_oza,
                    practicePrepare_oza: item.practicePrepare_oza,
                }))
            }
        },
    },
    template: `
      <div class="themes">
      <div class="parent-resizable-overlay" v-if="showModalThemes">
        <vue-draggable-resizable :drag-cancel="'.drag-cancel'" :resizable="false" :x="1918 / 2 - 350" :w="700" :y="20"
                                 :parent="true" :z="999">
          <div class="modal-themes modal-themes--drag" v-show="showModalThemes" ref="modalThemes">
            <svg @click="closeModalThemes" class="drag-cancel modal-themes__close" width="30" height="30"
                 viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
                  fill="black" fill-opacity="0.54"/>
            </svg>
            <div class="modal-themes__block">
              <h3 v-if="!isEditTheme">Раздел</h3>
              <div class="switch-container switch-container--top15" v-if="!isEditTheme">
                <label class="switch drag-cancel">
                  <input type="checkbox" :disabled="!parts.length" v-model="isNewPart">
                  <span class="switch__slider switch__round"></span>
                </label>
                Добавить&nbsp;<b>новый</b>&nbsp;раздел
              </div>
              <fielded-select v-if="!isNewPart" class="mt-20 drag-cancel"
                              :label="!isEditTheme ? 'Раздел для добавления темы' : 'Раздел для переноса темы'"
                              placeholder="Выберите раздел" :items="getPartsSelect" v-model="partToAddTheme"
                              item-name="name_segment"></fielded-select>

              <fielded-input class="mt-20 drag-cancel" label="Название раздела" placeholder="Введите текст"
                             v-model="editedItem.name_segment" v-if="isNewPart && !partToAddTheme"></fielded-input>
            </div>
            <div class="modal-themes__block">
              <h3>Тема</h3>
              <fielded-input class="mt-20 drag-cancel" label="Тема" placeholder="Введите текст"
                             v-model="editedItem.data[0].name_segment"></fielded-input>
            </div>
            <div class="modal-themes__block">
              <h3>Содержание</h3>
              <fielded-textarea class="mt-20 drag-cancel" label="Содержание" placeholder="Введите текст"
                                v-model="editedItem.data[0].description">
              </fielded-textarea>
            </div>
            <div class="modal-themes__block">
              <h3>Вопросы к семинарским занятиям</h3>
              <fielded-textarea class="mt-20 drag-cancel" label="Вопросы к семинарским занятиям"
                                placeholder="Введите текст" v-model="editedItem.data[0][getEducationFormFields[6]]"
                                :disabled="isDisabledByPractice">
              </fielded-textarea>
            </div>
            <div class="modal-themes__block modal-themes__clocks">
              <div class="modal-themes__clocks-wrapper">
                <span>лекции</span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[0].value) || !Boolean(selectedForms[0].value - getAllLectureHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[0]]"
                       @change="changeHorseValue($event.target.value, 0, selectedForms[0].value, getAllLectureHorse)"
                       @keypress="onlyNumber">
              </div>

              <div class="modal-themes__clocks-wrapper">
                <span>практические<br>занятия</span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[1].value) || !Boolean(selectedForms[1].value - getAllPracticeHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[1]]"
                       @change="changeHorseValue($event.target.value, 1, selectedForms[1].value, getAllPracticeHorse)"
                       @keypress="onlyNumber">
              </div>

              <div class="modal-themes__clocks-wrapper">
                <span>лабораторные<br>работы </span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[2].value) || !Boolean(selectedForms[2].value - getAllLabHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[2]]"
                       @change="changeHorseValue($event.target.value, 2, selectedForms[2].value, getAllLabHorse)"
                       @keypress="onlyNumber">
              </div>

              <div class="modal-themes__clocks-wrapper">
                <span>Самостоятельная<br>работа</span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[3].value) || !Boolean(selectedForms[3].value - getAllOutworkHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[3]]"
                       @change="changeHorseValue($event.target.value, 3, selectedForms[3].value, getAllOutworkHorse)"
                       @keypress="onlyNumber">
              </div>

              <div class="modal-themes__clocks-wrapper">
                <span>в т.ч.<br>интерактивная<br>форма</span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[4].value) || !Boolean(selectedForms[4].value - getAllInteractiveHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[4]]"
                       @change="changeHorseValue($event.target.value, 4, selectedForms[4].value, getAllInteractiveHorse)"
                       @keypress="onlyNumber">
              </div>

              <div class="modal-themes__clocks-wrapper">
                <span>в т.ч.<br>практическая<br>подготовка</span>
                <input @focus="$event.target.select()"
                       :disabled="!Boolean(selectedForms[5].value) || !Boolean(selectedForms[5].value - getAllPracticePrepareHorse) && !isEditTheme"
                       type="text" class="square-input drag-cancel"
                       v-model="editedItem.data[0][getEducationFormFields[5]]"
                       @change="changeHorseValue($event.target.value, 5, selectedForms[5].value, getAllPracticePrepareHorse)"
                       @keypress="onlyNumber">
              </div>
            </div>
            <button @click="confirmModalThemes" class="btn-confirm btn--mt10 btn--self-start drag-cancel">Принять
            </button>
          </div>
        </vue-draggable-resizable>
      </div>
      <div class="themes__container">
        <div class="themes__block themes__forms">
          <div class="themes-title">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M17 10H1C0.45 10 0 10.45 0 11V17C0 17.55 0.45 18 1 18H17C17.55 18 18 17.55 18 17V11C18 10.45 17.55 10 17 10ZM4 16C2.9 16 2 15.1 2 14C2 12.9 2.9 12 4 12C5.1 12 6 12.9 6 14C6 15.1 5.1 16 4 16ZM17 0H1C0.45 0 0 0.45 0 1V7C0 7.55 0.45 8 1 8H17C17.55 8 18 7.55 18 7V1C18 0.45 17.55 0 17 0ZM4 6C2.9 6 2 5.1 2 4C2 2.9 2.9 2 4 2C5.1 2 6 2.9 6 4C6 5.1 5.1 6 4 6Z"
                  fill="#2F80ED"/>
            </svg>
            <span>Форма <br> обучения</span>
          </div>
          <div class="themes__forms-list">
            <button
                v-for="form in forms" :key="form.guidform"
                :class="{'btn-confirm': form.guidform === activeEducationGuidForm}"
                class="btn-discard btn--fs14 btn--padding btn--mt10"
                @click="selectEducationForm(form)"
            >
              {{ form.name }}
            </button>
          </div>
        </div>
        <div class="themes__block themes__time">
          <div class="times__graph">
            <div class="themes-title">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.99 2C6.47 2 2 6.48 2 12C2 17.52 6.47 22 11.99 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 11.99 2ZM12 20C7.58 20 4 16.42 4 12C4 7.58 7.58 4 12 4C16.42 4 20 7.58 20 12C20 16.42 16.42 20 12 20Z"
                    fill="#2F80ED"/>
                <path d="M12.5 7H11V13L16.25 16.15L17 14.92L12.5 12.25V7Z" fill="#2F80ED"/>
              </svg>
              <span>Счетчик <br>часов</span>
            </div>
            <span class="times__graph-text times__graph-text--height">по плану</span>
            <span class="times__graph-text">остаток</span>
          </div>
          <div class="times__calculate">
            <div class="times__names">
              <span>всего </span>
              <span v-for="times in setNewHeaders" v-html="times"></span>
            </div>
            <div class="times__hour">
              <span>{{ getAllHours }}</span>
              <span>{{ selectedForms[0].value }}</span>
              <span>{{ selectedForms[1].value }}</span>
              <span>{{ selectedForms[2].value }}</span>
              <span>{{ selectedForms[3].value }}</span>
              <span>{{ selectedForms[4].value }}</span>
              <span>{{ selectedForms[5].value }}</span>
            </div>
            <div class="times__distribution">
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="getRemainHorse === 0"
                    :value="getRemainHorse">
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[0].value || !Boolean(getRemainLectureHorse)"
                    :value="getRemainLectureHorse"
                >
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[1].value || !Boolean(getRemainPracticeHorse)"
                    :value="getRemainPracticeHorse"
                >
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[2].value || !Boolean(getRemainLabHorse)"
                    :value="getRemainLabHorse"
                >
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[3].value || !Boolean(getRemainOutworkHorse)"
                    :value="getRemainOutworkHorse"
                >
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[4].value || !Boolean(getRemainInteractiveHorse)"
                    :value="getRemainInteractiveHorse"
                >
              </div>
              <div>
                <input
                    type="number"
                    class="square-input"
                    readonly
                    :disabled="!selectedForms[5].value || !Boolean(getRemainPracticePrepareHorse)"
                    :value="getRemainPracticePrepareHorse"
                >
              </div>
            </div>
          </div>
        </div>
      </div>
      <h2 class="h2-title">Тематический план дисциплины</h2>
      <div class="theme-plan">
        <div class="theme-plan__header">
          <div class="theme-plan__header-name">
            название: раздел / тема
          </div>
          <div class="theme-plan__names-group">
            <span>всего<br>часов</span>
            <span>лекции</span>
            <span>практические<br>занятия</span>
            <span>лабораторные<br>занятия</span>
            <span>самостоятельная<br>работа</span>
            <span>в т.ч.<br>интерактивная <br>форма</span>
            <span>в т.ч.<br>практическая<br>подготовка</span>
          </div>
        </div>

        <accordion multiple>
          <accordion-item v-for="(item, i) in parts" :key="item.id">
            <template slot="accordion-trigger">
              <div class="theme-plan__wrapper">
                <div @click.stop v-if="!coDev">
                  <v-popover
                      offset="4"
                      placement="right"
                      class="mr-45"
                  >
                    <svg class="theme-edit" width="18" height="18" viewBox="0 0 18 18" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                      <path
                          d="M0 14V16H6V14H0ZM0 2V4H10V2H0ZM10 18V16H18V14H10V12H8V18H10ZM4 6V8H0V10H4V12H6V6H4ZM18 10V8H8V10H18ZM12 6H14V4H18V2H14V0H12V6Z"
                          fill="#2F80ED"/>
                    </svg>

                    <template slot="popover">
                      <div class="themes-actions">
                        <span v-close-popover class="themes-actions__item" @click.stop="editSection(i)">
                          Редактировать
                        </span>
                        <span
                            v-close-popover
                            class="themes-actions__item"
                            v-show="!item.data.length"
                            @click="deletePart(item.id)"
                        >
                          Удалить
                        </span>
                        <span v-close-popover class="themes-actions__item" @click.stop="duplicatePart(item.id)">Дублировать</span>
                      </div>
                    </template>
                  </v-popover>
                </div>
                <label class="rpd-checkbox-container" @click.stop v-if="!coDev">
                  <input
                      type="checkbox"
                      :checked="isPartChecked(item.id)"
                      :disabled="!item.data.length"
                      @change="selectAllThemesInPart(item.id)"
                  >
                  <span class="checkbox-checkmark"></span>
                </label>
                <h3>Раздел {{ i + 1 }}</h3>
                <p class="theme-plan__wrapper-description">{{ item.name_segment }}</p>
              </div>
              <div class="theme-plan__time-plan">
                <span>{{ partAllHours(item.data) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[0]) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[1]) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[2]) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[3]) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[4]) }}</span>
                <span>{{ partAllHours(item.data, getEducationFormFields[5]) }}</span>
              </div>
            </template>
            <template slot="accordion-content">
              <draggable
                  tag="div"
                  v-model="item.data"
                  v-bind="dragOptions"
              >
                <div class="theme-plan__part" v-for="(theme, idx) in item.data" :key="idx">
                  <div class="theme-plan__wrapper">
                    <v-popover
                        offset="4"
                        placement="right"
                        class="mr-45"
                        v-if="!coDev"
                    >
                      <svg class="theme-edit" width="18" height="18" viewBox="0 0 18 18" fill="none"
                           xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0 14V16H6V14H0ZM0 2V4H10V2H0ZM10 18V16H18V14H10V12H8V18H10ZM4 6V8H0V10H4V12H6V6H4ZM18 10V8H8V10H18ZM12 6H14V4H18V2H14V0H12V6Z"
                            fill="black" fill-opacity="0.54"/>
                      </svg>

                      <template slot="popover">
                        <div class="themes-actions">
                        <span v-close-popover class="themes-actions__item" @click.stop="editTheme(i,idx)">
                          Редактировать
                        </span>
                          <span v-close-popover class="themes-actions__item"
                                @click.stop="deleteTheme(theme.id)">Удалить</span>
                          <span v-close-popover class="themes-actions__item" @click.stop="duplicateTheme(i, theme.id)">Дублировать</span>
                        </div>
                      </template>
                    </v-popover>

                    <label class="rpd-checkbox-container" v-if="!coDev">
                      <input type="checkbox" :checked="isThemeChecked(theme.id)"
                             @change="selectThemeToDelete(theme.id)">
                      <span class="checkbox-checkmark"></span>
                    </label>
                    <p class="theme-plan__name">тема {{ i + 1 }}.{{ idx + 1 }}</p>
                    <p class="theme-plan__wrapper-description" @click="editTheme(i,idx)">
                      {{ theme.name_segment }}
                    </p>
                  </div>
                  <div class="theme-plan__time-plan">
                    <span>{{ themeAllHours(theme) }}</span>
                    <span>{{ theme[getEducationFormFields[0]] }}</span>
                    <span>{{ theme[getEducationFormFields[1]] }}</span>
                    <span>{{ theme[getEducationFormFields[2]] }}</span>
                    <span>{{ theme[getEducationFormFields[3]] }}</span>
                    <span>{{ theme[getEducationFormFields[4]] }}</span>
                    <span>{{ theme[getEducationFormFields[5]] }}</span>
                  </div>
                </div>
              </draggable>
            </template>
          </accordion-item>
        </accordion>
      </div>
      <button
          @click.stop="openModalsTheme"
          class="btn-confirm btn--rounded btn--mr10"
          v-if="!coDev"
      >
        Добавить
      </button>
      <button
          @click="removeThemes"
          :disabled="!themesToDelete.length"
          class="btn-confirm btn--rounded"
          v-if="!coDev"
      >
        Удалить темы
        <span v-show="themesToDelete.length">({{ themesToDelete.length || 0 }})</span>
      </button>

      <div class="modal-part-edit" v-show="modalPartEdit">
        <svg
            @click="closeModalPartEdit"
            class="modal-themes__close" width="30" height="30" viewBox="0 0 30 30" fill="none"
            xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <div class="modal-part-edit__item">
          <fielded-input
              label="Название раздела"
              v-model.trim="editedPart.name_segment"
          ></fielded-input>
        </div>
        <button class="btn-confirm btn--self-start" @click="savePart">сохранить</button>
      </div>

      <div class="modal-theme-info" v-show="modalThemeInfo">
        <svg
            @click="closeModalThemeInfo"
            class="modal-themes__close" width="30" height="30" viewBox="0 0 30 30" fill="none"
            xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <div class="modal-part-edit__item">
          {{ themeInfo }}
        </div>
      </div>
<div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1SfSI8n81q5aBNAyOSbSoGk0Ks-F3hMhl/view?usp=drive_link">Видеоинструкция - Темы</a></p>
  </div>
      </div>
    `
}