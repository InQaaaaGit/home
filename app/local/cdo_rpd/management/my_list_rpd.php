<?php

require_once(__DIR__ . "/../../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;

$PAGE->set_context(context_system::instance());
$title = get_string('RPD_my_list', 'local_cdo_rpd');

$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css'));
$PAGE->set_title($title);
$PAGE->set_url('/local/cdo_rpd/management/my_list_rpd.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->navbar->add($title, '/local/cdo_rpd/management/my_list_rpd.php');
$PAGE->requires->js(new moodle_url('app-teacher.js'));

echo $OUTPUT->header();
require_capability('local/cdo_rpd:view_rpd', $PAGE->context);
?>
    <div id="appTeacher">
        <loader v-show="isLoader"></loader>
        <progress-bar
                color="#219653"
                title="Утвержденные"
                :all-disciplines="tableData.length"
                :current-disciplines="completedDisciplineLength"
        >
        </progress-bar>
        <progress-bar
                color="#F2994A"
                title="На согласование"
                :all-disciplines="tableData.length"
                :current-disciplines="approvalDisciplineLength"
        >
        </progress-bar>
        <progress-bar
                color="#EB5757"
                title="В разработке"
                :all-disciplines="tableData.length"
                :current-disciplines="inDevelop"
        >
        </progress-bar>

        <r-divider></r-divider>
        <div
                class="filters-title"
                @click="showFilters = !showFilters">
            <h2 class="">Фильтры дисциплин</h2>
            <span
                    :class="{'c-arrow-transform' : showFilters}"
                    class="c-arrow-down"></span>
        </div>

        <transition name="bounce">
            <div class="root-filters-wrapper" v-if="showFilters">
                <multiselect
                        v-model="selectedYear"
                        :options="years"
                        placeholder="Год начала реализации образовательной программы:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="'Выбрать'">
                </multiselect>

                <multiselect
                        v-model="selectedDirection"
                        :options="directions"

                        placeholder="Специальность/направление подготовки:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="Выбрать">
                </multiselect>

                <multiselect
                        v-model="selectedEducationLevel"
                        :options="educationLevels"

                        placeholder="Уровень образования:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="Выбрать">
                </multiselect>

                <multiselect
                        v-model="selectedTrainingLevels"
                        :options="trainingLevels"
                        placeholder="Уровень подготовки:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="Выбрать">
                </multiselect>

                <multiselect
                        v-model="selectedEducationPrograms"
                        :options="educationPrograms"
                        placeholder="Дисциплина:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="Выбрать">
                </multiselect>

                <multiselect
                        v-model="selectedProfiles"
                        :options="profiles"
                        placeholder="Профиль:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="Выбрать">
                </multiselect>

              <!--  <r-divider class="root-divider--gird-full-size"></r-divider>

                <fielded-input
                        label="Дисциплина"
                        search-icon
                        v-model="disciplineSearch"
                >
                </fielded-input>-->

            </div>
        </transition>

        <r-divider></r-divider>

        <filters-group
                :filters="optionsFilters"
                :selected-filter="selectedFilter"
                @filter-by="selectedFilter = $event"
        ></filters-group>

        <structure-table
                :table-columns="tableColumns"
                :table-data="filteredTable"
                :search="disciplineSearch"
        >
            <template #td="{item}">
                <td
                        :class="{'active-ceil font-bold': item.newChanges}"
                >
                    <a :href=`rpd.php?rpd_id=${item.id}&edu_plan=${item.edu_plan.id}&discipline=${item.discipline_code}`
                       class="structure-table__link">{{item.discipline}}</a>
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                >
                    {{item.direction}}
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                >
                    {{item.profile}}
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                        class="st-text-center"
                >
                    {{item.type}}
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                        class="st-text-center"
                >
                    {{item.year}}
                </td>
                <td
                        :class="{'active-ceil font-bold': item.newChanges}"
                >
                    {{item.developer}}
                </td>

                <td
                        :class="{'active-ceil': item.newChanges}"
                >
                    <div class="approval__block">
                        <span :class="writeStatusColor(item.status)">{{writeStatus(item.status)}}</span>
                    </div>
                        <!--<div
                                class="approval__item"
                                :class="getApprovalStatus(item.approval.fos)"
                        >
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#178B49"/>
                                <rect width="8.21318" height="0.921776" rx="0.460888"
                                      transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)"
                                      fill="white"/>
                                <rect width="5.99155" height="0.921776" rx="0.460888"
                                      transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)"
                                      fill="white"/>
                            </svg>
                            ФОС
                        </div>-->
                    </div>
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                >
                    <svg
                            @click.stop="showInfo(item.info)"
                            class="icon-show-info"
                            width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V9H11V15ZM11 7H9V5H11V7Z"
                              fill="#2F80ED"
                            :fill="disagree(item)"
                        />

                    </svg>
                </td>
                <td
                        :class="{'active-ceil': item.newChanges}"
                >
                    <a
                            class="st-download"
                            :href="`/ulsu/rpd/make.php?type=zip&rpd_id=${item.id}&edu_plan=${item.edu_plan.id}&discipline=${item.discipline_code}`"

                            download>
                        <svg
                                width="14" height="17" viewBox="0 0 14 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 6H10V0H4V6H0L7 13L14 6ZM0 15V17H14V15H0Z" fill="#2F80ED"/>
                        </svg>
                    </a>
                </td>
            </template>
        </structure-table>

        <div class="modal-show-events" v-if="showEventsModal" v-click-outside="closeModal"
            style="overflow: auto;
            height: 500px;"
        >
            <h2>История действий</h2>
            <table class="events-table">
                <thead>
                <tr>
                    <th>дата</th>
                    <th>событие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in modalInfo">
                    <td v-for="item in row">{{item}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="components/loader.js"></script>
    <script src="components/structureTable.js"></script>
    <script src="components/fieldedInput.js"></script>
    <script src="components/fieldedTextarea.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/filtersGroup.js"></script>
    <script src="components/progressBar.js"></script>
    <script src="components/rDivider.js"></script>


<?php
echo $OUTPUT->footer();