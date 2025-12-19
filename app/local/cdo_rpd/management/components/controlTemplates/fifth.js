const fifth = {
  data: () => ({
    competenceList: [
      {
        id: 1,
        title: 'ОПК-1',
        requirement: {
          know: '1',
          beAbleTo: '2',
          own: '3',
        },
      },
      {
        id: 2,
        title: 'ОПК-2',
        requirement: {
          know: '',
          beAbleTo: '',
          own: '',
        },
      },
      {
        id: 3,
        title: 'ОПК-3',
        requirement: {
          know: '',
          beAbleTo: '',
          own: '',
        },
      },{
        id: 15125,
        title: 'ОПК-1',
        requirement: {
          know: '1',
          beAbleTo: '2',
          own: '3',
        },
      },
      {
        id: 12515,
        title: 'ОПК-2',
        requirement: {
          know: '',
          beAbleTo: '',
          own: '',
        },
      },
      {
        id: 15215,
        title: 'ОПК-3',
        requirement: {
          know: '',
          beAbleTo: '',
          own: '',
        },
      }
    ],
    selectCompetenceQuestionForTasks: [],
    modalAddOffset: false
  }),
  template: `
    <div>
      <div class="controls-content">
        <table class="table-controls">
          <thead>
            <th>Компетенция</th>
            <th>Тема</th>
            <th>Задания</th>
            <th>
<!--              <a href="#" @click.prevent="" class="btn-import">-->
<!--                импорт вопросов-->
<!--                <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                  <path d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z" fill="#2F80ED"/>-->
<!--                </svg>-->
<!--              </a>-->
            </th>
          </thead>
          <tbody>
            <tr v-for="i in 4" :key="i">
              <td>
                <div class="controls-content__multiselect-wrapper">
                  <multiselect 
                    v-model="selectCompetenceQuestionForTasks" 
                    :options="competenceList" 
                    :preserve-search="true"
                    track-by="id" 
                    label="title"
                    multiple 
                    :close-on-select="false"
                    select-label="Выбрать" 
                    selected-label="Выбрано" 
                    placeholder="Выберите" 
                    deselect-label="Удалить"
                    select-group-label="Выбрать группу" 
                    deselect-group-label="Удалить группу" 
                    >
                    <span slot="noResult">Ничего не найдено</span>
                    <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length && !isOpen">{{ values.length }} выбрано</span></template>
                  </multiselect>
                </div>
              </td>
              <td>
                <div class="table-controls__group">
                  <div class="table-controls__text">
                    <span class="table-controls__theme-number">Тема {{i}}.</span>
                    <span class="table-controls__theme-name">Понятие и сущность авиастроения</span>
                  </div>
                  <button class="btn-discard" @click="modalAddOffset = true">ЗАПОЛНИТЬ</button>
                </div>
              </td>
              <td colspan="2">
                <fielded-input
                  placeholder="Заполнить"
                  readonly
                ></fielded-input>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- добавление заданий на Задания (задачи) к зачету-->
      <div class="modal-controls" v-show="modalAddOffset">
        <svg @click="modalAddOffset = false" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
        </svg>
        <div class="edit-question-number">
          Тема 1.
        </div>
        <div class="edit-question-name">
          Понятие и сущность авиастроения
        </div>
        <div class="questions-list">
          <div class="questions-list__item" v-for="i in 4" :key="i">
            <input type="checkbox" class="mr-10">
            
            <div class="questions-list__item-wrapper">
              <p class="questions-list__subtitle">Задание {{i}}</p>
              <quill-editor class="quill-height mb15"/>
            </div>
            
            <svg class="delete-question" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z" fill="#FF3A40"/>
            </svg>
          </div>
        </div>
        <a href="#" @click.prevent="" class="btn-add question-add">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15.1943 8.80579L15.1943 7.19435L8.80572 7.19435L8.80572 0.805721H7.19428L7.19428 7.19435L0.805652 7.19435L0.805653 8.80579L7.19428 8.80579V15.1944H8.80572V8.80579L15.1943 8.80579Z" fill="#2F80ED"/>
          </svg>
          Добавить
        </a>
        <button class="btn-confirm btn--self-start">принять</button>
      </div>
    </div>
  `
}