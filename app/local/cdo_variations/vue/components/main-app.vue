<script>
import {mapState} from "vuex";
import {store} from "../store";

export default {
  name: "MainApp",
  data: () => ({
    content: null,
    chosenCourse: '',
    chosenModuleForUpdate: [],
    modules: {}
  }),
  computed: {
    ...mapState(['strings', 'userID', 'variations', 'courseList', 'isAppLoading', 'excludedMods']),
  },
  async created() {
  },
  methods: {
    async loadCourseContent() {
      this.content = await store.dispatch('loadCourseContent', this.chosenCourse);
      this.content.forEach(content => {
        content.modules = content.modules.filter(module => {
          return !this.excludedMods.includes(module.modname);
        });
        content.modules.forEach(module => {
          module.parsedAvailability = JSON.parse(module.availability);
          if (module.parsedAvailability !== null) {
            module.parsedAvailability.c.forEach(item => {
              this.variations.forEach(variation => {
                if (variation === item.v) {
                  item.chosen = true;
                  let struct = {
                    cmid: module.id,
                    condition: item.v
                  };
                  this.chosenModuleForUpdate.push(struct);
                }
              });
            });
          }
        });
      });
    },
    async changeAvailability(moduleID, variant) {
      let struct = {
        cmid: moduleID,
        condition: variant,
      };

      let alreadyInArray = this.chosenModuleForUpdate.some(element => {
        return (element.cmid === struct.cmid && element.condition === struct.condition);
      });

      if (!alreadyInArray) {
        this.chosenModuleForUpdate.push(struct);
      } else {
        this.chosenModuleForUpdate = this.chosenModuleForUpdate.filter(element => {
          return !(element.cmid === struct.cmid && element.condition === struct.condition);
        });
      }

      await store.dispatch(
          'updateModuleInfo',
          {modules: this.chosenModuleForUpdate, courseid: this.chosenCourse}
      );
    },
    getLinkToMod(mod, id) {
      return '/mod/' + mod + '/view.php?id=' + id;
    },
    t(availability, variation) {
      let result = false;
      result = availability.some(item => {
        return variation === item.v;
      });
      return result;
    },
    changeAllModuleVariations(moduleID, e) {
      this.variations.forEach(async variant => {
        if (document.getElementById('tubler' + moduleID + variant).checked
            !== document.getElementById('alltubler' + moduleID).checked) {
          await this.changeAvailability(moduleID, variant);
        }
        document.getElementById('tubler' + moduleID + variant).checked =
            document.getElementById('alltubler' + moduleID).checked;
      });
    }
  }
};
</script>

<template>
  <div class="container overflow-auto" id="local_cdo_variations">
    <div v-if="isAppLoading" class="spinner-border text-dark" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="row">
      <div class="col-12">
        <label for="choseCourse">{{ strings.chose_course }}</label>
        <select id="choseCourse" v-on:change="loadCourseContent" v-model="chosenCourse" class="form-control">
          <option :value="course.id" v-for="course in courseList.courses">
            {{ course.fullname }} ({{ course.categoryname }})
          </option>
        </select>
      </div>
    </div>
    <table class="table">
      <tbody>
      <th class="z-index-upper">{{ strings.variation }}</th>
      <template v-for="element in content">
        <th v-for="module in element.modules" class="z-index-upper">
          <div class="custom-control custom-switch">
            <input
                :id="'alltubler'+module.id"
                type="checkbox"
                class="custom-control-input"
                @change="(event) => changeAllModuleVariations(module.id, event)"
            >
            <label class="custom-control-label z-index-lower" :for="'alltubler'+module.id"></label>
          </div>
          <a :href="getLinkToMod(module.modname, module.id)">{{ module.name }}</a>
        </th>
      </template>
      </tbody>
      <tr v-for="variation in variations">
        <td class="z-index-upper">{{ variation }}</td>
        <template v-for="element in content">
          <td v-for="module in element.modules">
            <div class="custom-control custom-switch">
              <template v-if="module.parsedAvailability !== null">
                <!--                <template v-for="el in module.parsedAvailability.c">-->
                <input @change="changeAvailability(module.id, variation)"
                       type="checkbox"
                       :checked="t(module.parsedAvailability.c, variation)"
                       class="custom-control-input"
                       :id="'tubler'+module.id+variation"
                >
                <label class="custom-control-label" :for="'tubler'+module.id+variation"></label>
                <!--                </template>-->
              </template>
              <template v-else>
                <input @change="changeAvailability(module.id, variation)"
                       type="checkbox"
                       class="custom-control-input red"
                       :id="'tubler'+module.id+variation">
                <label class="custom-control-label z-index-lower" :for="'tubler'+module.id+variation"></label>
              </template>
            </div>
          </td>
        </template>
      </tr>
    </table>
  </div>
</template>

<style scoped>
.z-index-upper {
  z-index: 3;
}

.z-index-lower {
  z-index: 1;
}

.table {
  width: 100%;
}

.table th:first-child,
.table td:first-child {
  position: sticky;
  left: -15px;
  background-color: #59595b;
  color: #ffffff;
}

.table td {
  white-space: nowrap;
}

tr:nth-child(odd) {
  background: white;
}

tr:nth-child(even) {
  background: white;
}
</style>