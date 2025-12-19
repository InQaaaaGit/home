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

const librarian = new Vue({
  el: '#librarian',
  data: () => ({
    showFilters: false,
    optionsFilters: [
      {id: 1, name: 'Все', filter: 'all'},
      {id: 2, name: 'Распределены', filter: '1'},
      {id: 3, name: 'Не распределены', filter: '2'},
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
    selectedYear: '',
    selectedDirection: '',
    selectedEducationLevel: '',
    selectedTrainingLevels: '',
    selectedEducationPrograms: '',
    selectedCode: '',
    codes:[],
    tableColumns: [

      {
        text: 'Шифр',
        value: 'code',
        sortable: true,
        sortFun: function (list, sortOrder) {
          function getStringCode(strCode) {
            const codeArr = strCode.split('.');
            return [codeArr[1],codeArr[0],codeArr[2]].join('');
          }
          const order = sortOrder['code'] || 1;
          return _.cloneDeep(list).sort((a, b) => {
            a = getStringCode(a.code);
            b = getStringCode(b.code);
            return (a === b ? 0 : a > b ? 1 : -1) * order;
          });
        }
      },
      {
        text: 'Направление',
        value: 'direction',
        sortable: true
      },
      {
        text: 'Ответственный',
        value: 'librarian',
      },
      {
        text: 'Статус',
        value: 'status',
        sortable: true
      }
    ],
    tableData: [
    ],
    userList: [

    ]
  }),
  created() {
    this.selectedFilter = this.optionsFilters[0];
    this.getIInfo();
    this.getWorkerList();

  },
  computed: {
    filteredTable() {
      return this.tableData.filter(item => {
        if(this.selectedFilter.filter === 'all') return item
        else if(this.selectedFilter.filter === '1')
          return item.librarian
        else if(this.selectedFilter.filter === '2')
          return !item.librarian
      })
       .filter(item => {
        if(this.selectedDirection !== null && this.selectedDirection.value)
          return item.direction === this.selectedDirection.value
        else return item
      }).filter(item => {
        if(this.selectedEducationLevel !== null && this.selectedEducationLevel.value)
          return item.educationLevels === this.selectedEducationLevel.value
        else return item
      }).filter(item => {
        if(this.selectedCode !== null && this.selectedCode.value)
          return item.code === this.selectedCode.value
        else return item
      }).filter(item => {
        if(this.selectedTrainingLevels !== null && this.selectedTrainingLevels.value)
          return item.trainingLevel === this.selectedTrainingLevels.value
        else return item
      });
    },
    completedDisciplineLength(){
      return this.tableData.filter(item => item.librarian).length;
    },
    notAllocatedDisciplineLength(){
      return this.tableData.filter(item => !item.librarian).length;
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
      returnArray.sort((el, el2) =>
          el2.value - el.value
      );
      return returnArray;
    },
    changeDeveloper(item) {
      require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

        var promises = ajax.call([
          {
            methodname: 'add_worker_on_special',
            args: {
              id:  item.librarian.id,
              spec: item.id.trim()
            }
          }
        ]);
        promises[0].done((response) => {

        }).fail(function (ex) {

          notification.exception(ex);
        });
      });
    },
    getIInfo() {
      const vm = this;
      require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

        var promises = ajax.call([
          {
            methodname: 'get_list_specialities_for_distribution',
            args: {
            }
          }
        ]);
        promises[0].done((response) => {
          vm.tableData = response;
          vm.trainingLevels = vm.createFuckingFilter('trainingLevel', vm.tableData);
          vm.directions = vm.createFuckingFilter('direction', vm.tableData);
          vm.educationLevels = vm.createFuckingFilter('educationLevels', vm.tableData);
          vm.directions.sort(function (a, b) {
                if (a.value > b.value) {
                  return 1;
                }
                if (a.value < b.value) {
                  return -1;
                }
                return 0;

              }
          );
          vm.codes = vm.createFuckingFilter('code', vm.tableData);
          vm.codes.sort(function (a, b) {
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
          notification.exception(ex);
        });
      });
    },
    getWorkerList(){
      const vm = this;
      require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

        var promises = ajax.call([
          {
            methodname: 'get_list_library_workers',
            args: {

            }
          }
        ]);
        promises[0].done((response) => {
          vm.userList = response;
        }).fail(function (ex) {
          notification.exception(ex);
        });
      });
    }
  }
})