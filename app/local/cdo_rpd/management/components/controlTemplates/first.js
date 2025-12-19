const createTemplate = () => ({
    id: uuidv4(),
    selectedValue: [],
    questionDescription: ''
});

const first = {
    props: {
        parts: {
            type: Array,
            required: true,
        },
        tab: {
            type: String,
            required: true
        },
        competenceList: {
            type: Array,
            required: true
        },
        questionsList: {
            type: Array,
            required: true
        },
        criteriaContent: {
            type: String,
            required: true,
            default: ''
        },
        importTooltipText: {
            type: String,
            required: false,
            default: ''
        },
        coDev: {
            type: Boolean,
            default: false,
        }

    },
    data: () => ({
        selectCompetence: [],
        modalAddQuestion: false,
        selectedQuestionToTheme: [],
        editedPartIndex: null,
        editedThemeIndex: null,
        editedPartID: null,
        editedThemeID: null,
        editedThemeName: '',
        editedThemeQuestions: [],
        postFileLoader: false,

        showCriteriaModal: false
    }),
    watch: {
        questionsClone: {
            handler: function (value, oldValue) {

                if (value[0]?.questions.length === oldValue[0]?.questions.length) {
                    const changedQuestionID = _.differenceWith(value[0]?.questions, oldValue[0]?.questions, _.isEqual)[0]?.id;
                    const currentThemeQuestionsList = this.parts[this.editedPartIndex]?.data[this.editedThemeIndex]?.data[this.tab] ?? [];

                    if (currentThemeQuestionsList.some(item => item.id === changedQuestionID)) {
                        this.$emit('delete-question-theme',
                            {
                                editedPartID: this.editedPartID,
                                editedThemeID: this.editedThemeID,
                                tab: this.tab,
                                questionId: changedQuestionID
                            }
                        )
                    }
                }
            },
            deep: true
        }
    },
    computed: {
        getAllQuestionsSelected() {
            // вопросы в текущей теме
            const selectedTheme = this.parts?.[this.editedPartIndex]?.data?.[this.editedThemeIndex]?.data[this.tab] ?? [];

            //все выбранные вопросы в темах
            let selectedAllThemesInControl = [];
            this.parts.forEach(part => {
                part.data.forEach(themes => {
                    selectedAllThemesInControl = selectedAllThemesInControl.concat(themes.data[this.tab]);
                })
            });

            //вопросы, которые выбраны, исключая вопросы текущей темы
            const questionsWithoutCurrentTheme = _.differenceBy(selectedAllThemesInControl, selectedTheme, 'id');

            return _.differenceBy(this.questionsList[0]?.questions, questionsWithoutCurrentTheme, 'id');
        },

        questionsClone() {
            return _.cloneDeep(this.questionsList);
        }
    },
    methods: {
        getBase64(file) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () {
                    resolve(reader.result);
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },
        async sendImport(file) {
            var promise = this.getBase64(file);
            let filebase64 = await promise;
            let this_ = this;
            require(['core/ajax', 'core/notification'], function (ajax, notification) {
                // window.btoa()
                var promises = ajax.call([
                    {
                        methodname: 'read_import_file',
                        args: {
                            file: filebase64,
                            filename: file.name
                        }
                    }
                ]);

                promises[0].done((response) => {
                    console.log(response);
                    this_.$emit('import-questions-list', {
                        questions: response,
                        tab: this_.tab,
                    });
                    this_.$toast.open({
                        message: `Успшено импортировано!`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                }).fail(function (ex) {
                    notification.exception(ex);
                });
            });
        },
        onCriteriaContentChange(e) {
            this.$emit('on-criteria-content-change', {
                text: e,
                tab: this.tab
            })
        },
        competenceChosen(competenceListInQuestion, competence) {
            return competenceListInQuestion.some(
                value => {
                    return value.id === competence;
                }
            );
        },
        onChooseCompetence(competence, questionId, checkedC) {
            console.log(checkedC);
            if (checkedC.target.checked) {
                this.onControlSelect(competence, questionId);
            }
            if (!checkedC.target.checked) {
                this.onControlRemove(competence, questionId);
            }
        },
        onControlSelect(competence, questionId) {
            this.$emit('select-competence-question', {
                competence: _.cloneDeep(competence),
                questionId: questionId,
                tab: this.tab
            });
        },
        onControlRemove(competence, questionId) {
            this.$emit('delete-competence-question', {
                competence: _.cloneDeep(competence),
                questionId: questionId,
                tab: this.tab
            });
        },
        onEditorChange(e, questionId) {
            this.$emit('change-text-question', {
                tab: this.tab,
                questionId: questionId,
                text: e.html,
            })
        },
        getQuestionIndex(questionId) {

            return this.questionsList[0]?.questions.findIndex(item => item.id === questionId) + 1;
        },
        deleteQuestion(questionId) {
            this.$emit('delete-question-theme',
                {
                    editedPartID: this.editedPartID,
                    editedThemeID: this.editedThemeID,
                    tab: this.tab,
                    questionId: questionId
                }
            );

            this.$emit('remove-question-from-list',
                {
                    tab: this.tab,
                    question: questionId
                });

        },
        addQuestion() {
            const $questionsList = this.$refs.questionsList;
            const questionTemplate = createTemplate();

            this.$emit('add-question-to-list',
                {
                    tab: this.tab,
                    question: _.cloneDeep(questionTemplate)
                })
            this.$nextTick(() => {
                $questionsList.scrollTop = $questionsList.scrollHeight;
            });
        },
        selectQuestion(question) {
            this.$emit('add-questions-to-theme',
                {
                    editedPartID: this.editedPartID,
                    editedThemeID: this.editedThemeID,
                    tab: this.tab,
                    question: _.cloneDeep(question)
                }
            );
        },
        openModalAddQuestion(partIndex, themeIndex, partID, themeID, themeName) {

            this.editedPartIndex = partIndex;
            this.editedThemeIndex = themeIndex;
            this.editedPartID = partID;
            this.editedThemeID = themeID;
            this.editedThemeName = themeName;


            this.modalAddQuestion = true;
        },

        closeModalAddQuestion() {
            this.editedPartIndex = null;
            this.editedThemeIndex = null;
            this.editedPartID = null;
            this.editedThemeID = null;
            this.editedThemeName = '';
            this.selectedQuestionToTheme = [];

            this.modalAddQuestion = false;
        },
        isChecked(question) {
            if (this.modalAddQuestion) {
                const questionsInTheme = this.parts[this.editedPartIndex].data[this.editedThemeIndex].data[this.tab];
                return questionsInTheme.some(item => item.id === question.id);
            }
        },
        displayResult(result) {

            let arrayQuestions = result.value.split("<li>");
            arrayQuestions.shift();
            arrayQuestions = arrayQuestions.map(item => item.replace("</li>", ""));
            arrayQuestions = arrayQuestions.map(item => item.replace("</ol>", ""));
            let summaryArray = [];
            arrayQuestions.forEach(item => {
                let question = {};
                question.questionDescription = item;
                question.id = uuidv4();
                question.selectedValue = [];
                summaryArray.push(question);
            });
            this.$emit('import-questions-list', {
                questions: summaryArray,
                tab: this.tab,
            });

        },
        readFileInputEventAsArrayBuffer(event, callback) {
            var file = event.target.files[0];

            var reader = new FileReader();

            reader.onload = function (loadEvent) {
                var arrayBuffer = loadEvent.target.result;
                callback(arrayBuffer);
            };

            reader.readAsArrayBuffer(file);
        },
        postFile(e) {
            this.readFileInputEventAsArrayBuffer(e, (arrayBuffer) => {
                mammoth
                    .convertToHtml({arrayBuffer: arrayBuffer})
                    .then(this.displayResult)
                    .done();
            });
        },
        showNumer(tab, question) {
            let listContents = [];
            this.parts.forEach(part => {
                part.data.forEach(theme => {

                    listContents.push(theme.data[tab]);
                });
            });
            let consolidateArray = _.flatten(listContents);
            let i = 0;
            let number = 1;
            consolidateArray.forEach(element => {
                element.sort = i++;
                if (element === question) {
                    number = i;
                }
            });
            return number;

        }
    },
    template: `
    <div>
      <div class="controls-content">
        <table class="table-controls">
          <thead>
            <th>Тема</th>
            <th>Задания</th>
            <th>
              <div class="lds-ellipsis" v-show="postFileLoader"><div></div><div></div><div></div><div></div></div>
              <form enctype="multipart/form-data" v-show="!postFileLoader">
                <label class="btn-import" v-tooltip="{content: importTooltipText}"
                       v-if="!coDev"
                  >
                  импорт
                  <input type="file" @change="postFile">
                  <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z" fill="#2F80ED"/>
                  </svg>
                </label>
              </form>
              
            </th>
          </thead>
          <tbody v-for="(part,i, ii=1) in parts" :key="parts.id">
            <tr v-for="(theme, idx) in part.data" :key="theme.id">
                
              <td>
                <div class="table-controls__group">
                  <div class="table-controls__text">
                    <span class="table-controls__theme-number">Тема {{i + 1}}.{{idx + 1}}.</span>
                    <span class="table-controls__theme-name">{{theme.name_segment}}</span>
                  </div>
                  <button 
                      class="btn-discard" 
                      @click.stop="openModalAddQuestion(i, idx, part.id, theme.id, theme.name_segment)"
                      v-if="!coDev"
                  >
                    ЗАПОЛНИТЬ
                  </button>
                </div>
              </td>
              
              <td colspan="2">
                <ul class="added-questions-list" v-if="theme.data[tab].length">
                  <li class="added-questions-list__item" v-for="(question,i) in theme.data[tab]" >
                    Вопрос {{showNumer(tab, question)}}
                  </li>
                </ul>
                <p v-else>Не заполнены</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="modal-controls" v-show="modalAddQuestion">
        <svg @click="closeModalAddQuestion" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
        </svg>
        <div class="edit-question-number">
          Тема {{editedPartIndex + 1}}.{{editedThemeIndex + 1}}
        </div>
        <div class="edit-question-name">
          {{editedThemeName}}
        </div>
        <span class="edit-question-subtitle">Вопросы</span>
        <div class="questions-list" ref="questionsList">
          <div class="questions-list__item" v-for="(question, i) in getAllQuestionsSelected" :key="question.id">
          
            <div class="questions-list__item-wrapper">
            <div class="d-flex">
                <template class="" v-for="competence in competenceList">
                      <label class="rpd-checkbox-container pl-4 mr-2">
                          <span v-html="competence.short_code" :title="competence.title"> </span>
                          <input 
                            type="checkbox"
                            :value = "competenceChosen(question.selectedValue, competence.id)"
                            :checked = "competenceChosen(question.selectedValue, competence.id)"
                            @change="onChooseCompetence(competence, question.id, $event)"
                          > 
                          <span class="checkbox-checkmark"></span>
                    </label>
                </template>
            </div>
               <!--<multiselect 
                :value="question.selectedValue"
                @remove="onControlRemove($event, question.id)"
                @select="onControlSelect($event, question.id)"
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
                class="mb15"
                >
                <span slot="noResult">Ничего не найдено</span>
              </multiselect>-->
              <quill-editor class="quill-height mb15" :content="question.questionDescription" @change="onEditorChange($event, question.id)"/>
              <div class="d-flex wrap justify-between">
                <label class="rpd-checkbox-container">
                  <span v-html="isChecked(question) ? 'Открепить от темы' : 'Прикрепить к теме'"> </span>
                  <input 
                    type="checkbox"
                    :checked="isChecked(question)" 
                    @change="selectQuestion(question)" 
                    :disabled="!question.questionDescription.trim().length || !question.selectedValue.length"
                    
                  > 
                  <span class="checkbox-checkmark"></span>
                </label>
                <a href="#" @click.stop="deleteQuestion(question.id)" class="delete-link">
                  <svg 
                    class="mr-10" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z" fill="#FF3A40"/>
                  </svg>
                 Удалить вопрос 
                </a>
              </div>  
              <r-divider></r-divider>
            </div>
            

          </div>
        </div>
        <a href="#" @click.stop.prevent="addQuestion" class="btn-add question-add">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15.1943 8.80579L15.1943 7.19435L8.80572 7.19435L8.80572 0.805721H7.19428L7.19428 7.19435L0.805652 7.19435L0.805653 8.80579L7.19428 8.80579V15.1944H8.80572V8.80579L15.1943 8.80579Z" fill="#2F80ED"/>
          </svg>
          Добавить
        </a>
        <button class="btn-confirm btn--self-start" @click="closeModalAddQuestion">принять</button>
      </div>
      
      <div class="criteria">
        <span class="criteria__text">Критерии и шкала оценивания</span>
        <button class="btn-confirm" @click="showCriteriaModal = true">
          открыть
         
        </button>
      </div>
      
      <criteria-modal 
        :is-open="showCriteriaModal" 
        :content="criteriaContent"
        @on-criteria-content-change="onCriteriaContentChange"
        @close-criteria-modal="showCriteriaModal = false"
        :co-dev="coDev"
        ></criteria-modal>
    </div> 
  `
}