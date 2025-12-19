const control = {
    components: {
        first,
        second,
        third,
        fourth,
        fifth
    },
    props: {
        criteriaList: {
            type: Object,
            required: true
        },
        questionsForDiscipline: {
            type: Object,
            required: true
        },
        controls: {
            type: Array,
            default: () => ([])
        },
        parts: {
            type: Array,
            required: true
        },
        competenceList: {
            type: Array,
            required: true
        },
        questions: {
            type: Array,
            required: false,
        },
        coDev: {
            type:Boolean,
            default:false,
        }
    },
    data: () => ({
        importTooltipText: 'Вы можете импортировать вопросы <br>общим списком из файла в формате docx.',
        selectCompetence: [],
        selectCompetenceTest: [],
        activeTab: null,
        modalAddQuestion: false,
        modalAddOffset: false
    }),
    watch: {
        // controls(val, oldVal) {
        //   this.activeTab = val[0]?.code;
        // }
    },
    methods: {
        getQuestionsForTemplate(code) {
            // возвращаем нужные вопросы для нужного раздела
            return this.questions.filter(controlQuestions => controlQuestions.code === code);
        },
        isComplete(code) {
            if (this.questionsForDiscipline?.[code]) {
                const arrItems = Object.values(this.questionsForDiscipline[code]);
                return !!arrItems.every(item => item.length);
            } else {
                let arrControlsFromAllThemes = [];
                this.parts.forEach(part => {
                    part.data.forEach(theme => {
                        if (theme.data.length)
                        arrControlsFromAllThemes.push(!!theme.data[code].length);
                    })
                })
                return !!arrControlsFromAllThemes.every(item => item === true);
            }
        }
    },
    template: `
    <div>
      <ul class="controls-tabs">
        <li 
          v-for="tab in controls" :key="tab.code"
          @click="activeTab = tab.code"
          class="controls-tabs__item"
          :class="{'controls-tabs__item--complete': tab.code === activeTab}"
          >
          <svg
            v-show="isComplete(tab.code)"
            class="controls-tabs__arrow-complete" width="21" height="21" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="12" fill="#007DFF"/>
            <path d="M9.50013 15.4749L6.02513 11.9999L4.8418 13.1749L9.50013 17.8332L19.5001 7.8332L18.3251 6.6582L9.50013 15.4749Z" fill="white"/>
          </svg>
          <span v-html="tab.name"></span>
        </li>
      </ul>
      
      <component 
        v-for="component in controls" 
        :is="component.template"
        :key="component.code"
        v-if="component.code === activeTab"
        :parts="parts"
        :tab="activeTab"
        :competence-list="competenceList"
        :questions-list="getQuestionsForTemplate(component.code)"
        :questions-for-discipline="questionsForDiscipline"
        :criteria-content="criteriaList[component.code]"
        :import-tooltip-text="importTooltipText"
        :co-dev="coDev"
        @on-criteria-content-change="$emit('on-criteria-content-change', $event)"
        @add-questions-to-theme="$emit('add-questions-to-theme', $event)"
        @delete-question-theme="$emit('delete-question-theme', $event)"
        @add-question-to-list="$emit('add-question-to-list', $event)"
        @remove-question-from-list="$emit('remove-question-from-list', $event)"
        @change-text-question="$emit('change-text-question', $event)"
        @change-text-answer="$emit('change-text-answer', $event)"
        @select-competence-question="$emit('select-competence-question', $event)"
        @delete-competence-question="$emit('delete-competence-question', $event)"
        @add-from-unallocated-part="$emit('add-from-unallocated-part', $event)"
        @delete-from-unallocated-part="$emit('delete-from-unallocated-part', $event)"
        @import-questions-list="$emit('import-questions-list', $event)"
        @add-imported-list="$emit('add-imported-list', $event)"
      >
      </component>
       <div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1PRIENf3cC4TILs1NxUMvTIQjgB_f2qg9/view?usp=drive_link">Видеоинструкция - Вопросы к зачету</a></p>
<p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1wCBOlrxlEqwJom05yphGzpUwYgBQ9kuU/view?usp=drive_link">Видеоинструкция - Тесты</a></p>
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1f_KYSujbI-ZzpRHw3ILHYPTO8BUfvNYa/view?usp=drive_link">Видеоинструкция - Лабораторные работы</a></p>
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1C3IdvbxmhJP24zLOr3UcAWXg298RuLmF/view?usp=drive_link">Видеоинструкция - Курсовая работа</a></p>
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1QwN4iqglJl2sUGn5CQr_9iZGTd6oi9yS/view?usp=drive_link">Видеоинструкция - Импорт тестов</a></p>
</div>

    </div>
  `
}