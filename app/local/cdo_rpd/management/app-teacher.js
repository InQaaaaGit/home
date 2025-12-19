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
const appTeacher = new Vue({
    el: '#appTeacher',
    data: () => ({
        showFilters: false,
        optionsFilters: [
            {id: 1, name: 'Все', filter: 'all'},
            {id: 2, name: 'Новые', filter: '2'},
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
                text: 'Тип/модуль',
                value: 'type',
                sortable: true
            },
            {
                text: 'Год',
                value: 'year',
                align: 'center',
                sortable: true
            },
            {
                text: 'Ваш статус',
                value: 'developer',
                sortable: true
            },
            {
                text: 'Статус',
                value: 'approval',
            },
            {
                text: ' ',
                value: 'info'
            },
            {
                text: ' ',
                value: 'file'
            }
        ],
        tableData: [],
        showEventsModal: false,
        modalInfo: [],
        disciplineSearch: '',
        years: [],
        directions: [],
        educationLevels: [],
        trainingLevels: [],
        educationPrograms: [],
        selectedYear: '',
        selectedDirection: '',
        selectedEducationLevel: '',
        selectedTrainingLevels: '',
        selectedEducationPrograms: '',
        selectedProfiles: '',
        isLoader: false
    }),
    computed: {
        filteredTable() {
            return this.tableData.filter(item => {
                if (this.selectedFilter.filter === 'all') return item
                else return item.librarianStatus === this.selectedFilter.filter
            }).filter(item => {
                if (this.selectedYear !== null && this.selectedYear.value)
                    return item.year === this.selectedYear.value
                else return item.year
            }).filter(item => {
                if (this.selectedDirection !== null && this.selectedDirection.value)
                    return item.direction === this.selectedDirection.value
                else return item.direction
            }).filter(item => {
                if (this.selectedEducationLevel !== null && this.selectedEducationLevel.value)
                    return item.educationLevel === this.selectedEducationLevel.value
                else return item.educationLevel
            }).filter(item => {
                if (this.selectedTrainingLevels !== null && this.selectedTrainingLevels.value)
                    return item.trainingLevel === this.selectedTrainingLevels.value
                else return item.trainingLevel
            }).filter(item => {
                if (this.selectedProfiles !== null && this.selectedProfiles.value)
                    return item.profile === this.selectedProfiles.value
                else if ( item.profile === "") {
                    return true;
                } else return item.profile
            }).filter(item => {
                if (this.selectedEducationPrograms !== null && this.selectedEducationPrograms.value)
                    return item.discipline === this.selectedEducationPrograms.value
                else return item.discipline
            })
            /*return this.tableData.filter(item => {
              if(this.selectedFilter.filter === 'all') return item
              else return item.newChanges !== null
            }).filter(item => {
              const arrFilters = [
                this.selectedYear.value,
                this.selectedDirection.value,
                this.selectedEducationLevel.value,
                this.selectedTrainingLevels.value,
                this.selectedEducationPrograms.value
              ].filter(Boolean)
              if(arrFilters.length) return Object.values(item).some(el => arrFilters.includes(el))
              else return item
            });*/
        },
        completedDisciplineLength() {
            return this.tableData.filter(item => {
                return item.status==='1';
            }).length;
        },
        approvalDisciplineLength() {

            return this.tableData.filter(item => {
                return item.status==='2';
            }).length;
        },
        inDevelop() {
            return this.tableData.filter(item => {
                return item.status==='3';
            }).length;
        },


    },
    created() {
        this.getMyRPD();
        this.selectedFilter = this.optionsFilters[0];
    },
    methods: {
        disagree(item) {
            if (item.info.length) {
                return item.info[item.info.length-1].event.includes('Отклонено') ? 'red' : '#2F80ED';
            }
            return '#2F80ED';
        },
        writeStatus(status) {
            switch (status) {
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
                case '3':
                    return 'text-primary';
                    break;
                case '2':
                    return 'text-warning';
                    break;
                case '1':
                    return 'text-success';
                    break;
            }
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
            return returnArray;
        },
        async getMyRPD() {
            const vm = this;
            vm.isLoader = true;
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    //notification.init(1);

                    var promises = ajax.call([
                        {
                            methodname: 'get_rpd_list_by_user_id_from_1c',
                            args: {}
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.tableData = response;
                        vm.years = vm.createFuckingFilter('year', vm.tableData);
                        vm.trainingLevels = vm.createFuckingFilter('trainingLevel', vm.tableData);
                        vm.educationPrograms = vm.createFuckingFilter('discipline', vm.tableData);
                        vm.directions = vm.createFuckingFilter('direction', vm.tableData);
                        vm.educationLevels = vm.createFuckingFilter('educationLevel', vm.tableData);
                        vm.profiles = vm.createFuckingFilter('profile', vm.tableData);
                        vm.isLoader = false;
                    }).fail(function (ex) {
                        vm.isLoader = false;
                        notification.exception(ex);
                    });
                });
        },
        closeModal() {
            this.showEventsModal = false;
        },
        showInfo(eventsList) {
            this.modalInfo = eventsList;
            this.showEventsModal = true;
        },
        getStatusType(status) {
            switch (status) {
                //зеленый
                case '1':
                    return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#27AE60"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                    break
                //оранжевый
                case '2':
                    return `<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#F2994A"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          `
                    break
                //красный
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
        },
        getApprovalStatus(status) {
            switch (status) {
                case 0:
                    return {'approval__item--red': true}
                    break
                case 1:
                    return {'approval__item--orange': true}
                    break
            }
        },
        isDownloadLinkActive(arrStatuses) {
            return !arrStatuses.every(item => item === 2);
        }
    }
})