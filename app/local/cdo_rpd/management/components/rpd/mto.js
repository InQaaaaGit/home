const mto = {
    props: {
        chosen: {
            type: Array,
            required: false
        },
        loader: {
            type: Boolean,
            required: false
        },
        coDev: {
            type: Boolean,
            default:false,
        }
    },
    data: () => ({
        building: [],
        buildingSelected: '',
        auditoriumList: [],
        auditoriumSelected: '',
        inventoryList: [],
        softwareList: [],
    }),
    created() {
        this.getBuildings();
    },
    computed: {
        getUniqInventory() {
            let allInventory = [];
            this.chosen.forEach(item => {
                item.auditorium.forEach(auditory => {
                    allInventory = allInventory.concat(auditory.inventory);
                })
            });
            let returningArray = Array.from(new Set(allInventory.map(JSON.stringify))).map(JSON.parse);
            returningArray.push(
                {
                    fullname: 'Мультимедийное оборудование: компьютер/ноутбук, экран, проектор/телевизор',
                    uid: '1'
                },
                {
                    fullname: 'Компьютерная техника',
                    uid: '2'
                }
            );

            return returningArray;
        },
        getUniqSoftware() {
            let allSoftware = [];
            this.chosen.forEach(item => {
                item.auditorium.forEach(software => {
                    allSoftware = allSoftware.concat(software.software);
                })
            })
            let returningArray = Array.from(new Set(allSoftware.map(JSON.stringify))).map(JSON.parse);
            returningArray.push(
                {
                    fullname: 'Операционная система "Альт образование"',
                    uid: '3'
                },
                {
                    fullname: 'Офисный пакет "Мой офис"',
                    uid: '4'
                }
            );
            return returningArray;
        },
    },
    methods: {
        setLoaderStatus(status) {
            this.$emit('status-loader', status);
        },
        getBuildings() {
            const vm = this;
            this.setLoaderStatus(true);
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'get_building_from_1c',
                            args: {}
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.building = response;
                        vm.setLoaderStatus(false);
                    }).fail(function (ex) {
                        vm.setLoaderStatus(false);
                        notification.exception(ex);
                    });
                });
        },
        getAuditorium(uid) {
            const vm = this;
            this.setLoaderStatus(true);
            require(['core/ajax', 'core/notification', 'core/loadingicon'],
                function (ajax, notification, LoadingIcon) {
                    var promises = ajax.call([
                        {
                            methodname: 'get_auditorium_by_parent_building_from_1c',
                            args: {
                                parent_uid: uid
                            }
                        }
                    ]);
                    promises[0].done((response) => {
                        vm.auditoriumList = response;
                        vm.setLoaderStatus(false);
                    }).fail(function (ex) {
                        vm.setLoaderStatus(false);
                        notification.exception(ex);
                    });
                });
        },
        getInventories(uid) {
            const vm = this;
            vm.setLoaderStatus(true);
            if (!!uid) {
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {
                        var promises = ajax.call([
                            {
                                methodname: 'get_inventory_by_auditorium_from_1c',
                                args: {
                                    location_room_uid: uid
                                }
                            }
                        ]);
                        promises[0].done((response) => {
                            vm.inventoryList = response.filter(item =>
                                !item.fullname.toLowerCase().includes('компьютер') /*||
                            !item.fullname.toLowerCase().includes('экран') ||
                            !item.fullname.toLowerCase().includes('проектор') ||
                            !item.fullname.toLowerCase().includes('мультимедиа-проектор') ||
                            !item.fullname.toLowerCase().includes('кронштейн') ||
                            !item.fullname.toLowerCase().includes('кронштейн для проектора') ||
                            !item.fullname.toLowerCase().includes('калькулятор')*/
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('экран')
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('ноутбук')
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('проектор')
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('мультимедиа-проектор')
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('кронштейн')
                            );
                            vm.inventoryList = vm.inventoryList.filter(item =>
                                !item.fullname.toLowerCase().includes('калькулятор')
                            );
                            vm.inventoryList = _.uniqBy(vm.inventoryList, "fullname")
                            //vm.inventoryList = response;
                            vm.setLoaderStatus(false);
                        }).fail(function (ex) {
                            vm.setLoaderStatus(false);
                            notification.exception(ex);
                        });
                    });
            }
        },
        getSoftware(uid) {
            const vm = this;
            vm.setLoaderStatus(true);
            if (!!uid) {
                require(['core/ajax', 'core/notification', 'core/loadingicon'],
                    function (ajax, notification, LoadingIcon) {
                        var promises = ajax.call([
                            {
                                methodname: 'get_software_by_auditorium_from_1c',
                                args: {
                                    location_room_uid: uid
                                }
                            }
                        ]);
                        promises[0].done((response) => {
                            vm.softwareList = response.filter(item =>
                                !item.fullname.toLowerCase().includes('microsoft office')
                            );
                            vm.softwareList = vm.softwareList.filter(item =>
                                !item.fullname.toLowerCase().includes('calculate linux')
                            );
                            vm.softwareList = vm.softwareList.filter(item =>
                                !item.fullname.toLowerCase().includes('windows')
                            );
                            vm.softwareList = _.uniqBy(vm.softwareList, "fullname");
                            vm.setLoaderStatus(false);
                        }).fail(function (ex) {
                            vm.setLoaderStatus(false);
                            notification.exception(ex);
                        });
                    })
            }
        },
        deleteGroup(uid) {
            this.chosen = this.chosen.filter(item => item.uid !== uid);
        },

        deleteItem(groupUid, auditoryUid, itemObj, key) {
            //ищем индекс строения в списке выбранных
            const indexBuildByUid = this.chosen.findIndex(item => item.uid === groupUid);
            //ищем индекс аудитории в списке выбранных
            const indexAuditoryByUid = this.chosen[indexBuildByUid].auditorium.findIndex(item => item.uid === auditoryUid);
            this.chosen[indexBuildByUid].auditorium[indexAuditoryByUid][key] =
                this.chosen[indexBuildByUid].auditorium[indexAuditoryByUid][key].filter(item => item.uid !== itemObj.uid);
        },

        deleteAuditory(groupUid, auditoryUid) {
            const indexBuildByUid = this.chosen.findIndex(item => item.uid === groupUid);

            this.chosen[indexBuildByUid].auditorium =
                this.chosen[indexBuildByUid].auditorium.filter(item => item.uid !== auditoryUid);
            if (!this.chosen[indexBuildByUid].auditorium.length) {
                this.chosen.splice(indexBuildByUid, 1);
            }
        },

        editChosenByCheckbox(itemObj, key) {
            // если выбрали здание, сюда придет его индекс или -1 если не найдено
            const hasBuilding = this.chosen.findIndex(item => item.uid === this.buildingSelected.uid);
            // если выбрали аудиторию и здание, сюда придет индекс аудитории или -1 если не найдено
            const hasAuditory = this.chosen[hasBuilding]?.auditorium.findIndex(item => item.uid === this.auditoriumSelected.uid);
            //если имеется такая аудитория в выбранном здание, то смотрим есть ли у нас выбранный элемент (есть - добавляем, нет - удаляем)
            if (hasAuditory !== -1 && hasBuilding !== -1) {
                const list = this.chosen[hasBuilding].auditorium[hasAuditory][key];
                //если есть
                if (list.some(item => item.uid === itemObj.uid)) {
                    this.chosen[hasBuilding].auditorium[hasAuditory][key] = this.chosen[hasBuilding].auditorium[hasAuditory][key]
                        .filter(item => item.uid !== itemObj.uid);
                } else {
                    this.chosen[hasBuilding].auditorium[hasAuditory][key].push({...itemObj})
                }
            } else {
                // если нет здания, то нам его нужно добавить вместе с аудиторией и айтемом
                if (hasBuilding === -1) {
                    const newBuilding = () => ({
                        "uid": this.buildingSelected.uid,
                        "code": this.buildingSelected.code,
                        "name": this.buildingSelected.name,
                        auditorium: [
                            {
                                "uid": this.auditoriumSelected.uid,
                                "code": this.auditoriumSelected,
                                "name": this.auditoriumSelected.name,
                                inventory: [],
                                software: []
                            }
                        ],
                    });
                    const createBuilding = newBuilding();
                    createBuilding.auditorium[0][key].push({...itemObj});
                    this.chosen.push(createBuilding);
                } else {
                    //если здание есть, то нам в него нужно добавить аудиторию
                    const newAuditory = () => ({
                        "uid": this.auditoriumSelected.uid,
                        "code": this.auditoriumSelected.code,
                        "name": this.auditoriumSelected.name,
                        inventory: [],
                        software: []
                    });
                    const createAuditory = newAuditory();
                    createAuditory[key].push({...itemObj});
                    this.chosen[hasBuilding].auditorium.push(createAuditory);
                }
            }
        },
        isSelected(uid, key) {
            //есть ли в существующих нужное здание
            const hasBuilding = this.chosen.findIndex(item => item.uid === this.buildingSelected.uid);
            //если нашли здание
            if (hasBuilding !== -1) {
                //ищем есть ли нужная аудитория в текущем здание
                const hasAuditory = this.chosen[hasBuilding].auditorium.findIndex(item => item.uid === this.auditoriumSelected.uid);
                //если есть аудитория, то проверяем есть ли в ней хоть один нужный нам uid
                if (hasAuditory !== -1) {
                    return this.chosen[hasBuilding].auditorium[hasAuditory][key].some(item => item.uid === uid);
                }
            }
            //просто явно возвращаем false, иначе функция вернет undefined
            return false
        }
    },
    watch: {
        buildingSelected(newVal) {
            this.getAuditorium(newVal.uid);
            this.auditoriumSelected = '';
        },
        auditoriumSelected(newVal) {
            this.getInventories(newVal.uid);
            this.getSoftware(newVal.uid);
        },
        chosen: {
            handler: function (val, oldVal) {
                val.forEach((item, i) => {
                    item.auditorium.forEach((auditory, idx) => {
                        if (!auditory.inventory.length && !auditory.software.length) {
                            this.chosen[i].auditorium.splice(idx, 1);
                        }
                    })
                });
                this.$emit('edit-mto', val);
            },
            deep: true
        },
    },
    template: `
    <div>
        <h4 style="text-align: justify;">Выберите из списков ниже корпус и аудиторию, в которой предполагается проводить занятия, и затем в появившихся списках выберите необходимое оборудование и программное обеспечение. Не выбирайте аудитории, если в учебном процессе не предусмотрено специфическое оборудование или программное обеспечение. Обратите внимание, что при необходимости Вы можете последовательно просмотреть несколько аудиторий</h4>
      <div class="mto-search" v-if="!coDev">
        <fielded-select
          label="Выберите корпус"
          :items="building"
          v-model="buildingSelected"
          item-name="name"
        ></fielded-select>
        <fielded-select
          label="Выберите аудиторию"
          :items="auditoriumList"
          v-model="auditoriumSelected"
          item-name="name"
        ></fielded-select>
      </div>
      <div v-show="auditoriumSelected">
        <div v-if="inventoryList.length">
          <p class="mto-title">Материально-техническое обеспечение</p>
          <div class="mto-list">
            <div class="mto-list__item" v-for="inventory in inventoryList" :key="inventory.uid">
              <input 
                @change="editChosenByCheckbox(inventory, 'inventory')"
                :checked="isSelected(inventory.uid, 'inventory')" 
                type="checkbox" 
               >
              <p class="mto-list__text">{{inventory.fullname}}</p>
            </div>
          </div>
        </div>
        <div v-else>Оборудование не найдено.</div>
        <div v-if="softwareList.length">
          <p class="mto-title">Програмное обеспечение</p>
          <div class="mto-list">
            <div class="mto-list__item" v-for="software in softwareList" :key="software.uid">
              <input 
                @change="editChosenByCheckbox(software, 'software')"
                :checked="isSelected(software.uid, 'software')" 
                type="checkbox"
              >
              <p class="mto-list__text">{{software.fullname}}</p>
            </div>
          </div>
        </div>
        <div v-else>Программное обеспечение не найдено.</div>
      </div>
      <r-divider></r-divider>
      <p class="mto-title">Полный перечень</p>
      <table class="mto-table">
        <thead>
          <th>Корпус</th>
          <th>Аудитория</th>
          <th>Оборудование</th>
          <th>Програмное обеспечение</th>
        </thead>
        <tbody v-for="(group,i) in chosen" :key="group.uid">
        
          <tr v-for="(auditory, idx) in group.auditorium" :key="auditory.uid">
            <td :rowspan="group.auditorium.length" :class="{'hide-td': idx !== 0}">
              <div class="mto-table__item">
                <svg v-if="!coDev"
                   @click="deleteGroup(group.uid)"
                  class="delete-row" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#EB5757"/>
                </svg>
                 {{group.name}}
              </div>
            </td>
            
            <td>
              <div class="mto-table__item">
                {{auditory.name}} 
                <svg v-if="!coDev"
                  @click="deleteAuditory(group.uid, auditory.uid)"
                  class="delete-row" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#EB5757"/>
                </svg>
              </div>
            </td>
            
            <td>
              <div class="mto-table__item" v-for="(inventory, index) in auditory.inventory">
                <p class="mto-table__item-text">
                  {{inventory.fullname}}
                </p>
                <svg  v-if="!coDev"
                  @click="deleteItem(group.uid, auditory.uid, inventory, 'inventory')"
                  class="delete-row-item" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#EB5757"/>
                </svg>
              </div>
            </td>
            
            <td>
              <div class="mto-table__item" v-for="(software, index) in auditory.software">
                <p class="mto-table__item-text">
                  {{software.fullname}}
                </p>
                <svg v-if="!coDev"
                  @click="deleteItem(group.uid, auditory.uid, software, 'software')"
                  class="delete-row-item" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#EB5757"/>
                </svg>
              </div>
            </td>
            
          </tr>
        </tbody>
      </table>
      <div class="mto-selected">
        <p class="mto-title">Перечень основного оборудования</p>
        <div class="mto-selected__list" v-show="getUniqInventory.length">
          <div class="mto-selected__item" v-for="inventory in getUniqInventory" :key="inventory.uid">
            {{inventory.fullname}}
          </div>
        </div>
        <p class="mto-title mto-title--mb-0">Перечень программного обеспечения</p>
        <div class="mto-selected__list" v-show="getUniqSoftware.length">
          <div class="mto-selected__item" v-for="software in getUniqSoftware" :key="software.uid">
            {{software.fullname}}
          </div>
        </div>
      </div>
      <div style="padding:15px;">
      <p style="font-weight:bold:font-size:18px;"><a target="_blank" href="https://drive.google.com/file/d/1fONBaeb1gwwYuOk-V21OJrQlTibXpAXM/view?usp=drive_link">Видеоинструкция - МТО</a></p>
 </div>
    </div>
  `
}