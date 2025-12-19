<template>

  <div v-if="this.$store.state.statusApplication">
    <v-container>
      <v-row class="d-flex align-center">
        <v-col md="3">
          <v-select
              :items="filters.edu_level"
              :label="strings.labels.edu_level"
              v-model="edu_level"
              attach
              chips
              multiple
          ></v-select>
        </v-col>
        <v-col md="3">
          <v-select
              :items="filters.edu_form"
              :label="strings.labels.edu_form"
              v-model="edu_form"
              attach
              chips
              multiple
          ></v-select>
        </v-col>
        <v-col md="3">
          <v-select
              :items="filters.years"
              :label="strings.labels.years"
              v-model="years"
              attach
              chips
              multiple
          ></v-select>
        </v-col>
        <v-col md="3">
          <v-btn @click="clearAllInputs" color="orange">{{ strings.buttons.clear }}</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6">
          <v-select
              :items="filters.edu_division"
              :label="strings.labels.edu_division"
              v-model="edu_division"
              attach
              chips
              multiple
          ></v-select>
        </v-col>
        <v-col md="6">
          <v-select
              :items="filters.edu_speciality"
              :label="strings.labels.edu_speciality"
              v-model="edu_speciality"
              attach
              chips
              multiple

          ></v-select>
        </v-col>
      </v-row>
      <v-row v-if="!isGEK">
        <v-col md="8">
          <v-radio-group column v-model="status">
            <v-radio
                :label="status_item.name"
                :value="status_item.id"
                v-for="status_item in this.$store.state.status"
                :key="status_item.id">
            </v-radio>

          </v-radio-group>
        </v-col>
        <v-col md="4" v-if="gekMode">
          <v-btn @click="setGEK" class="primary">
            {{ strings.phrases.switchModeGEK }}
          </v-btn>
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col>
          <v-btn @click="setManage" class="primary" v-if="!managerVKRsEmpty">
            {{ strings.phrases.switchModeManager }}
          </v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="12">
          <v-data-table
              :footer-props="{
                                'items-per-page-options': [10, 20, 50, 100]
                            }"
              :headers="headers"
              :items="searchedVKRsByFilter"
              :items-per-page="10"
              class="elevation-1"
              :loading="!vkrs.length"
              :loading-text="strings.loadings.loadingVKR"
              @click:row="chooseVKR"
          >
            <template v-slot:item.status="{item}">
              <div v-if="!isGEK">
                {{ writeVKRStatusName(item.status.id) }}
              </div>

            </template>
            <template v-slot:item.status.changed="{item}">
              <template v-if="item.status.changed && !isGEK">
                <i class="fas fa-exclamation-triangle text-danger"></i>
              </template>
            </template>

            <!--                    <template v-slot:item.FIO="{item}">
                                    <span @click="chooseVKR(item)">{{ item.FIO }} </span>
                                </template>-->
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </div>
  <div v-else>
    <UserVKR :item-v-k-r="this.$store.state.itemVKR" :is-g-e-k="isGEK"></UserVKR>
  </div>
</template>

<script>
import Jabber from "jabber";
import _ from "lodash";
import UserVKR from "@/Components/The-User-VKR.vue";
import Loader from "@/Components/Loader.vue";
import utility from "@/utility";

export default {
  components: {Loader, UserVKR},
  data: () => ({
    strings: {},
    filters: {
      edu_level: [],
      edu_form: [],
      edu_division: [],
      edu_speciality: [],
      years: [],
      status: []
    },
    clearedControls: [
      'years', 'edu_level', 'search', 'edu_division', 'edu_form', 'edu_speciality', 'status'
    ],
    isGEK: false,
    edu_level: '',
    edu_form: '',
    edu_division: '',
    edu_speciality: '',
    years: '',
    status: '',
    loaderUp: false,
    search: '',
    vkrs: [],
    headers: [
      {
        text: 'Фамилия имя отчество',
        align: 'start',
        sortable: true,
        value: 'FIO',
        class: 'blue lighten-4'
      },
      {text: 'Учебная группа', value: 'edu_group', class: 'blue lighten-4'},
      {text: 'Учебное подразделение', value: 'edu_division', class: 'blue lighten-4'},
      {text: 'Наименование темы ВКР', value: 'name_of_vkr', class: 'blue lighten-4'},
      {text: 'Статус', value: 'status', class: 'blue lighten-4',},
      {value: 'status.changed', class: 'blue lighten-4'}

    ],
    gekMode: false,
    managerVKRsEmpty: true

  }),
  methods: {
    async setManage() {
      this.vkrs = [];
      this.headers[4].align = '';
      this.headers[5].align = '';
      this.isGEK = false;
      this.vkrs = await utility.ajaxMoodleCall('local_cdo_vkr_get_vkrs', {mode_gek: false});
      this.createDataForFilter();
    },
    async setGEK() {
      this.vkrs = [];
      this.isGEK = true;
      this.headers[4].align = ' d-none';
      this.headers[5].align = ' d-none';
      this.vkrs = await utility.ajaxMoodleCall('local_cdo_vkr_get_vkrs', {mode_gek: true});
      this.createDataForFilter();
    },
    writeVKRStatusName(status_id) {
      return utility.writeVKRStatusName(status_id, this.$store.state.status);
    },
    clearAllInputs() {
      this.clearedControls.forEach(control => {
        this[control] = '';
      })
    },
    createDataForFilter() {
      const vm = this;

      for (let filter in this.filters) {
        let makeUniq = false;
        vm.filters[filter] = this.vkrs.map(el => {
          if (typeof el[filter] === 'object' && el[filter] !== null) {
            makeUniq = true;
          }
          return el[filter];
        });
        vm.filters[filter] = _.uniq(vm.filters[filter]);
        if (makeUniq) {
          vm.filters[filter] = _.uniqBy(vm.filters[filter], 'id');
        }
      }
      let currentYear = new Date().getFullYear().toString();
      if (this.years.includes(currentYear)) {
        this.filters.years.push(currentYear);
      }
      this.years = [currentYear];

    },
    async chooseVKR(item) {
      this.$store.commit('changeStatusApplication', !this.$store.state.statusApplication); //change interface
      this.$store.commit('changeItemVKR', item);
      if (!!item.status.changed)
        if (await this.changeVKRStatusManager(item.id)) {
          item.status.changed = false;
        }

    },
    async changeVKRStatusManager(id) {
      return await utility.ajaxMoodleCall(
          'local_cdo_vkr_change_manager_status_of_vkr',
          {
            id: id,
            status_changed: 0,
          }
      );
    },

  },
  computed: {
    searchedVKRsByFilter() {
      let resultVkrs = this.vkrs;
      const vm = this;

      for (let filter in this.filters) {
        if ((Array.isArray(vm[filter]) && vm[filter].length) || Number.isInteger(vm[filter])) {
          resultVkrs = resultVkrs.filter(vkr => {
            if (typeof vkr[filter] === 'object' && !Array.isArray(vkr[filter])) {
              return vkr[filter].id === vm[filter];
            }
            return vm[filter].includes(vkr[filter]);
          });
        }
      }
      return resultVkrs;
    }

  },
  async created() {
    this.strings = this.$store.state.strings;
    this.$store.state.loaderOn = true;
    this.gekMode = await utility.ajaxMoodleCall('local_cdo_vkr_check_is_gek');
    if (this.gekMode) {
      await this.setGEK();
      let count_vkrs = await utility.ajaxMoodleCall('local_cdo_vkr_get_vkrs', {mode_gek: false});
      this.managerVKRsEmpty = !!!(count_vkrs.length);
    } else {
      await this.setManage();
    }

    this.$store.state.loaderOn = false;
  }
}
</script>