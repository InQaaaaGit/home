<?php

require_once(__DIR__."/../../../config.php");

global $PAGE, $OUTPUT, $CFG, $USER;

$PAGE->set_context(context_system::instance());
#$title = get_string('management', 'block_rpd');
$title = "Согласование литературы";

$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->requires->css(new moodle_url('style.css'));
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/vue-toast-notification/dist/theme-sugar.css'));
$PAGE->set_title($title);
$PAGE->set_url('/blocks/rpd/management/admin.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->navbar->add('Список литературы', '/blocks/rpd/management/librarian-manager.php');
$PAGE->navbar->add($title, '/blocks/rpd/management/librarian-manager-confirm.php');

$PAGE->requires->js(new moodle_url('librarian-manager-confirm.js'));


echo $OUTPUT->header();

?>
    <div id="librarian-manager-confirm">
        <r-divider></r-divider>
        <div class="literature-confirm">
            <div v-if=" !!literatureList ">
            <div class="literature-confirm__title">11. УЧЕБНО-МЕТОДИЧЕСКОЕ И ИНФОРМАЦИОННОЕ ОБЕСПЕЧЕНИЕ ДИСЦИПЛИНЫ <b>"{{displayDiscipline}}"</b></div>
            <div class="literature-confirm__subtitle"> Список рекомендуемой литературы:</div>
            <div>
                <p class="literature-confirm__title-block">основная литература</p>
                <ul class="literature-confirm__list" >
                    <li
                            class="literature-confirm__item"
                            v-for="(book, i) in literatureList.mainSelected"
                            :key="book.id"
                    >
                        <div class="literature-confirm__description" @click.stop="deselectBook(book)">
                            <transition name="bounce">
                                <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="book.approval">
                                    <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#EB5757"/>
                                    <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
                                    <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
                                </svg>
                            </transition>
                            {{i+1}}. {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} URL: {{book.link}}
                        </div>
                        <transition name="bounce">
                            <fielded-input
                                    v-model.trim="book.commentary"
                                    v-if="book.approval"
                                    class="literature-confirm__input"
                                    :label="`Причина отказа ${i+1}`"
                            ></fielded-input>
                        </transition>
                    </li>
                </ul>

                <p class="literature-confirm__title-block">дополнительная литература</p>
                <ul class="literature-confirm__list">
                    <li
                            class="literature-confirm__item"
                            v-for="(book, i) in literatureList.additionalSelected"
                            :key="book.id"
                    >
                        <div class="literature-confirm__description" @click.stop="deselectBook(book)">
                            <transition name="bounce">
                                <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="book.approval">
                                    <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#EB5757"/>
                                    <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
                                    <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
                                </svg>
                            </transition>
                            {{i+1}}. {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} URL: {{book.link}}
                        </div>
                        <transition name="bounce">
                            <fielded-input
                                    v-model.trim="book.commentary"
                                    v-if="book.approval"
                                    class="literature-confirm__input"
                                    :label="`Причина отказа ${i+1}`"
                            ></fielded-input>
                        </transition>
                    </li>
                </ul>

                <p class="literature-confirm__title-block">учебно-методическая литература</p>
                <ul class="literature-confirm__list">
                    <li
                            class="literature-confirm__item"
                            v-for="(book, i) in literatureList.methodicalSelected"
                            :key="book.id"
                    >
                        <div class="literature-confirm__description" @click.stop="deselectBook(book)">
                            <transition name="bounce">
                                <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="book.approval">
                                    <ellipse cx="9.675" cy="9.5" rx="9.675" ry="9.5" fill="#EB5757"/>
                                    <rect width="8.21318" height="0.921776" rx="0.460888" transform="matrix(0.71353 -0.700624 0.71353 0.700624 8.75806 11.7544)" fill="white"/>
                                    <rect width="5.99155" height="0.921776" rx="0.460888" transform="matrix(0.71353 0.700624 -0.71353 0.700624 5.78613 7.55664)" fill="white"/>
                                </svg>
                            </transition>
                            {{i+1}}. {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} URL: {{book.link}}
                        </div>
                        <transition name="bounce">
                            <fielded-input
                                    v-model.trim="book.commentary"
                                    v-if="book.approval"
                                    class="literature-confirm__input"
                                    :label="`Причина отказа ${i+1}`"
                            ></fielded-input>
                        </transition>
                    </li>
                </ul>
            </div>
            <form>
                <fielded-textarea
                        label="Комментарий"
                        v-model="comment"
                >
                </fielded-textarea>
                <h2>Текущий статус: <b>{{showAlphabetStatus}}</b></h2>
                <div v-if = "alreadySend"
                     class="comment-actions">
                    <button
                            class="btn-confirm btn-confirm--mr10"
                            @click.prevent="approve"
                            :disabled="isDisabledApproveButton"
                    >
                        согласовать
                    </button>
                    <button class="btn-discard" @click.prevent="discard" >отклонить</button>
                </div>
            </form>
            </div>
            <div v-else><h2>Не найдена литература</h2></div>
            <loader v-show="isLoader"></loader>
        </div>

    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-toast-notification@0.6/dist/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
    <script src="components/loader.js"></script>
    <script src="components/rDivider.js"></script>
    <script src="components/structureTable.js"></script>
    <script src="components/progressBar.js"></script>
    <script src="components/fieldedSelect.js"></script>
    <script src="components/fieldedTextarea.js"></script>
    <script src="components/fieldedInput.js"></script>


<?php

echo $OUTPUT->footer();