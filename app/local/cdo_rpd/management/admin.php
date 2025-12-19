<?php

require_once(__DIR__ . "/../../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;

$context = context_system::instance();
$title = get_string('management', 'local_cdo_rpd');

$PAGE->set_context($context);
$PAGE->set_url('/local/cdo_rpd/management/admin.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->set_title($title);
$PAGE->navbar->add($title, $PAGE->url);
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css'));
$PAGE->requires->js(new moodle_url('app-personal-zav.js'));

echo $OUTPUT->header();

if (!has_capability('local/cdo_rpd:view_executive_secretary', $context) || !has_capability('local/cdo_rpd:view_admin_rpd', $context)) {
    bootstrap_renderer::early_error('Недостаточно прав', '', '', []);
}
?>

    <div id="app-zav-kaf" >
        <progress-bar
            color="#219653"
            title="Утвержденные"
            :all-disciplines="tableData.length"
            :current-disciplines="completedDisciplineLength"
        >
        </progress-bar>
        <progress-bar
            color="#F2994A"
            title="На согласовании"
            :all-disciplines="tableData.length"
            :current-disciplines="approvalDisciplineLength"
        >
        </progress-bar>
        <progress-bar

            color="#2F80ED"
            title="В разработке"
            :all-disciplines="tableData.length"
            :current-disciplines="developingDisciplineLength"
        >
        </progress-bar>
        <progress-bar
            color="#EB5757"

            title="Не распределенные"
            :all-disciplines="tableData.length"
            :current-disciplines="notAllocatedDisciplineLength"
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

            </div>
        </transition>

        <r-divider></r-divider>

        <filters-group
            :filters="optionsFilters"
            :selected-filter="selectedFilter"
            @filter-by="selectedFilter = $event"
            :hide-codev="hideCodev"
        ></filters-group>

        <structure-table

            :table-columns="tableColumns"
            :table-data="filteredTable"
        >
            <template v-slot:discipline="{item}">
                <!-- <a :href=`rpd.php?rpd_id=${item.id}&edu_plan=${item.edu_plan.id}&discipline=${item.discipline_code}`
                    class="structure-table__link">{{item.discipline}}</a>-->
                <template v-if="!isExecutiveSecretary">
                    <a style="cursor: pointer;"
                       @click.stop="editDevelopers(item.developers, item.id, item.main_department, item.hideCodev)"
                    >
                        {{item.discipline}}
                    </a>
                </template>
                <template v-else>
                    {{item.discipline}}
                </template>
            </template>

            <template v-slot:developers="{item}">
                <div
                    class="developers"
                >
                    <div class="developers__block">
                        <span class="developers__main-developer"
                              :class="!item.developers.mainDeveloper.length ? 'text-danger' : ''"
                        >
                            {{item.developers.mainDeveloper[0]?.user || 'НЕ НАЗНАЧЕН РАЗРАБОТЧИК'}}
                        </span>
                    </div>
                    <!-- <svg
                             @click.stop="editDevelopers(item.developers, item.id, item.main_department)"
                             class="developers__edit"
                             width="19"
                             height="19"
                             viewBox="0 0 19 19"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                         <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                               fill="black" fill-opacity="0.54"/>
                     </svg>-->
                </div>


            </template>

            <template v-slot:status="{item}">
                <span :class="writeStatusColor(item.status)">{{writeStatus(item.status)}}</span>
            </template>

            <template v-slot:id="{item}">

                <div v-if="item.status=='2'">
                    <div v-if="((!item.isHODAgreed && type==='headofdepartment') || (!item.isOPOPAgreed && type==='ExecutiveSecretary'))">
                        <div>
                            <button class="w-160 btn-confirm rpd-header_button"
                                    @click="setStatus(item, 1)">
                                согласовать
                            </button>
                        </div>
                        <div>
                            <button class="w-160 mt-2 btn-confirm btn-disagreed rpd-header_button"
                                    @click="openModalDisagreed(item)"
                            >
                                отклонить
                            </button>
                        </div>
                    </div>
                    <div class="mt-2" v-if="(item.isHODAgreed && type==='headofdepartment')">Заведующий кафедрой согласовал РПД</div>
                    <div class="mt-2" v-if="(item.isOPOPAgreed && type==='ExecutiveSecretary')">Руководитель ОП согласовал РПД</div>
                </div>
                <div v-else></div>
            </template>
            <template v-slot:info="{item}">
                <svg
                    v-show="item.developers.mainDeveloper.length"
                    @click.stop="showInfo(item.info)"
                    class="icon-show-info"
                    width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V9H11V15ZM11 7H9V5H11V7Z"
                          fill="#2F80ED"
                          :fill="disagree(item)"
                    />

                </svg>
            </template>
            <template v-slot:discipline_index="{item}">
                <a
                    v-if="item.developers.mainDeveloper.length"
                    class="st-download"
                    :class="!item.developers.mainDeveloper.length ? 'link-inactive' : ''"
                    :href="`/local/cdo_rpd/rpd/make.php?type=zip&rpd_id=${item.id}&edu_plan=${item.edu_plan.id}&discipline=${item.discipline_code}&user_id=${item.developers.mainDeveloper[0].id}`"
                    :disabled="!item.developers.mainDeveloper.length"
                    download>

                    <svg
                        :class="!item.developers.mainDeveloper.length ? 'link-inactive' : ''"
                        width="14" height="17" viewBox="0 0 14 17" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 6H10V0H4V6H0L7 13L14 6ZM0 15V17H14V15H0Z"

                              :fill="!item.developers.mainDeveloper.length ? 'grey' : '#2F80ED'"
                        />
                    </svg>
                </a>
            </template>
            <template v-slot:actions="{item}">
                <!--<div v-show="item.status=='2'">-->
                    <button class="btn-confirm rpd-header_button w-100 mb-1 bg-success"
                            @click="setStatusRPD(0, item.id, item)">Принять
                    </button>
                    <button class="btn-confirm rpd-header_button w-100 mb-1 bg-danger"
                            @click.stop="showDismissModalFunc(item)">Отклонить
                    </button>
                    <button class="btn-confirm rpd-header_button w-100 ">
                        <a style="color:white;" target="_blank" :href="`../print.php?rpd_id=${item.id}&layout=RDPZIP`">Скачать</a>
                    </button>
                <!--</div>-->
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
                    <th width="200">дата</th>
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
        <div class="modal-change-developer" style="margin-top: 200px" v-if="showDisagreedModal">
            <div @click="showDisagreedModal=false" class="close-modal" style="">Х</div>
            <fielded-textarea
                placeholder="введите причину отклонения (более 10 символов)"
                dashed
                v-model.trim="reasonDisagreed"
                style="width: 100%"
            ></fielded-textarea>
            <button class="w-160 mt-2 btn-confirm btn-disagreed rpd-header_button"
                    @click="setDisagree(currentItem, 3); showDisagreedModal=false"
                    :disabled="reasonDisagreed.length < 10"
            >
                Отклонить
            </button>
        </div>
        <div class="modal-change-developer" v-show="showDeveloperModal" v-click-outside="closeDevelopersModal">

            <filters-group
                v-if="isAdmin"
                :filters="filterModals"
                :selected-filter="selectedModalTab"
                @filter-by="selectedModalTab = $event"
                :hide-codev="hideCodev"
            ></filters-group>

            <div v-if="selectedModalTab.filter === 1">
                <div class="block-developers">
                    <div class="block-developers__title">
                        Разработчик
                    </div>
                    <div v-if="!changeDisciplineDevelopersObj.mainDeveloper?.length" class="block-developers__empty">
                        <span class="">Не назначен</span>
                    </div>
                    <div class="developers-list">
                        <div v-for="(mainDeveloper, idx) in changeDisciplineDevelopersObj.mainDeveloper"
                             class="developers-list__item"
                        >
                            <div
                                class="developer-header"
                                :class="{'selected-developer': selectedDeveloperID === mainDeveloper.id}"
                            >
                                {{mainDeveloper.user}}
                                <div class="edit-developer">
                                    <svg
                                        @click.stop="selectDeveloper(mainDeveloper.id)"
                                        class="cursor-pointer"
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                              fill="black" fill-opacity="0.54"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="developers-list__field">
                                <svg
                                    v-if="isAdmin"
                                    width="19"
                                    height="19"
                                    viewBox="0 0 19 19"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                          fill="black" fill-opacity="0.54"/>
                                </svg>
                                <fielded-input
                                    :disabled="!isAdmin"
                                    v-if="Object.keys(changeDisciplineDevelopersObj).length"
                                    placeholder="введите модуль"
                                    dashed
                                    v-model.trim="mainDeveloper.blockControl"
                                    style="width: 100%"
                                    @change="selectMainDeveloper(mainDeveloper)"
                                ></fielded-input>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <fielded-input
                        label="Поиск разработчика"
                        search-icon
                        v-model="searchDevelopersField"
                    >
                    </fielded-input>
                    <div class="block-developers__heading">
                        <p class="block-developers__heading-text">Все разработчики</p>
                    </div>
                    <div class="all-developers">
                        <div v-for="user in filteredDevelopers" :key="user.id" class="block-developers__item">
                            <div class="block-developers__developer-name">
                                {{user.user}}
                                <transition name="bounce">
                                    <svg
                                        v-show="selectedDeveloperID || !changeDisciplineDevelopersObj.mainDeveloper.length"
                                        @click="selectMainDeveloper(user)"
                                        class="block-developers__developer-add" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.0823 10.8965L18.0823 8.90247L10.4794 8.90247L10.4794 0.99702H8.56164L8.56164 8.90247L0.958727 8.90247L0.958727 10.8965L8.56164 10.8965V18.802H10.4794V10.8965L18.0823 10.8965Z"
                                              fill="#2F80ED"/>
                                    </svg>
                                </transition>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="selectedModalTab.filter === 2">
                <div v-if="!isAdmin" class="block-developers">
                    <div class="block-developers__title">
                        Разработчик
                    </div>
                    <div v-if="!changeDisciplineDevelopersObj.mainDeveloper?.length" class="block-developers__empty">
                        <span class="">Не назначен</span>
                    </div>
                    <div class="developers-list">
                        <div v-for="(mainDeveloper, idx) in changeDisciplineDevelopersObj.mainDeveloper"
                             class="developers-list__item"
                        >
                            <div
                                class="developer-header"
                                :class="{'selected-developer': selectedDeveloperID === mainDeveloper.id}"
                            >
                                {{mainDeveloper.user}}
                                <div class="edit-developer">
                                    <svg
                                        v-if="isAdmin"
                                        @click.stop="selectDeveloper(mainDeveloper.id)"
                                        class="cursor-pointer"
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                              fill="black" fill-opacity="0.54"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="developers-list__field">
                                <svg
                                    v-if="isAdmin"
                                    width="19"
                                    height="19"
                                    viewBox="0 0 19 19"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                          fill="black" fill-opacity="0.54"/>
                                </svg>
                                <fielded-input
                                    :disabled="!isAdmin"
                                    v-if="Object.keys(changeDisciplineDevelopersObj).length"
                                    placeholder="введите модуль"
                                    dashed
                                    v-model="mainDeveloper.blockControl"
                                    style="width: 100%"
                                    @change=""
                                ></fielded-input>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-developers">
                    <div class="block-developers__title">
                        Соразработчик
                    </div>
                    <div v-if="!changeDisciplineDevelopersObj.coDevelopers?.length" class="block-developers__empty">
                        <span class="">Не назначен</span>
                    </div>
                    <div class="developers-list">
                        <div v-for="(selectedCoDeveloper, idx) in changeDisciplineDevelopersObj.coDevelopers"
                             :key="selectedCoDeveloper.id + idx"
                             class="developers-list__item"
                        >
                            <div
                                class="developer-header"
                                :class="{'selected-developer': selectedCoDeveloperToChangeIDX === idx}"
                            >
                                {{selectedCoDeveloper.user}}

                                <div class="edit-developer">
                                    <svg
                                        @click.stop="coDeveloperToChange(selectedCoDeveloper, idx)"
                                        class="cursor-pointer"
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                              fill="black" fill-opacity="0.54"/>
                                    </svg>
                                    <svg
                                        @click.stop="deleteCoDeveloper(selectedCoDeveloper, idx)"
                                        class="ml-10 cursor-pointer" width="14" height="14" viewBox="0 0 14 14"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z"
                                              fill="#EB5757"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="developers-list__field">
                                <svg
                                    width="19"
                                    height="19"
                                    viewBox="0 0 19 19"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 15.25V19H3.75L14.81 7.94L11.06 4.19L0 15.25ZM17.71 5.04C18.1 4.65 18.1 4.02 17.71 3.63L15.37 1.29C14.98 0.899998 14.35 0.899998 13.96 1.29L12.13 3.12L15.88 6.87L17.71 5.04Z"
                                          fill="black" fill-opacity="0.54"/>
                                </svg>
                                <fielded-input
                                    v-if="Object.keys(changeDisciplineDevelopersObj).length"
                                    placeholder="введите модуль"
                                    dashed
                                    v-model.trim="selectedCoDeveloper.blockControl"
                                    style="width: 100%"
                                    @change = "changeBlockCoDeveloper(selectedCoDeveloper)"
                                ></fielded-input>
                            </div>
                        </div>
                    </div>
                </div>
                <fielded-input
                    label="Поиск соразработчика"
                    search-icon
                    v-model="searchCoDevelopersField"
                >
                </fielded-input>
                <div class="block-developers__heading">
                    <p class="block-developers__heading-text">Все разработчики</p>
                </div>
                <div class="all-developers" v-if="!loadingDevelopers">
                    <div v-for="(user, idx) in filteredCoDevelopers" :key="user.id + idx"
                         class="block-developers__item">
                        <div class="block-developers__developer-name">
                            {{user.user}}
                            <svg
                                @click.stop="addCoDeveloper(user)"
                                class="block-developers__developer-add" width="19" height="19" viewBox="0 0 19 19"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.0823 10.8965L18.0823 8.90247L10.4794 8.90247L10.4794 0.99702H8.56164L8.56164 8.90247L0.958727 8.90247L0.958727 10.8965L8.56164 10.8965V18.802H10.4794V10.8965L18.0823 10.8965Z"
                                      fill="#2F80ED"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>

            <div class="modal-change-developer__footer">
                <button
                    @click="closeDevelopersModal"
                    class="btn-discard mr-10">
                    закрыть
                </button>
                <!--<button
                        @click="saveDiscipline"
                        class="btn-confirm">
                    сохранить
                </button>-->
            </div>
        </div>
        <loader v-show="isLoader"></loader>
        <div style="padding:15px;">
            <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1Xtp-l6t_XjEA-9NHw9USg5R9uedjuFCb/view?usp=drive_link">Видеоинструкция - Назначение ответственных за разработку РПД, ФОС, АРП</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-toast-notification@0.6/dist/index.min.js"></script>
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="components/loader.js"></script>
    <script src="components/rDivider.js"></script>
    <script src="components/structureTable.js"></script>
    <script src="components/filtersGroup.js"></script>
    <script src="components/progressBar.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/fieldedInput.js"></script>
    <script src="components/fieldedTextarea.js"></script>

<?php
echo $OUTPUT->footer();