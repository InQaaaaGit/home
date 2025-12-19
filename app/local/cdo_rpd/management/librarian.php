<?php

require_once(__DIR__ . "/../../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;
$context = context_system::instance();
require_capability('local/cdo_rpd:view_admin_library', $context);
$PAGE->set_context($context);
$title = get_string('librarian_admin', 'local_cdo_rpd');

$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css'));
$url = '/local/cdo_rpd/management/librarian.php';
$PAGE->set_title($title);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->navbar->add($title, $url);
$PAGE->requires->js(new moodle_url('librarian.js'));

echo $OUTPUT->header();

?>
    <div id="librarian">
        <progress-bar
                color="#219653"
                title="Распределены"
                :all-disciplines="tableData.length"
                :current-disciplines="completedDisciplineLength"
        >
        </progress-bar>
        <progress-bar
                color="#EB5757"
                title="Не распределены"
                :all-disciplines="tableData.length"
                :current-disciplines="notAllocatedDisciplineLength"
        >
        </progress-bar>
        <r-divider></r-divider>
        <div
                class="filters-title"
                @click="showFilters = !showFilters">
            <h2 class="">Фильтр ОПОП </h2>
            <span
                    :class="{'c-arrow-transform' : showFilters}"
                    class="c-arrow-down"></span>
        </div>
        <transition name="bounce">
            <div class="root-filters-wrapper" v-if="showFilters">

                <multiselect
                        v-model="selectedDirection"
                        :options="directions"
                        placeholder="Специальность/направление подготовки:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="'Выбрать'">
                </multiselect>

                <multiselect
                        v-model="selectedEducationLevel"
                        :options="educationLevels"
                        placeholder="Уровень образования:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="'Выбрать'">
                </multiselect>
                <multiselect
                        v-model="selectedTrainingLevels"
                        :options="trainingLevels"
                        placeholder="Уровень подготовки:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="'Выбрать'">
                </multiselect>
                <multiselect
                        v-model="selectedCode"
                        :options="codes"
                        placeholder="Шифр:"
                        label="value"
                        track-by="value"
                        deselect-label="Удалить"
                        select-label="'Выбрать'">
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
                <a :href=`/${item.id}` class="structure-table__link">{{item.discipline}}</a>
            </template>

            <template v-slot:librarian="{item}">
                <div style="width: 385px">
                    <fielded-select
                            v-model="item.librarian"
                            placeholder="Выберите ответственного"
                            :items="userList"
                            item-name="user"
                            @input="changeDeveloper(item)"
                    ></fielded-select>
                </div>
            </template>

            <template v-slot:status="{item}">
                <div class="librarian-status" v-if="item.librarian">
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#27AE60"/>
                        <rect width="8.21318" height="0.921776" rx="0.460888"
                              transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
                        <rect width="5.99155" height="0.921776" rx="0.460888"
                              transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
                    </svg>
                </div>
                <div class="librarian-status" v-else>
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#EB5757"/>
                        <rect width="8.21318" height="0.921776" rx="0.460888"
                              transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
                        <rect width="5.99155" height="0.921776" rx="0.460888"
                              transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
                    </svg>
                </div>
            </template>
        </structure-table>

    </div>
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="components/rDivider.js"></script>
    <script src="components/structureTable.js"></script>
    <script src="components/filtersGroup.js"></script>
    <script src="components/progressBar.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/fieldedInput.js"></script>
    <script src="components/loader.js"></script>
<?php

echo $OUTPUT->footer();