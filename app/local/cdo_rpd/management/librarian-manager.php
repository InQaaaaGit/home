<?php

require_once(__DIR__ . "/../../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;
$context = context_system::instance();
require_capability('local/cdo_rpd:view_worker_library', $context);
$PAGE->set_context($context);
#$title = get_string('management', 'block_rpd');
$title = 'Список литературы РПД на согласовании';
$url = '/local/cdo_rpd/management/librarian-manager.php';
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css'));
$PAGE->set_title($title);
$PAGE->set_url($url);
$PAGE->set_heading($title);
#$PAGE->set_pagelayout('base');
$PAGE->navbar->add($title, $url);
$PAGE->requires->js(new moodle_url('librarian-manager.js'));

echo $OUTPUT->header();
?>
    <div id="librarian-manager">
        <progress-bar
                color="#219653"
                title="Согласовано"
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
                title="Не согласовано"
                :all-disciplines="tableData.length"
                :current-disciplines="developingDisciplineLength"
        >
        </progress-bar>
        <progress-bar
                color="#EB5757" EB5757
                title="В разработке"
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
        ></filters-group>

        <structure-table
                :table-columns="tableColumns"
                :table-data="filteredTable"
        >
            <template v-slot:discipline="{item}">
                <a target="_blank"
                   :href=`librarian-manager-confirm.php?discipline=${item.discipline}&guid=${item.developers.mainDeveloper[0]?.guid}&module=${item.developers.mainDeveloper[0]?.blockControl}&rpd_id=${item.id}&user_id=${item.developers.mainDeveloper[0]?.id}`
                   class="structure-table__link">{{item.discipline}}</a>
            </template>

            <template v-slot:modules="{item}">
                <template v-for="(devs, idx) in item.developers?.coDevelopers" >
                    <a
                            :href=`librarian-manager-confirm.php?discipline=${item.discipline}&guid=${item.guid}&module=${devs.blockControl}&rpd_id=${item.id}&user_id=${devs?.id}`
                            class="structure-table__link">

                        {{devs.blockControl}}
                    </a> <br/>
                </template>
                <a target="_blank"
                   :href=`librarian-manager-confirm.php?discipline=${item.discipline}&guid=${item.developers.mainDeveloper[0]?.guid}&module=${item.developers.mainDeveloper[0]?.blockControl}&rpd_id=${item.id}&user_id=${item.developers.mainDeveloper[0]?.id}`
                   class="structure-table__link">
                    {{item.developers?.mainDeveloper[0]?.blockControl}}
                </a>
            </template>

            <template v-slot:developers="{item}">
                <div class="developers">

                    <div class="developers__block">
                        <span class="developers__main-developer">
                          {{item.developers.mainDeveloper[0]?.user || 'Не назначен'}}
                        </span>
                    </div>
                </div>
            </template>

            <template v-slot:librarian_status="{item}">
                
                <div class="discipline-status" v-html="getStatusType(item.librarian_status)">
                </div>
            </template>

        </structure-table>
        <loader v-show="isLoader"></loader>
    </div>
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="components/loader.js"></script>
    <script src="components/rDivider.js"></script>
    <script src="components/structureTable.js"></script>
    <script src="components/filtersGroup.js"></script>
    <script src="components/progressBar.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/fieldedInput.js"></script>
    <script src="components/loader.js"></script>


<?php

echo $OUTPUT->footer();