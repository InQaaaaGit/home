const typesOfTasks = {
    props: {
        parts: {
            type: Array,
            required: true,
        },
        appraisalTools: {
            type: Array,
            required: true,
        },
        formsCount: {
            type: Number,
            required: true
        },
        themeControls: {
            type: Array,
            required: false
        },
        selectedControls_: {
            type: Object,
            required: false
        },
        auditWork: {
            type: String,
            required: true,
            default: ''
        },
        outwork: {
            type: String,
            required: true,
            default: ''
        },
        coDev: {
            type: Boolean,
            default: false,
        }
    },
    data: function () {
        return {
            // selectedControls: this.selectedControls_,
            selectedControls: [],
            showAuditEditor: false,
            showOutworkEditor: false,
            currentUserID: ''
        }
    },
    created() {
        this.getRPDInfo();

    },
    watch: {
        selectedControls_(val, oldVal) {
            console.log(val);
            this.onControlSelect(val);
        },
        parts: {
            handler: function (val, oldVal) {
                //   this.$emit('update:parts', val);
                this.$emit('selected-controls', _.cloneDeep(this.selectedControls));
            },
            deep: true
        },
        selectedControls(val, OldVal) {
            this.$emit('selected-controls', _.cloneDeep(val));
        },
        appraisalTools(val, OldVal) {
            if (!this.selectedControls.length) {
                let requiredList = [];
                this.appraisalTools.forEach(item => {
                    item.enroleTypes = item.enroleTypes.map(item => ({
                        ...item,
                        $isDisabled: !!item.required
                    }))
                });

                this.appraisalTools.forEach(item => {
                    requiredList =
                        requiredList.concat(
                            item.enroleTypes.filter(item => item.required)
                        );
                });

                this.selectedControls = requiredList;
            }
        }

    },
    methods: {
        insertControlsList(controlsList) {
            this.selectedControls = _.uniqBy(controlsList, "code");
            //this.selectedControls = controlsList;
        },
        customLabel({name}) {
            return `${name}`
        },
        onAuditWorkChange(e) {
            this.$emit('on-audit-work-change', e.html)
        },
        onOutworkChange(e) {
            this.$emit('on-outwork-change', e.html)
        },
        findGetParameter(parameterName) {
            var result = null,
                tmp = [];
            location.search
                .substr(1)
                .split("&")
                .forEach(function (item) {
                    tmp = item.split("=");
                    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
                });
            return result;
        },
        getRPDInfo() { //rename
            const vm = this;
            const edu_plan = this.findGetParameter('edu_plan');
            const discipline = this.findGetParameter('discipline');
            const rpd_id = this.findGetParameter('rpd_id');
            const currentGuid = this.findGetParameter('guid');
            // const user_id = this.currentUserID;

            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    //notification.init(1);
                    var promises = ajax.call([
                        {
                            methodname: 'get_current_user_info',
                            args: {}
                        }
                    ], false);
                    promises[0].done((response) => {
                        vm.currentUserID = response.id;
                    }).fail(function (ex) {
                        notification.exception(ex);

                    });
                    var promises = ajax.call(
                        [
                            {
                                methodname: 'get_competencies_for_rpd',
                                args: {
                                    edu_plan: edu_plan,
                                    discipline: discipline,
                                    rpd_id: rpd_id,
                                    user_id: vm.currentUserID,
                                    module_guid: currentGuid
                                }
                            }
                        ], false
                    );
                    promises[0].done((response) => {
                        const cl = _.uniqBy(response.controlsList, "code");
                        vm.selectedControls = cl;
                        if (!vm.selectedControls.length) {
                            let requiredList = [];
                            vm.appraisalTools.forEach(item => {
                                item.enroleTypes = item.enroleTypes.map(item => ({
                                    ...item,
                                    $isDisabled: !!item.required
                                }))
                            });

                            vm.appraisalTools.forEach(item => {
                                requiredList =
                                    requiredList.concat(
                                        item.enroleTypes.filter(item => item.required)
                                    );
                            });

                            vm.selectedControls = requiredList;
                        }
                    }).fail(function (ex) {
                        notification.exception(ex);

                    });
                });
        },
        isLabsInclude(themeData) {
            const labs = this.themeControls.find(item => item.type === 'labs').enroleTypes;
            return _.intersectionBy(labs, themeData, 'code');
        },
        isPracticeInclude(themeData) {
            const practice = this.themeControls.find(item => item.type === 'practice').enroleTypes;
            return _.intersectionBy(practice, themeData, 'code');
        },
        isOutworkInclude(themeData) {
            const outwork = this.themeControls.find(item => item.type === 'outwork').enroleTypes;
            return _.intersectionBy(outwork, themeData, 'code');
        },

        colorStatus(themeData) {
            const labs = this.isLabsInclude(themeData);
            const practice = this.isPracticeInclude(themeData);
            const outwork = this.isOutworkInclude(themeData);

            const arrThemes = [labs, practice, outwork];

            if (arrThemes.every(item => item.length === 0)) {
                return {
                    'tasks-plan__status--default ': true
                }
            }

            if (arrThemes.some(item => item.length === 0)) {
                return {
                    'tasks-plan__status--warning': true
                }
            }

            if (arrThemes.every(item => item.length !== 0)) {
                return {
                    'tasks-plan__status--complete': true
                }
            }

        },
        blockedByHoursControls(theme) {
            const labs = [theme.lab, theme.lab_za, theme.lab_oza].slice(0, this.formsCount).some(item => item === '0');
            const practice = [theme.practice, theme.practice_za, theme.practice_oza].slice(0, this.formsCount).some(item => item === '0');
            const outwork = [theme.outwork, theme.outwork_za, theme.outwork_oza].slice(0, this.formsCount).some(item => item === '0');

            const themeControls = _.cloneDeep(this.themeControls);

            themeControls.forEach(group => {
                if (labs) {
                    const itemsToDisable = themeControls.find(item => item.type === 'labs');
                    itemsToDisable.enroleTypes = itemsToDisable.enroleTypes.map(enroleTypes => ({
                        ...enroleTypes,
                        $isDisabled: true
                    }))
                }
                if (practice) {
                    const itemsToDisable = themeControls.find(item => item.type === 'practice');
                    itemsToDisable.enroleTypes = itemsToDisable.enroleTypes.map(enroleTypes => ({
                        ...enroleTypes,
                        $isDisabled: true
                    }))
                }
                if (outwork) {
                    const itemsToDisable = themeControls.find(item => item.type === 'outwork');
                    itemsToDisable.enroleTypes = itemsToDisable.enroleTypes.map(enroleTypes => ({
                        ...enroleTypes,
                        $isDisabled: true
                    }))
                }
            });

            return themeControls;
        },
        onControlRemove(removedOption) {
            this.$dialog
                .confirm('Удалить вид контроля?')
                .then(dialog => {
                    if (!removedOption.required)
                        this.selectedControls = this.selectedControls.filter(
                            (item) => item.code !== removedOption.code
                        );
                    this.$emit('delete-theme-control', removedOption.code);
                }).catch(e => {
            })
        },
        onControlSelect(option) {
            if (Array.isArray(option))
                this.selectedControls = this.selectedControls.concat(option);
            else {
                if (!this.selectedControls.some(element => {
                   return element.code === option.code;
                })) {
                    this.selectedControls.push(option);
                }
            }
        },
        tooltipStatus(themeData) {
            const isLabsIncludeStatus = !!this.isLabsInclude(themeData).length ? '' : 'Лабораторные работы';
            const isPracticeIncludeStatus = !!this.isPracticeInclude(themeData).length ? '' : 'Практические занятия';
            const isOutworkIncludeStatus = !!this.isOutworkInclude(themeData).length ? '' : 'Самостоятельные занятия';

            const thisPartsIsEmpty = [isLabsIncludeStatus, isPracticeIncludeStatus, isOutworkIncludeStatus].filter(Boolean).join(', ');

            if (!thisPartsIsEmpty.length) {
                return `Заполнено`
            } else {
                return `Заполните оценочные средства: <br><span class="fs-12">${thisPartsIsEmpty}</span>`
            }

        }
    },
    computed: {},

    template: `
      <div>
      <div class="tasks-hero">
        <p class="tasks-hero__subtitle">При необходимости, выберите дополнительные виды контроля (кроме указанных по
          умолчанию)</p>
        <span class="tasks-hero__text">
          
        </span>
        <multiselect
            :disabled="coDev"
            :value="selectedControls"
            :options="appraisalTools"
            @remove="onControlRemove"
            @select="onControlSelect"
            group-values="enroleTypes"
            group-label="enrole"
            track-by="code"
            label="name"
            multiple
            :close-on-select="false"
            :group-select="true"
            select-label="Выбрать"
            selected-label="Выбрано"
            placeholder="Выберите"
            deselect-label="Удалить"
            select-group-label="Выбрать группу"
            deselect-group-label="Удалить группу"
        >
          <span slot="noResult">Ничего не найдено</span>
          <template
              slot="tag"
              slot-scope="{ option, remove }"
          >
                 <span class="multiselect__tag multiselect__tag--custom"
                       :class="{'multiselect__tag--disabled': option.required}">
                  <span>{{ option.name }}</span>
                  <i tabindex="1" class="multiselect__tag-icon" v-if="!option.required" @click="remove(option)"></i>
                </span>
          </template>
        </multiselect>
      </div>
      <h5>При неободимости, отредактируйте информацию об образовательных технологиях в полях ниже</h5>
      <div class="tasks-edited">
        <div class="tasks-edited__item">
          <div class="tasks-edited__header">
            <span>Образовательные технологии для аудиторной работы</span>
            <button class="btn-discard" @click="showAuditEditor = true">Открыть</button>
          </div>
          <div class="tasks-edited__body" v-show="showAuditEditor">
            <template v-if="!coDev">
              <quill-editor class="quill-height" :value="auditWork" @change="onAuditWorkChange($event)"/>
            </template>
            <template v-else>
              <div v-html="auditWork"></div>
            </template>
          </div>
        </div>
        <div class="tasks-edited__item">
          <div class="tasks-edited__header">
            <span>Образовательные технологии для самостоятельной работы</span>
            <button class="btn-discard" @click="showOutworkEditor = true">Открыть</button>
          </div>
          <div class="tasks-edited__body" v-show="showOutworkEditor">
            <template v-if="!coDev">
              <quill-editor class="quill-height" :value="outwork" @change="onOutworkChange($event)"/>
            </template>
            <template v-else>
              <div v-html="outwork"></div>
            </template>
          </div>
        </div>

      </div>
      <div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1RS6wcnZbVYGYq2uczhXkbqX14YGezq75/view?usp=drive_link">Видеоинструкция - Виды заданий</a></p>
 </div>
      </div>
    `
}