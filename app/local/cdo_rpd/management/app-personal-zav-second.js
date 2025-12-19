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

const appPersonalZavSecond = new Vue({
  el: '#appPersonalZavSecond',
  data: () => ({
    optionsFilters: [
      {id: 1, name: 'Все', filter: 'all'},
      {id: 2, name: 'Новые', filter: '2'},
    ],
    selectedFilter:'',
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
        text: 'Курс',
        value: 'course',
        sortable: true
      },
      {
        text: 'Разработчик',
        value: 'developer',
        sortable: true
      },
      {
        text: 'Статус',
        value: 'status',
        sortable: true
      },
      {
        text: 'Согласование',
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
    tableData: [
      {
        id: '123-3123',
        discipline: 'Управление рисками в сложных производственно-технологических системах',
        direction: '27.04.03 Системный анализ и управление',
        course: '2',
        developer: 'Санников Игорь Алексеевич',
        status: '1',
        approval:{
          rpd: 2,
          fos: 2
        },
        info: [
          {date: '21.04.2021', event: 'Назначен разработчик'},
          {date: '21.04.2021', event: 'Поступило на согласование'},
          {date: '21.04.2021', event: 'Отправлено на доработку'},
          {date: '21.04.2021', event: 'Поступило на согласование'},
          {date: '21.04.2021', event: 'Согласованно'},
        ],
        file: 'file link',
        newChanges: null
      },
      {
        id: '223-3123',
        discipline: 'Управление качеством и сертификация изделий авиационной техники',
        direction: '15.04.03 Автоматизация технологических процессов и производств',
        course: '1',
        developer: 'Торбин Сергей Викторович',
        status: '2',
        approval:{
          rpd: 1,
          fos: 2
        },
        info: [
          {date: '21.04.2021', event: 'Назначен разработчик1'},
          {date: '21.04.2021', event: 'Поступило на согласование1'},
          {date: '21.04.2021', event: 'Отправлено на доработку1'},
          {date: '21.04.2021', event: 'Поступило на согласование1'},
          {date: '21.04.2021', event: 'Согласованно1'},
        ],
        file: 'file link',
        newChanges: true
      },
      {
        id: '323-3123',
        discipline: 'Технология самоорганизации личности',
        direction: '24.03.04 Авиастроение',
        course: '2',
        developer: 'Торбин Сергей Викторович',
        status: '2',
        approval:{
          rpd: 0,
          fos: 0
        },
        info: [
          {date: '21.04.2021', event: 'Назначен разработчик2'},
          {date: '21.04.2021', event: 'Поступило на согласование2'},
          {date: '21.04.2021', event: 'Отправлено на доработку2'},
          {date: '21.04.2021', event: 'Поступило на согласование2'},
          {date: '21.04.2021', event: 'Согласованно2'},
        ],
        file: 'file link',
        newChanges: true
      },
    ],
    showEventsModal: false,
    modalInfo: [],
    disciplineSearch: '',
  }),
  computed:{
    completedDisciplineLength(){
      return this.tableData.filter(item => item.status === '1').length;
    },
    approvalDisciplineLength(){
      return this.tableData.filter(item => item.status === '2').length;
    },
    filteredTable() {
      return this.tableData.filter(item => {
        if(this.selectedFilter.filter === 'all')  return item
        else return item.newChanges !== null
      });
    }
  },
  created(){
    this.selectedFilter = this.optionsFilters[0];
  },
  methods:{
    closeModal(){
      this.showEventsModal = false;
    },
    showInfo(eventsList){
      this.modalInfo = eventsList;
      this.showEventsModal = true;
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
    },
    getApprovalStatus(status){
      switch (status) {
        case 0:
          return {'approval__item--red': true}
          break
        case 1:
          return {'approval__item--orange': true}
          break
      }
    },
    isDownloadLinkActive(arrStatuses){
      return !arrStatuses.every(item => item === 2);
    }
  }
})