const literature = {
    props: {
        booksList: {
            type: Object,
            required: false
        },
        currentUser: {
            type: String,
            required: false
        },
        blockControl: {
            type: String,
            required: false
        },
        guid: {
            type: String,
            required: false
        },
        coDev: {
            type:Boolean,
            default:false,
        },
        agreedStatus: {
            type: Object,
            required: false
        }
    },
    data: () => ({
        sliders: [false, false, false],
        literatureList: [],
        mainLiterature: [],
        additionalLiterature: [],
        methodicalLiterature: [],
        mainLiteratureSelected: [],
        additionalLiteratureSelected: [],
        methodicalLiteratureSelected: [],
        mainLiteratureSearch: {
            author: '',
            name: '',
        },
        additionalLiteratureSearch: {
            author: '',
            name: '',
        },
        methodicalLiteratureSearch: {
            author: '',
            name: '',
        },

        showMoreMainLiterature: true,
        showMoreAdditionalLiterature: true,
        showMoreMethodicalLiterature: true,

     //   isApproval: false,

        /*agreedStatus: {
            status: '2',
            text: 'Ответ от библиотеки'
        },*/

        questionForm: {
            theme: '',
            question: '',
            email: '',
            fio: '',
            phone: ''
        },
        showForm: false,
        isLoadMainLiterature: false,
        isLoadAdditionalLiterature: false,
        isLoadMethodicalLiterature: false,
        loading: false,
    }),
    created() {
        this.mainLiteratureSelected = this.booksList.mainSelected;
        this.additionalLiteratureSelected = this.booksList.additionalSelected;
        this.methodicalLiteratureSelected = this.booksList.methodicalSelected;
    },
    watch: {
        booksList: {
            handler: function (newVal, old) {
                this.mainLiteratureSelected = newVal.mainSelected;
                this.additionalLiteratureSelected = newVal.additionalSelected;
                this.methodicalLiteratureSelected = newVal.methodicalSelected;
            },
            immediate: true,
            deep: true
        },
    },
    methods: {
        /*checkApprovalForRPD() {
            const vm = this;
            this.loading = true;
            const rpd_id = this.findGetParameter('rpd_id');
            const user_id = this.currentUser;
            require(['core/ajax', 'core/notification', 'core/loadingicon'], function (ajax, notification, LoadingIcon) {

                var promises = ajax.call([
                    {
                        methodname: 'get_literature_for_approve',
                        args: {
                            user_id: user_id,
                            rpd_id: rpd_id,
                            guid: vm.guid,
                        }
                    }
                ]);
                promises[0].done((response) => {
                    vm.isLoader = false;
                    if (response) {
                        if (response.status == "2") {
                            vm.isApproval = true;
                        }
                        vm.$emit('get-status-approval', response.status);
                        vm.agreedStatus.status = response.status;
                        vm.agreedStatus.text = response.comment;
                        vm.mainLiteratureSelected = response.literature.mainSelected;
                        vm.additionalLiteratureSelected = response.literature.additionalSelected;
                        vm.methodicalLiteratureSelected = response.literature.methodicalSelected;
                        vm.saveBooksOnSelect();
                    }

                    vm.loading = false;
                }).fail(function (ex) {
                    vm.isLoader = false;
                    notification.exception(ex);
                    vm.loading = false;
                });
            });
        },*/
        saveBooksOnSelect() {
            let selectedBooks = {
                mainSelected: this.mainLiteratureSelected,
                additionalSelected: this.additionalLiteratureSelected,
                methodicalSelected: this.methodicalLiteratureSelected,
            };
            this.$emit('save-selected-books', selectedBooks);
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
        sendToApproval() {
            this.$emit('set-status', "2");
            //console.log(this.mainLiteratureSelected, this.additionalLiteratureSelected, this.methodicalLiteratureSelected);
            /*const selectedBooks = {
                mainSelected: this.mainLiteratureSelected,
                additionalSelected: this.additionalLiteratureSelected,
                methodicalSelected: this.methodicalLiteratureSelected,
            };
            const vm = this;
            const rpd_id = this.findGetParameter('rpd_id');
            // const guid = this.findGetParameter('guid');
            const guid = this.guid == null ? "" : this.guid;

            let structToSend = {
                literature: selectedBooks,
                rpd_id: rpd_id,
                user_id: vm.currentUser,
                blockControl: vm.blockControl,
                guid: guid.length > 0 ? guid : "",
                status: "2"
            }

            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'send_litarature_for_approve',
                            args: {
                                JSON: JSON.stringify(structToSend)
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        console.log(response)
                        vm.$toast.open({
                            message: `Отправлено на согласование`,
                            type: "success",
                            duration: 5000,
                            dismissible: true
                        });
                    }).fail(function (ex) {
                        notification.exception(ex);
                    });
                });*/
        },
        sendForm() {
            const vm = this;
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {

                    var promises = ajax.call([
                        {
                            methodname: 'send_mail_to_library',
                            args: {
                                subject: vm.questionForm.theme,
                                message: "Вопрос: " + vm.questionForm.question + " ФИО: " + vm.questionForm.fio + " Телефон: " + vm.questionForm.phone + " E-mail: " + vm.questionForm.email
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        for (let key in vm.questionForm) {
                            vm.questionForm[key] = ''
                        }
                        vm.$toast.open({
                            message: `Вопрос направлен в библиотеку.`,
                            type: "success",
                            duration: 5000,
                            dismissible: true
                        });
                    }).fail(function (ex) {
                        notification.exception(ex);
                    });
                });

       //     console.log(this.questionForm);

        },

        getMainBooks() {
            this.isLoadMainLiterature = true;
            const vm = this; //MAGIC
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'search_in_1c_library',
                            args: {
                                author: vm.mainLiteratureSearch.author,
                                name: vm.mainLiteratureSearch.name
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.mainLiterature = _.uniqBy(response, 'id');
                        vm.isLoadMainLiterature = false;
                    }).fail(function (ex) {
                        notification.exception(ex);
                        vm.isLoadMainLiterature = false;
                    });
                });
            //this.mainLiterature = this.literatureList;
        },
        deleteMainBook(bookID) {
            this.mainLiteratureSelected = this.mainLiteratureSelected.filter(item => item.id !== bookID);
            this.saveBooksOnSelect();
        },
        isCheckedMainBook(bookID) {
            return this.mainLiteratureSelected.filter(item => item.id === bookID).length
        },
        selectMainBook(book) {
            if (this.mainLiteratureSelected.filter(item => item.id === book.id).length) {
                this.mainLiteratureSelected = this.mainLiteratureSelected.filter(item => item.id !== book.id);
            } else {
                this.mainLiteratureSelected.push(_.cloneDeep(book));
            }
            this.saveBooksOnSelect();
        },

        getAdditionalBooks() {
            this.isLoadAdditionalLiterature = true;
            const vm = this; //MAGIC
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'search_in_1c_library',
                            args: {
                                author: vm.additionalLiteratureSearch.author,
                                name: vm.additionalLiteratureSearch.name
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.additionalLiterature = _.uniqBy(response, 'id');
                        vm.isLoadAdditionalLiterature = false;
                    }).fail(function (ex) {
                        notification.exception(ex);
                        vm.isLoadAdditionalLiterature = false;
                    });
                });
            //this.additionalLiterature = this.literatureList;
        },
        deleteAdditionalBook(bookID) {
            this.additionalLiteratureSelected = this.additionalLiteratureSelected.filter(item => item.id !== bookID);
            this.saveBooksOnSelect();
        },
        isCheckedAdditionalBook(bookID) {
            return this.additionalLiteratureSelected.filter(item => item.id === bookID).length
        },
        selectAdditionalBook(book) {
            if (this.additionalLiteratureSelected.filter(item => item.id === book.id).length) {
                this.additionalLiteratureSelected = this.additionalLiteratureSelected.filter(item => item.id !== book.id);
            } else {
                this.additionalLiteratureSelected.push(_.cloneDeep(book));
            }
            this.saveBooksOnSelect();
        },

        getMethodicalBooks() {
            this.isLoadMethodicalLiterature = true;
            const vm = this; //MAGIC
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'search_in_1c_library',
                            args: {
                                author: vm.methodicalLiteratureSearch.author,
                                name: vm.methodicalLiteratureSearch.name
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.methodicalLiterature = _.uniqBy(response, 'id');
                        vm.isLoadMethodicalLiterature = false;
                    }).fail(function (ex) {
                        notification.exception(ex);
                        vm.isLoadMethodicalLiterature = false;
                    });
                });
            //this.methodicalLiterature = this.literatureList;
        },
        deleteMethodicalBook(bookID) {
            this.methodicalLiteratureSelected = this.methodicalLiteratureSelected.filter(item => item.id !== bookID);
            this.saveBooksOnSelect();
        },
        isCheckedMethodicalBook(bookID) {
            return this.methodicalLiteratureSelected.filter(item => item.id === bookID).length
        },
        selectMethodicalBook(book) {
            if (this.methodicalLiteratureSelected.filter(item => item.id === book.id).length) {
                this.methodicalLiteratureSelected = this.methodicalLiteratureSelected.filter(item => item.id !== book.id);
            } else {
                this.methodicalLiteratureSelected.push(_.cloneDeep(book));
            }
            this.saveBooksOnSelect();
        },

        toggleItem(idx) {
            if (this.sliders[idx]) this.sliders.splice(idx, 1, false)
            else this.sliders.splice(idx, 1, true)
        },
        goNextStep(current, next) {
            this.sliders.splice(current, 1, false);
            this.sliders.splice(next, 1, true);
        }
    },
    computed: {

        isApproval(){
          return this.agreedStatus.status=="2";
        },
        sortMainLiterature() {
            return this.mainLiterature.sort((a, b) => a.year - b.year);
        },
        sortAdditionalLiterature() {
            return this.additionalLiterature.sort((a, b) => a.year - b.year);
        },
        sortMethodicalLiterature() {
            return this.methodicalLiterature.sort((a, b) => a.year - b.year);
        },
        allSelectedLiterature() {
            return [].concat(this.booksList.additionalSelected, this.booksList.mainSelected, this.booksList.methodicalSelected);
            return this.mainLiteratureSelected.concat(this.additionalLiteratureSelected, this.methodicalLiteratureSelected);
        },
        disabledAgreedButton(){
            return [].concat(this.booksList.additionalSelected, this.booksList.mainSelected, this.booksList.methodicalSelected).length === 0 ||
                ["1","2"].includes(this.agreedStatus.status);
        },
    },
    template: `
    <div>
      <div v-show="!showForm">
        <div class="literature-alert">
          <span class="text-red">Внимание:</span> При добавлении печатного источника в список основной и дополнительной литературы, обязательно учитывайте количество экземпляров в библиотеке  (СПО 1:1, ВО 3+ 1:2, ВО 3++ 1:4)
        </div>
        <div class="literature">
        
          <!--основная-->
          <template>
          <div class="literature__item">
            <div class="literature__header" @click="toggleItem(0)">
              <div 
                :class="{'literature__status--complete': mainLiteratureSelected.length}"
                class="literature__status"
               >
                <svg v-if="mainLiteratureSelected.length" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.50013 9.47487L2.02513 5.99987L0.841797 7.17487L5.50013 11.8332L15.5001 1.8332L14.3251 0.658203L5.50013 9.47487Z" fill="white"/>
                </svg>
                <span v-else>1</span>
              </div>
              Основная 
              <svg 
                class="literature__arrow" 
                :class="{'literature__arrow--rotate': sliders[0]}"
                width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 8L0.669873 0.499999L9.33013 0.5L5 8Z" fill="#4F4F4F"/>
                </svg>
            </div>
            <div class="literature__body">
              <transition name="slide">
                <div class="literature__content" v-show="sliders[0]">
                  <div class="literature__inputs-group" v-if="!coDev">
                    <fielded-input
                      v-model.trim="mainLiteratureSearch.author"
                      label="Автор"
                      placeholder="Не выбрано"
                    ></fielded-input>
                    <fielded-input
                      v-model.trim="mainLiteratureSearch.name"
                      label="Заглавие"
                      placeholder="Не выбрано"
                    ></fielded-input>
                  </div>
                  <div class="d-flex align-items-center" v-if="!coDev">
                    <button 
                      @click="getMainBooks"
                      class="btn-confirm mr-10" 
                      :disabled="!mainLiteratureSearch.author.length && !mainLiteratureSearch.name.length"
                      v-show="!isLoadMainLiterature"
                    >
                      поиск
                    </button>
                    <div class="lds-ring" v-show="isLoadMainLiterature"><div></div><div></div><div></div><div></div></div>
                    <button 
                      class="btn-discard" 
                      :disabled="!mainLiterature.length"
                      @click="mainLiterature = []"
                    >
                      Очистить
                    </button>
                  </div>
                  <div class="literature-list">
                    <div class="literature-list__item" v-for="book in sortMainLiterature" :key="book.id">
                      <input type="checkbox" :checked="isCheckedMainBook(book.id)" @change="selectMainBook(book)">
                      <span class="literature-list__text">
                       {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                      </span>
                    </div>
                  </div>
                  <div class="literature-added">
                  
                      <div class="literature-added__chips-block" v-if="!coDev">
                        <div 
                          :class="{'chip-red': book.approval}"
                          class="chip chip--margin" v-for="book in mainLiteratureSelected" :key="book.id">
                          <p class="chip__text">{{book.book}}</p>
                          <svg @click="deleteMainBook(book.id)" class="cursor-pointer" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M10 0C4.47 0 0 4.47 0 10C0 15.53 4.47 20 10 20C15.53 20 20 15.53 20 10C20 4.47 15.53 0 10 0ZM15 13.59L13.59 15L10 11.41L6.41 15L5 13.59L8.59 10L5 6.41L6.41 5L10 8.59L13.59 5L15 6.41L11.41 10L15 13.59Z" fill="white"/>
                          </svg>
                        </div>
                      </div>
                      
                      <div class="literature-added__show-more cursor-pointer" @click="showMoreMainLiterature = !showMoreMainLiterature">
                        <transition-group name="fade">
                          <svg key="1" v-show="!showMoreMainLiterature" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 0.5C6 0.5 1.73 3.61 0 8C1.73 12.39 6 15.5 11 15.5C16 15.5 20.27 12.39 22 8C20.27 3.61 16 0.5 11 0.5ZM11 13C8.24 13 6 10.76 6 8C6 5.24 8.24 3 11 3C13.76 3 16 5.24 16 8C16 10.76 13.76 13 11 13ZM11 5C9.34 5 8 6.34 8 8C8 9.66 9.34 11 11 11C12.66 11 14 9.66 14 8C14 6.34 12.66 5 11 5Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                          <svg key="2" v-show="showMoreMainLiterature" width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4C13.76 4 16 6.24 16 9C16 9.65 15.87 10.26 15.64 10.83L18.56 13.75C20.07 12.49 21.26 10.86 21.99 9C20.26 4.61 15.99 1.5 10.99 1.5C9.59 1.5 8.25 1.75 7.01 2.2L9.17 4.36C9.74 4.13 10.35 4 11 4ZM1 1.27L3.28 3.55L3.74 4.01C2.08 5.3 0.78 7.02 0 9C1.73 13.39 6 16.5 11 16.5C12.55 16.5 14.03 16.2 15.38 15.66L15.8 16.08L18.73 19L20 17.73L2.27 0L1 1.27ZM6.53 6.8L8.08 8.35C8.03 8.56 8 8.78 8 9C8 10.66 9.34 12 11 12C11.22 12 11.44 11.97 11.65 11.92L13.2 13.47C12.53 13.8 11.79 14 11 14C8.24 14 6 11.76 6 9C6 8.21 6.2 7.47 6.53 6.8ZM10.84 6.02L13.99 9.17L14.01 9.01C14.01 7.35 12.67 6.01 11.01 6.01L10.84 6.02Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                        </transition-group>
                      </div>


                      <div class="literature-added__full-information" v-show="showMoreMainLiterature">
                        <div class="literature-added__item" v-for="(book,i) in mainLiteratureSelected" :key="book.id">
                          <div>
                            <span class="literature-added__number">{{i+1}}</span> 
                            <span class="literature-added__text">
                              {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                            </span>
                          </div>
                          <div class="literature-added__status" v-if="book.approval">
                            <span class="text-red">Отклонено</span> - {{book.commentary}}
                          </div>
                        </div>
                      </div>
                      
                  </div>
                  <button class="btn-confirm btn-icon" 
                    @click="goNextStep(0, 1)"
                    :disabled="!mainLiteratureSelected.length">далее
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z" fill="white"/>
                    </svg>
                  </button>
                </div>
              </transition>
            </div>
          </div>
          
          <!--дополнительная-->
          <div class="literature__item">
            <div class="literature__header" @click="toggleItem(1)">
              <div 
                :class="{'literature__status--complete': additionalLiteratureSelected.length}"
                class="literature__status"
              >
                <svg v-if="additionalLiteratureSelected.length" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5.50013 9.47487L2.02513 5.99987L0.841797 7.17487L5.50013 11.8332L15.5001 1.8332L14.3251 0.658203L5.50013 9.47487Z" fill="white"/>
                </svg>
                <span v-else>2</span>
              </div>
              Дополнительная 
              <svg class="literature__arrow" 
                :class="{'literature__arrow--rotate': sliders[1]}"
                width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 8L0.669873 0.499999L9.33013 0.5L5 8Z" fill="#4F4F4F"/>
                </svg>
            </div>
            <div class="literature__body">
              <transition name="slide">
                <div class="literature__content" v-show="sliders[1]">
                  <div class="literature__inputs-group" v-if="!coDev">
                    <fielded-input
                      v-model.trim="additionalLiteratureSearch.author"
                      label="Автор"
                      placeholder="Не выбрано"
                    ></fielded-input>
                    <fielded-input
                      v-model.trim="additionalLiteratureSearch.name"
                      label="Заглавие"
                      placeholder="Не выбрано"
                    ></fielded-input>
                  </div>
                  <div class="d-flex align-items-center" v-if="!coDev">
                    <button 
                      @click="getAdditionalBooks"
                      class="btn-confirm mr-10" 
                      :disabled="!additionalLiteratureSearch.author.length && !additionalLiteratureSearch.name.length"
                      v-if="!isLoadAdditionalLiterature"
                    >
                      поиск
                    </button>
                    <div class="lds-ring" v-show="isLoadAdditionalLiterature"><div></div><div></div><div></div><div></div></div>
                    <button 
                      class="btn-discard" 
                      :disabled="!mainLiterature.length"
                      @click="additionalLiterature = []"
                    >
                      Очистить
                    </button>
                  </div>
                  <div class="literature-list">
                    <div class="literature-list__item" v-for="book in sortAdditionalLiterature" :key="book.id">
                      <input type="checkbox" :checked="isCheckedAdditionalBook(book.id)" @change="selectAdditionalBook(book)">
                      <span class="literature-list__text">
                       {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                      </span>
                    </div>
                  </div>
                  <div class="literature-added">
                  
                      <div class="literature-added__chips-block" v-if="!coDev">
                        <div 
                          :class="{'chip-red': book.approval}"
                          class="chip chip--margin" v-for="book in additionalLiteratureSelected" :key="book.id">
                          <p class="chip__text">{{book.book}}</p>
                          <svg @click="deleteAdditionalBook(book.id)" class="cursor-pointer" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M10 0C4.47 0 0 4.47 0 10C0 15.53 4.47 20 10 20C15.53 20 20 15.53 20 10C20 4.47 15.53 0 10 0ZM15 13.59L13.59 15L10 11.41L6.41 15L5 13.59L8.59 10L5 6.41L6.41 5L10 8.59L13.59 5L15 6.41L11.41 10L15 13.59Z" fill="white"/>
                          </svg>
                        </div>
                      </div>
                      
                      <div class="literature-added__show-more cursor-pointer" @click="showMoreAdditionalLiterature = !showMoreAdditionalLiterature">
                        <transition-group name="fade">
                          <svg key="1" v-show="!showMoreAdditionalLiterature" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 0.5C6 0.5 1.73 3.61 0 8C1.73 12.39 6 15.5 11 15.5C16 15.5 20.27 12.39 22 8C20.27 3.61 16 0.5 11 0.5ZM11 13C8.24 13 6 10.76 6 8C6 5.24 8.24 3 11 3C13.76 3 16 5.24 16 8C16 10.76 13.76 13 11 13ZM11 5C9.34 5 8 6.34 8 8C8 9.66 9.34 11 11 11C12.66 11 14 9.66 14 8C14 6.34 12.66 5 11 5Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                          <svg key="2" v-show="showMoreAdditionalLiterature" width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4C13.76 4 16 6.24 16 9C16 9.65 15.87 10.26 15.64 10.83L18.56 13.75C20.07 12.49 21.26 10.86 21.99 9C20.26 4.61 15.99 1.5 10.99 1.5C9.59 1.5 8.25 1.75 7.01 2.2L9.17 4.36C9.74 4.13 10.35 4 11 4ZM1 1.27L3.28 3.55L3.74 4.01C2.08 5.3 0.78 7.02 0 9C1.73 13.39 6 16.5 11 16.5C12.55 16.5 14.03 16.2 15.38 15.66L15.8 16.08L18.73 19L20 17.73L2.27 0L1 1.27ZM6.53 6.8L8.08 8.35C8.03 8.56 8 8.78 8 9C8 10.66 9.34 12 11 12C11.22 12 11.44 11.97 11.65 11.92L13.2 13.47C12.53 13.8 11.79 14 11 14C8.24 14 6 11.76 6 9C6 8.21 6.2 7.47 6.53 6.8ZM10.84 6.02L13.99 9.17L14.01 9.01C14.01 7.35 12.67 6.01 11.01 6.01L10.84 6.02Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                        </transition-group>
                      </div>


                      <div class="literature-added__full-information" v-show="showMoreAdditionalLiterature">
                        <div class="literature-added__item" v-for="(book,i) in additionalLiteratureSelected" :key="book.id">
                          <div>
                            <span class="literature-added__number">{{i+1}}</span> 
                            <span class="literature-added__text">
                              {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                            </span>
                          </div>
                          <div class="literature-added__status" v-if="book.approval">
                            <span class="text-red">Отклонено</span> - {{book.commentary}}
                          </div>
                        </div>
                      </div>
                      
                  </div>
                  <button class="btn-confirm btn-icon" @click="goNextStep(1, 2)" :disabled="!additionalLiteratureSelected.length">далее
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z" fill="white"/>
                    </svg>
                  </button>
                </div>
              </transition>
            </div>
          </div>
          
          <!--Учебно-методическая-->
          <div class="literature__item">
            <div class="literature__header" @click="toggleItem(2)">
              <div 
                :class="{'literature__status--complete': methodicalLiteratureSelected.length}"
                class="literature__status"
              >
                <svg v-if="methodicalLiteratureSelected.length" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5.50013 9.47487L2.02513 5.99987L0.841797 7.17487L5.50013 11.8332L15.5001 1.8332L14.3251 0.658203L5.50013 9.47487Z" fill="white"/>
                </svg>
                <span v-else>3</span>
              </div>
              Учебно-методическая  
              <svg class="literature__arrow" 
                :class="{'literature__arrow--rotate': sliders[2]}"
              width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 8L0.669873 0.499999L9.33013 0.5L5 8Z" fill="#4F4F4F"/>
                </svg>
            </div>
            <div class="literature__body">
              <transition name="slide">
                <div class="literature__content" v-show="sliders[2]">
                  <div class="literature__inputs-group" v-if="!coDev">
                    <fielded-input
                      v-model.trim="methodicalLiteratureSearch.author"
                      label="Автор"
                      placeholder="Не выбрано"
                    ></fielded-input>
                    <fielded-input
                      v-model.trim="methodicalLiteratureSearch.name"
                      label="Заглавие"
                      placeholder="Не выбрано"
                    ></fielded-input>
                  </div>
                  <div class="d-flex align-items-center" v-if="!coDev">
                    <button 
                      @click="getMethodicalBooks"
                      class="btn-confirm mr-10" 
                      :disabled="!methodicalLiteratureSearch.author.length && !methodicalLiteratureSearch.name.length"
                      v-if="!isLoadMethodicalLiterature"
                    >
                      поиск
                    </button>
                    <div class="lds-ring" v-show="isLoadMethodicalLiterature"><div></div><div></div><div></div><div></div></div>
                    <button 
                      class="btn-discard" 
                      :disabled="!mainLiterature.length"
                      @click="methodicalLiterature = []"
                    >
                      Очистить
                    </button>
                  </div>
                  <div class="literature-list">
                    <div class="literature-list__item" v-for="book in sortMethodicalLiterature" :key="book.id">
                      <input type="checkbox" :checked="isCheckedMethodicalBook(book.id)" @change="selectMethodicalBook(book)">
                      <span class="literature-list__text">
                       {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                      </span>
                    </div>
                  </div>
                  <div class="literature-added">
                  
                      <div class="literature-added__chips-block" v-if="!coDev">
                        <div 
                          :class="{'chip-red': book.approval}"
                          class="chip chip--margin" v-for="book in methodicalLiteratureSelected" :key="book.id">
                          <p class="chip__text">{{book.book}}</p>
                          <svg @click="deleteMethodicalBook(book.id)" class="cursor-pointer" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M10 0C4.47 0 0 4.47 0 10C0 15.53 4.47 20 10 20C15.53 20 20 15.53 20 10C20 4.47 15.53 0 10 0ZM15 13.59L13.59 15L10 11.41L6.41 15L5 13.59L8.59 10L5 6.41L6.41 5L10 8.59L13.59 5L15 6.41L11.41 10L15 13.59Z" fill="white"/>
                          </svg>
                        </div>
                      </div>
                      
                      <div class="literature-added__show-more cursor-pointer" @click="showMoreMethodicalLiterature = !showMoreMethodicalLiterature">
                        <transition-group name="fade">
                          <svg key="1" v-show="!showMoreMethodicalLiterature" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 0.5C6 0.5 1.73 3.61 0 8C1.73 12.39 6 15.5 11 15.5C16 15.5 20.27 12.39 22 8C20.27 3.61 16 0.5 11 0.5ZM11 13C8.24 13 6 10.76 6 8C6 5.24 8.24 3 11 3C13.76 3 16 5.24 16 8C16 10.76 13.76 13 11 13ZM11 5C9.34 5 8 6.34 8 8C8 9.66 9.34 11 11 11C12.66 11 14 9.66 14 8C14 6.34 12.66 5 11 5Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                          <svg key="2" v-show="showMoreMethodicalLiterature" width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4C13.76 4 16 6.24 16 9C16 9.65 15.87 10.26 15.64 10.83L18.56 13.75C20.07 12.49 21.26 10.86 21.99 9C20.26 4.61 15.99 1.5 10.99 1.5C9.59 1.5 8.25 1.75 7.01 2.2L9.17 4.36C9.74 4.13 10.35 4 11 4ZM1 1.27L3.28 3.55L3.74 4.01C2.08 5.3 0.78 7.02 0 9C1.73 13.39 6 16.5 11 16.5C12.55 16.5 14.03 16.2 15.38 15.66L15.8 16.08L18.73 19L20 17.73L2.27 0L1 1.27ZM6.53 6.8L8.08 8.35C8.03 8.56 8 8.78 8 9C8 10.66 9.34 12 11 12C11.22 12 11.44 11.97 11.65 11.92L13.2 13.47C12.53 13.8 11.79 14 11 14C8.24 14 6 11.76 6 9C6 8.21 6.2 7.47 6.53 6.8ZM10.84 6.02L13.99 9.17L14.01 9.01C14.01 7.35 12.67 6.01 11.01 6.01L10.84 6.02Z" fill="black" fill-opacity="0.54"/>
                          </svg>
                        </transition-group>
                      </div>


                      <div class="literature-added__full-information" v-show="showMoreMethodicalLiterature">
                        <div class="literature-added__item" v-for="(book,i) in methodicalLiteratureSelected" :key="book.id">
                          <div>
                            <span class="literature-added__number">{{i+1}}</span> 
                            <span class="literature-added__text">
                              {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
                            </span>
                          </div>
                          <div class="literature-added__status" v-if="book.approval">
                            <span class="text-red">Отклонено</span> - {{book.commentary}}
                          </div>
                        </div>
                      </div>
                      
                  </div>
                </div>
              </transition>
            </div>
          </div>

          </template>
          <div class="literature__item">
            <div class="literature__header cursor-default">
              <div 
                :class="{'literature__status--complete':isApproval}"
                class="literature__status"
              >
                <svg v-if="isApproval" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5.50013 9.47487L2.02513 5.99987L0.841797 7.17487L5.50013 11.8332L15.5001 1.8332L14.3251 0.658203L5.50013 9.47487Z" fill="white"/>
                </svg>
                <span v-else>4</span>
              </div>
              На согласовании
            </div>
            <div class="literature__body">
            </div>
          </div>
          
          
          <div class="literature__item">
            <div class="literature__header cursor-default">
              <div
                :class="{'literature__status--warning': agreedStatus.status === '3', 'literature__status--success': agreedStatus.status === '1',}"
                class="literature__status"
              >
                !
              </div>
              Результат согласования
            </div>
            <div class="literature__body literature__body--empty" v-if="agreedStatus?.text">
              <p class="agreed-text">
                <span class="text-red">*</span>{{agreedStatus?.text}}
              </p>
            </div>
          </div>
          
          <div class="all-literature-block">
            <h4>Весь список выбранной литературы</h4>
            <ol class="all-literature-block__list">
              <li class="all-literature-block__item" v-for="(book,idx) in allSelectedLiterature" :key="idx">
                {{book.author}} {{book.book}} {{book.publishing}} {{book.year}} {{book.link}} Количество: {{book.count}}
               
                <div class="text-red" v-if="book.commentary !== null && book.commentary.length > 1 ">
                  Комментарий: {{book.commentary}}
                </div>
              </li>
            </ol>
          </div>
        <div class="literature-actions" v-if="!coDev">
          <button :disabled="disabledAgreedButton || loading" class="btn-confirm btn-icon btn--mr20" @click="sendToApproval">
            отправить на согласование
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z" fill="white"/>
            </svg>
          </button>
         <!-- <button class="btn-discard" @click="showForm = true">задать вопрос</button>-->
        </div>  
        </div>
      </div>
      <form class="literature-question" v-show="showForm">
        <div class="literature-question__item">
          <fielded-input
            v-model="questionForm.theme"
            label="Тема"
          ></fielded-input>
        </div>
        
        <div class="literature-question__item">
          <fielded-textarea
            v-model="questionForm.question"
            label="Вопрос"
          ></fielded-textarea>
        </div>
        <div class="literature-question__item">
          <fielded-input
            v-model="questionForm.email"
            label="E-mail"
          ></fielded-input>
        </div>
        
        <div class="literature-question__item">
          <fielded-input
            v-model="questionForm.fio"
            label="ФИО"
          ></fielded-input>
        </div>
        <div class="literature-question__item">
          <fielded-input
            v-model="questionForm.phone"
            label="Телефон"
          ></fielded-input>
        </div>
        <div class="literature-actions">
          <button class="btn-confirm btn--mr20" @click.prevent="sendForm">
            Отправить
          </button>
          <button class="btn-discard" type="button" @click="showForm = false">Вернуться к заполнению</button>
        </div>
      </form>
    </div>
  `
}