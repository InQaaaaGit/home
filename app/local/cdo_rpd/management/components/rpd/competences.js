const competences = {
  props: {
    competence: {
      type: Array,
      required: true
    },
    coDev: {
      type:Boolean,
      default:false,
    }
  },
  data: () => ({

    editModal: false,
    editedCompetenceItem: {
      id: null,
      title: '',
      requirement: {
        know: ' ',
        beAbleTo: ' ',
        own: ' ',
      },
      submitted: null
    },
    defaultCompetenceItem: {
      id: null,
      title: '',
      requirement: {
        know: ' ',
        beAbleTo: ' ',
        own: ' ',
      },
      submitted: null
    }
  }),
  template: `
    <div class="competence">
        <h4>Для заполнения полей "Знать", "Уметь" и "Владеть" нажмите на наименование компетенции</h4>
      <ul class="competence_list">
        <li 
          v-for="competence in competence" :key="competence.id" 
          class="competence_item"
          @click.stop="editCompetence(competence)"
          >
          <svg class="competence_status"width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse 
              :class="{'competence_status--submitted': isCompletedCompetence(competence.requirement)}"
              cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#E0E0E0"/>
            <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75781 11.7539)" fill="white"/>
            <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
          </svg>
          <p class="competence_title">
            {{competence.title}}
          </p>
        </li>
      </ul>
      <div v-show="editModal" class="modal-edit-competence" v-click-outside="closeCompetenceModal">
          <svg @click="closeCompetenceModal" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="modal-themes__close"><path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"></path></svg>
          <!--<div class="modal-edit-competence_indicator"></div>-->
          <div class="modal-edit-competence_title">Перечень планируемых результатов обучения по дисциплине, соотнесенных с индикаторами достижения компетенций</div>
          <div class="modal-edit-competence_subtitle"> {{editedCompetenceItem.title}}</div>
          <div class="modal-edit-competence_container">
            <template v-if="!coDev">
            <fielded-textarea
              class="textarea-noresize"
              label="Знать"
              placeholder="Знать"
              v-model="editedCompetenceItem.requirement.know"
              
            >
            </fielded-textarea>
            </template>
            <div v-else>
              Знать: <br>
              {{editedCompetenceItem.requirement.know}}
            </div>
            <template v-if="!coDev">
            <fielded-textarea
              class="textarea-noresize"
              label="Уметь"
              placeholder="Уметь" 
              v-model="editedCompetenceItem.requirement.beAbleTo"
            >
            </fielded-textarea>
            </template>
            <div v-else>
              Уметь: <br>
              {{editedCompetenceItem.requirement.beAbleTo}}
            </div>
            <template v-if="!coDev">
            <fielded-textarea
              class="textarea-noresize"
              label="Владеть"
              placeholder="Владеть" 
              v-model="editedCompetenceItem.requirement.own"
            >
            </fielded-textarea>
            </template>
            <div v-else>
              Владеть: <br>
              {{editedCompetenceItem.requirement.beAbleTo}}
            </div>
          </div>
          <button 
            class="btn-confirm btn-confirm--competence"
            @click="submitCompetence"
            >принять</button>
      </div>
      <!--<div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1rT7tyfqcfIZB4AaYizHLs7681QaK2CcN/view?usp=drive_link">Видеоинструкция - Компетенции</a></p>
      </div>-->
    </div>
  `,
  methods: {
    submitCompetence(){
      const competenceIndex = this.competence.findIndex(item => item.id === this.editedCompetenceItem.id);
      this.competence.splice(competenceIndex, 1, this.editedCompetenceItem);
      this.editModal = false;
      this.editedCompetenceItem = _.cloneDeep(this.defaultCompetenceItem);
    },
    editCompetence(competence) {
      this.editedCompetenceItem = _.cloneDeep(competence);
      this.editModal = true;
    },
    closeCompetenceModal(){
      this.editedCompetenceItem = _.cloneDeep(this.defaultCompetenceItem);
      this.editModal = false;
    },
    isCompletedCompetence(requirement){
      return Object.values(requirement).every(item => Boolean(item.trim()))
    }
  }
}