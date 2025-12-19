Vue.component('criteria-modal', {
  props: {
    isOpen: {
      type: Boolean,
      required: false,
      default: false
    },
    content: {
      type: String,
      default: ''
    },
    coDev: {
      type:Boolean,
      default:false,
    }
  },
  data: () => ({}),
  methods: {
    closeCriteriaModal() {
      this.$emit('close-criteria-modal');
    },
    onCriteriaContentChange(e){
      this.$emit('on-criteria-content-change', e.html);
    }
  },
  template: `
    <div class="modal-controls" v-show="isOpen">
      <svg @click="closeCriteriaModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
      </svg>
      <div class="edit-question-number">
        Критерии и шкала оценивания
      </div>
      <div class="edit-question-name"></div>
      <template v-if="!coDev">
      <quill-editor class="quill-height quill-overflow mb15" :content="content" @change="onCriteriaContentChange($event)"/>
      <button class="btn-confirm btn--self-start" @click="closeCriteriaModal">принять</button>
      </template>
      <div v-else v-html="content">
        
      </div>
    </div>
  `,
})