Vue.component('progress-bar', {
  props: {
    allDisciplines:{
      type: Number,
      required: true
    },
    currentDisciplines:{
      type: Number,
      required: true
    },
    color: {
      type: String,
      required: false,
      default: '#0D45CA'
    },
    title: {
      type: String,
      required: true
    }
  },
  computed: {
    getPercentDiscipline(){
      return Math.round((this.currentDisciplines / this.allDisciplines) * 100);
    }
  },
  template: `
    <div class="root-progress">
      <span class="root-progress__text">{{title}}</span>
      <div class="root-progress__track">
        <div 
          :style="{width: getPercentDiscipline +'%', backgroundColor: color}"
          class="root-progress__bar">
        
        </div>
      </div>
      <div class="root-progress__count">{{currentDisciplines}} / {{allDisciplines}}</div>
    </div>
  `,
});
