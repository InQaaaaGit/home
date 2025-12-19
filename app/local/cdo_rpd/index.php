<?php

require_once(__DIR__ . "/../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;
$edu_plan = required_param("edu_plan", PARAM_TEXT);
$rpd_id = required_param("rpd_id", PARAM_TEXT);
$discipline = required_param("discipline", PARAM_TEXT);

$link = "/local/cdo_rpd/index.php?rpd_id=$rpd_id&edu_plan=$edu_plan&discipline=$discipline";

$title = get_string('RPD', 'local_cdo_rpd');
$PAGE->set_context(context_system::instance());
$PAGE->requires->css(new moodle_url('https://cdn.quilljs.com/1.3.4/quill.snow.css'));
$PAGE->requires->css(new moodle_url('https://cdn.quilljs.com/1.3.4/quill.core.css'));
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('vuejs-dialog.min.css'));
$PAGE->set_title($title);
$PAGE->set_url($link);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->navbar->add(get_string('RPD_my_list', 'local_cdo_rpd'), "/blocks/rpd/management/my_list_rpd.php");
$PAGE->navbar->add($title, $link);
$PAGE->requires->js(new moodle_url('app-rpd.js'));

echo $OUTPUT->header();

if (false) {
    \core\notification::error(get_string('RPD_not_right_main_developer', 'local_cdo_rpd'));
} else {

    ?>
    <div id="rpd" @keydown.ctrl.83.prevent.stop="saveRPDAll" >
        <loader v-show="isLoader"></loader>
        <div v-if="isDeveloper">
            <r-divider></r-divider>
            <div class="modules">
                <a
                    v-for="module in modules"
                    :key="module.guid"
                    class="modules__item"
                    :class="{'modules__item--active': module.guid === currentGuid}"
                    :href="`rpd.php?edu_plan=${findGetParameter('edu_plan')}&guid=${module.guid}&rpd_id=${findGetParameter('rpd_id')}&discipline=${findGetParameter('discipline')}`"
                >
                    {{module.module}}
                </a>
            </div>
            <div class="modules" v-if="consolidateModules.length">
                <a v-for="dev in developers"
                   class="modules__item w-100"
                   :class="{'modules__item--active': dev.guid === currentGuid}"
                   :href="`rpd.php?edu_plan=${findGetParameter('edu_plan')}&guid=${dev.guid}&rpd_id=${findGetParameter('rpd_id')}&discipline=${findGetParameter('discipline')}`"
                >
                    {{dev.user}} - {{dev.module}}
                </a>
            </div>
            <r-divider></r-divider>
            <div class="rpd-header">
                <div class="rpd-title">
                    {{INFO.discipline}} - {{showUserBlock}}</div>
                <div class="rpd-subtitle">
                    {{INFO.direction}} - {{INFO.type}} - {{INFO.year}} - Статус: {{writeStatus(INFO.status)}}
                </div>

                <div class="ml-auto">
                    <button
                        v-show="!globalImportLoader"
                        @click="openGlobalImportModal"
                        class="btn-confirm rpd-header_button"
                        v-if="!coDev"
                    >
                        Импорт
                    </button>
                    <button class="btn-confirm rpd-header_button"
                            @click="setStatus"
                            :disabled="INFO.status==2 || INFO.status==1"
                    >
                        Отправить на согласование
                    </button>
                    <button class="btn-confirm rpd-header_button" target="_blank">
                        <a class="color-white"
                           :href="`/ulsu/rpd/make.php?type=rpd&rpd_id=${INFO.id}&edu_plan=${findGetParameter('edu_plan')}&discipline=${findGetParameter('discipline')}`"
                           target="_blank"
                        >
                            Скачать РПД
                        </a>
                    </button>
                    <button class="btn-confirm rpd-header_button" target="_blank">
                        <a class="color-white"
                           :href="`/ulsu/rpd/make.php?type=fos&rpd_id=${INFO.id}&edu_plan=${findGetParameter('edu_plan')}&discipline=${findGetParameter('discipline')}`"
                           target="_blank">Скачать ФОС
                        </a>
                    </button>
                    <button class="btn-confirm rpd-header_button" target="_blank">
                        <a class="color-white"
                           :href="`/ulsu/rpd/make.php?type=annotation&rpd_id=${INFO.id}&edu_plan=${findGetParameter('edu_plan')}&discipline=${findGetParameter('discipline')}`"
                           target="_blank">Скачать Аннотацию
                        </a>
                    </button>
                    <!--<a
                            :href="`../ajax_lib.php?API=download_archive&rpd_id=${INFO.id}&rpd_name=${rpd_name}`"
                            class="btn-confirm rpd-header_button">Скачать (тестовый режим)
                    </a>-->
                    <div class="lds-ellipsis" v-show="globalImportLoader">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <button class="btn-confirm rpd-header_button" @click="saveRPDAll" v-show="!coDev">Сохранить</button>
                </div>
            </div>
            <r-divider></r-divider>
            <filters-group
                :filters="accessAdminFilters"
                :selected-filter="selectedFilter"
                @filter-by="selectedFilter = $event"

            >
            </filters-group>

            <target-and-tasks
                v-if="getActiveTabContainer('targetAndTasks')"
                key="target-and-tasks"
                @target-discipline="onTargetDiscipline"
                :target-text="dataTargetDiscipline"
                @tasks-discipline="onTasksDiscipline"
                :tasks-text="dataTasksDiscipline"
                :co-dev="coDev"
            ></target-and-tasks>

            <competences
                v-if="getActiveTabContainer('competences')"
                key="competences"
                :competence="competences"
                :co-dev="coDev"
            ></competences>
            <themes
                v-if="getActiveTabContainer('themes')"
                key="themes"
                :forms="forms"
                :parts.sync="parts"
                @edit-parts="editParts"
                :co-dev="coDev"
                ref="theme_child"

            ></themes>
            <types-of-tasks
                v-if="getActiveTabContainer('typesOfTasks')"
                key="types-of-tasks"
                :parts.sync="parts"
                :forms-count="forms.length"
                :appraisal-tools="controls"
                :theme-controls="appraisalTools"
                :selected-controls="controlsList"
                :selected-controls_="controlsList_"
                :audit-work="auditWork"
                :outwork="outwork"
                @on-audit-work-change="auditWork = $event"
                @on-outwork-change="outwork = $event"
                @edit-parts="editParts"
                @selected-controls="selectControls"
                @delete-theme-control="deleteThemesControl"
                :co-dev="coDev"
                ref="tot"
            ></types-of-tasks>
            <control
                v-if="getActiveTabContainer('control')"
                key="control"
                :controls="controlsList"
                :questions="questionsForAllThemes"
                :parts.sync="parts"
                :competence-list="competences"
                :questions-for-discipline="questionsForDiscipline"
                :criteria-list="criteriaList"
                @on-criteria-content-change="handleOnCriteriaContentChange"
                @add-questions-to-theme="handleAddQuestionToTheme"
                @delete-question-theme="handleDeleteQuestionTheme"
                @add-question-to-list="handleAddQuestionToList"
                @remove-question-from-list="handleRemoveQuestionFromList"
                @change-text-question="handleOnEditorChange"
                @change-text-answer="handleOnAnswerChange"
                @select-competence-question="handleSelectCompetenceQuestion"
                @delete-competence-question="handleDeleteCompetenceQuestion"
                @add-from-unallocated-part="handleAddFromUnallocatedPart"
                @delete-from-unallocated-part="handleDeleteFromUnallocatedPart"
                @import-questions-list="handleImportQuestionList"
                @add-imported-list="handleAddImportedList"
                :co-dev="coDev"
            ></control>
            <literature
                v-if="getActiveTabContainer('literature')"
                key="literature"
                @save-selected-books="saveSelectedBooks"
                @get-status-approval="getStatusApproval"
                :books-list="selectedBooksList"
                :current-user="currentUserID.id"
                :block-control="showUserBlock"
                :guid="currentGuid"
                :co-dev="coDev"
                :agreed-status="agreedStatus"
                @set-status="setStatusLiterature"
                ref="litera"
            ></literature>
            <mto
                v-if="getActiveTabContainer('mto')"
                key="mto"
                @edit-mto="changeMto"
                :chosen="MTO"
                :loader="isLoader"
                @status-loader="statusLoader"
                :co-dev="coDev"
            ></mto>
            <div class="rpd-footer">
                <button class="btn-confirm" @click="saveRPDAll" v-show="!coDev">Сохранить</button>
            </div>
            <loader v-show="isLoader"></loader>
            <div class="modal-controls import-modal" v-show="globalImportModal">
                <svg @click="globalImportModal = false" class="modal-controls__close" width="30" height="30"
                     viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
                          fill="black" fill-opacity="0.54"/>
                </svg>
                <div class="edit-question-number">
                    Выберите дисциплину для импорта
                </div>
                <div class="edit-question-name"></div>
                <div class="import-fields">
                    <div class="import-fields__item">
                        <multiselect
                            v-model="educationLevel"
                            :options="educationLevels"

                            placeholder="Уровень образования:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="Выбрать">
                        </multiselect>
                    </div>
                    <div class="import-fields__item-right">
                        <multiselect
                            v-model="trainingLevel"
                            :options="trainingLevels"
                            placeholder="Уровень подготовки:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="Выбрать">
                        </multiselect>
                    </div>
                    <div class="import-fields__item">
                        <multiselect
                            v-model="direction"
                            :options="directions"

                            placeholder="Специальность/направление подготовки:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="Выбрать">
                        </multiselect>
                    </div>
                    <div class="import-fields__item-right">
                        <multiselect
                            v-model="profile"
                            :options="profiles"
                            placeholder="Профиль:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="Выбрать">
                        </multiselect>
                    </div>
                    <div class="import-fields__item import-fields__item-small">
                        <multiselect
                            v-model="year"
                            :options="years"
                            placeholder="Год начала реализации образовательной программы:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="'Выбрать'">
                        </multiselect>
                    </div>
                    <div class="import-fields__item-right import-fields__item-big">
                        <multiselect
                            v-model="discipline"
                            :options="disciplines"
                            placeholder="Дисциплина:"
                            label="value"
                            track-by="value"
                            deselect-label="Удалить"
                            select-label="Выбрать">
                        </multiselect>
                    </div>
                </div>
                <div class="discipline-container">
                    <div class="discipline-container__archive">
                        <div
                            @click="selectArchiveItem(item)"
                            v-for="item in paginatedArchive"
                            :key="item.id"
                            class="discipline-container__item"
                            :class="{'discipline-container__item--active': item.id === selectedArchiveDiscipline?.id}"
                        >
                            <div class="discipline-container__item-title">{{item.typeAndModule}}
                                <a @click.stop href="https://www.okbhmao.ru/download.php?file=12021" download>
                                    <svg width="15" height="18" viewBox="0 0 15 18" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.5898 6.5H10.5898V0.5H4.58984V6.5H0.589844L7.58984 13.5L14.5898 6.5ZM0.589844 15.5V17.5H14.5898V15.5H0.589844Z"
                                              fill="#2F80ED"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="discipline-container__item-descr">
                                {{item.discipline}} - {{item.year}} <br /> {{item.direction}} - {{item.profile}}
                            </div>
                        </div>
                    </div>
                    <div class="discipline-container__imported">
                        <div class="discipline-container__imported-title">Выберите элементы</div>
                        <div class="discipline-container__checkboxes-group">
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container"
                                       data-toggle="tooltip" data-placement="top" title="Весь раздел полностью переносится из выбранной РПД."
                                >
                                    Цели и задачи
                                    <input type="checkbox" v-model="selectedImport.targetAndTasks">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Компетенции
                                    <input type="checkbox" v-model="selectedImport.knowAbleOwn">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Темы
                                    <input type="checkbox" v-model="selectedImport.themes" @change="unCheck">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-subitem"
                                 :class="{'disabled-import-tag' : disableHours}"
                            >
                                <label class="rpd-checkbox-container">
                                    Часы
                                    <input :disabled="disableHours" type="checkbox" v-model="selectedImport.hours" >
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Задания
                                    <input type="checkbox" v-model="selectedImport.control" @change="unCheck">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-subitem"
                                 :class="{'disabled-import-tag' : disableControl}"
                            >
                                <label class="rpd-checkbox-container">
                                    Закрепление тем и компетенций
                                    <input :disabled="disableControl" type="checkbox" v-model="selectedImport.distribution">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Оборудование
                                    <input type="checkbox" v-model="selectedImport.inventory">
                                    <span class="checkbox-checkmark" :class="{'disabled-import-input': disableControl}">

                                    </span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Программное обеспечение
                                    <input type="checkbox" v-model="selectedImport.software">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                            <div class="discipline-container__checkboxes-wrapper">
                                <label class="rpd-checkbox-container">
                                    Литература
                                    <input type="checkbox" v-model="selectedImport.literature">
                                    <span class="checkbox-checkmark"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="discipline-container__footer">
                        <div class="archive-pagination">
                            <div class="pagination-per-page">
                                <span class="pagination-per-page_title">Записей на странице: </span>
                                <div
                                    @click="isActivePaginationSelect = true"
                                    @blur="isActivePaginationSelect = false"
                                    class="pagination-per-page_select"
                                    tabindex="0"
                                >
                                    <span class="pagination-per-page_counter">{{itemsPerPage}}</span>
                                    <span class="c-arrow-down c-arrow-down--reduce-margin"></span>
                                    <transition name="bounce">
                                        <ul class="pagination-dropdown" v-show="isActivePaginationSelect">
                                            <li
                                                v-for="page in listPerPage"
                                                :key="page"
                                                class="pagination-dropdown__item"
                                                @click.stop="selectPage(page)">{{page}}
                                            </li>
                                        </ul>
                                    </transition>
                                </div>
                            </div>
                            <span class="pagination__counter">
          {{fromPage}}-{{toPage}} из {{filteredArchive.length}}
        </span>
                            <button
                                @click="currentPage--"
                                :disabled="currentPage == 0"
                                class="pagination_button">
                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z" fill="black"
                                          fill-opacity="0.87"/>
                                </svg>
                            </button>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= pageCount - 1"
                                class="pagination_button pagination_button--margin-left">
                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z"
                                          fill="black" fill-opacity="0.87"/>
                                </svg>
                            </button>
                        </div>
                        <button class="btn-confirm" @click="importFromArchive">импорт</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>Вы не разработчик данной программы!</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue-draggable-resizable@2.3.0/dist/VueDraggableResizable.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uuid/8.1.0/uuidv4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-toast-notification@0.6/dist/index.min.js"></script>
    <script src="https://unpkg.com/v-tooltip@^2.0.0"></script>
    <script src="https://unpkg.com/vue-multiselect@2.1.6"></script>
    <script src="https://cdn.quilljs.com/1.3.4/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-quill-editor@3.0.6/dist/vue-quill-editor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script type="text/javascript" src="vuejs-dialog.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.17/mammoth.browser.min.js"></script>

    <script src="components/criteriaModal.js"></script>
    <script src="components/controlTemplates/first.js"></script>
    <script src="components/controlTemplates/second.js"></script>
    <script src="components/controlTemplates/third.js"></script>
    <script src="components/controlTemplates/fourth.js"></script>
    <script src="components/controlTemplates/fifth.js"></script>

    <script src="components/fieldedInput.js"></script>
    <script src="components/fieldedTextarea.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/filtersGroup.js"></script>
    <script src="components/rDivider.js"></script>
    <script src="components/loader.js"></script>
    <script src="components/rpd/targetAndTasks.js"></script>
    <script src="components/rpd/competences.js"></script>
    <script src="components/rpd/themes.js"></script>
    <script src="components/rpd/typesOfTasks.js"></script>
    <script src="components/rpd/control.js"></script>
    <script src="components/rpd/literature.js"></script>
    <script src="components/rpd/mto.js"></script>
    <script src="components/accordion/accordion.js"></script>
    <script src="components/accordion/accordion-item.js"></script>


    <?php
}
echo $OUTPUT->footer();