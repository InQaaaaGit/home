Vue.component('multiselect', window.VueMultiselect.default);

const librarianManager = new Vue({
  el: '#librarian-manager',
  data: () => ({
    showFilters: false,
    optionsFilters: [
      {id: 1, name: 'Все', filter: 'all'},
      {id: 2, name: 'Согласовано', filter: '1'},
      {id: 3, name: 'На согласовании', filter: '2'},
      {id: 4, name: 'Не согласовано', filter: '3'},
      {id: 5, name: 'В разработке', filter: '4'},
    ],
    selectedFilter:'',
    years: [

    ],
    directions: [

    ],
    educationLevels: [

    ],
    trainingLevels: [

    ],
    educationPrograms: [

    ],
    profiles:[],
    selectedYear: '',
    selectedDirection: '',
    selectedEducationLevel: '',
    selectedTrainingLevels: '',
    selectedEducationPrograms: '',
    selectedProfiles: '',
    tableColumns: [
      {
        text: 'Дисциплина',
        value: 'discipline',
        sortable: true
      },
      {
        text: 'Модули',
        value: 'modules',
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
        text: 'Преподаватель',
        value: 'developers',
      },
      {
        text: 'Статус',
        value: 'librarian_status',
        sortable: true
      }
    ],
    tableData: [

    ],
    isLoader: false
  }),
  created() {
    this.selectedFilter = this.optionsFilters[0];
    this.getListRPD();
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
        this.profiles.sort();
        this.profiles.sort(function (a, b) {
              if (a.value > b.value) {
                return 1;
              }
              if (a.value < b.value) {
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
    filteredTable() {
      return this.tableData.filter(item => {
        if(this.selectedFilter.filter === 'all') return item
        else return item.librarianStatus === this.selectedFilter.filter
      }).filter(item => {
        if(this.selectedYear !== null && this.selectedYear.value )
          return item.year === this.selectedYear.value
        else return item
      }).filter(item => {
        if(this.selectedDirection !== null && this.selectedDirection.value)
          return item.direction === this.selectedDirection.value
        else return item
      }).filter(item => {
        if(this.selectedEducationLevel !== null && this.selectedEducationLevel.value)
          return item.educationLevel === this.selectedEducationLevel.value
        else return item
      }).filter(item => {
        if(this.selectedTrainingLevels !== null && this.selectedTrainingLevels.value)
          return item.trainingLevel === this.selectedTrainingLevels.value
        else return item
      }).filter(item => {
        if(this.selectedProfiles !== null && this.selectedProfiles.value)
          return item.profile === this.selectedProfiles.value
        else return item
      }).filter(item => {
        if(this.selectedEducationPrograms !== null && this.selectedEducationPrograms.value)
          return item.discipline === this.selectedEducationPrograms.value
        else return item
      });
    },
    completedDisciplineLength(){
      return this.tableData.filter(item => item.librarianStatus === '1').length;
    },
    approvalDisciplineLength(){
      return this.tableData.filter(item => item.librarianStatus === '2').length;
    },
    developingDisciplineLength(){
      return this.tableData.filter(item => item.librarianStatus === '3').length;
    },
    notAllocatedDisciplineLength(){
      return this.tableData.filter(item => item.librarianStatus === '4').length;
    },
  },
  methods: {
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
      /*returnArray.sort(function (a, b) {
            if (a.value > b.value) {
              return 1;
            }
            if (a.value < b.value) {
              return -1;
            }
            return 0;

          }
      );*/
      return returnArray;
    },
    getListRPD(){
      const vm = this;
      vm.isLoader = true;
      require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

        var promises = ajax.call([
          {
            methodname: 'get_list_rpd_for_library_worker_on_specialty',
            args: {
            }
          }
        ]);
        promises[0].done((response) => {
          vm.tableData = _.uniqBy(response,"id");
          vm.years = vm.createFuckingFilter('year', vm.tableData);
          vm.trainingLevels = vm.createFuckingFilter('trainingLevel', vm.tableData);
          vm.educationPrograms = vm.createFuckingFilter('discipline', vm.tableData);
          vm.directions = vm.createFuckingFilter('direction', vm.tableData);
          vm.educationLevels = vm.createFuckingFilter('educationLevel', vm.tableData);

          vm.isLoader = false;
        }).fail(function (ex) {
          vm.isLoader = false;
          notification.exception(ex);
        });
      });
    },

    getStatusType(status){
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
})