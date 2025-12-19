const targetAndTasks = {
    props: {
        targetText: {
            type: String,
            required: false
        },
        tasksText: {
            type: String,
            required: false
        },
        coDev: {
            type:Boolean,
            default:false,
        }
    },
    data: () => ({

        tasksDiscipline: ''
    }),
    methods: {
        onTargetDiscipline(e) {
            this.$emit('target-discipline', e.html);
        },
        onTasksDiscipline(e) {
            this.$emit('tasks-discipline', e.html);
        },
    },
    template: `
    <div>
      <div class="quill-wrapper">
        <div class="quill-wrapper_title">Цели освоения дисциплины:</div>
        <template v-if="coDev">
          <div v-html="targetText"></div>
        </template>
        <template v-else>
          <quill-editor class="quill-height"

                        :content="targetText"
                        @change="onTargetDiscipline($event)"
          />
        </template>
        
      </div>
      <div class="quill-wrapper">
        <div class="quill-wrapper_title">Задачи освоения дисциплины:</div>
        <template v-if="coDev">
          <div v-html="tasksText"></div>
        </template>
        <template v-else>
        <quill-editor class="quill-height" 
            
            :content="tasksText" 
             @change="onTasksDiscipline($event)"
        
        />
        </template>
      </div>
      <div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1k8DJ4MDYVHcCjMmPBFl4PaW318dui5is/view?usp=drive_link">Видеоинструкция - Заполнение раздела "Цель и задачи"</a></p>
<p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1p-6o9SaonOUczKi26Ig2JXgo71RTTlBB/view?usp=drive_link">Видеоинструкция - Импорт РПД при несовпадении компетенций</a></p>
<p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1dul8mONcYqAJadUeBTXUng3X3OjmCeSn/view?usp=drive_link">Видеоинструкция - Импорт РПД при совпадении компетенций и часов</a></p>
 </div>
    </div>
  `
}