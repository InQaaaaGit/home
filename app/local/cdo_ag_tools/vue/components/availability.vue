<script setup>

import {useMainStore} from "../store/store";
import {computed, ref} from "vue";
import {useLangStore} from "../store/storeLang";
import notification from "core/notification";

const mainStore = useMainStore();
const langStore = useLangStore();
const search = ref();
const chosenUser = ref(0);
const course = ref();
const quarters = ref([
  'st_quarter',
  'nd_quarter',
  'rd_quarter',
  'th_quarter'
]);


async function notify() {
  await notification.addNotification(
      [{
        type: 'warning',
        message: 1
      }]
  );
}

const isUserChosen = computed(() => {
  return chosenUser.value !== 0;
});
const searchByFirstname = function s() {
  mainStore.loadUsers(search.value);
};


</script>

<template>
  <div class="container-fluid">
    <div class="row d-flex align-items-center">
      <div class="col-4">
        <form class="form-inline">
          <div class="form-group mx-sm-3 mb-2">
            <label class="mr-2" for="search">{{ langStore.strings.enter_surname }}</label>
            <input id="search" class="form-control" v-model="search"/>
          </div>
          <button class="btn btn-primary mb-2" @click="searchByFirstname">Поиск</button>
        </form>
      </div>
      <div class="col-4" v-show="search">
        <h4>{{ langStore.strings.chose_user }}</h4>
        <div class="list-group">
          <a v-for="user in mainStore.users.users"
             href="#"
             @click="chosenUser = user"
             class="list-group-item list-group-item-action "
             :aria-current="chosenUser === user"
             :class="chosenUser === user ? 'active' : ''"
          >
            {{ user.fullname }}
          </a>
        </div>
      </div>
      <div class="col-4">
        {{ mainStore.log }}
      </div>

    </div>
    <div class="row mt-3" v-if="isUserChosen">
      <div class="col-12">
        <!--        <v-autocomplete
                    :label="langStore.strings.choose_course"
                    v-model="course"
                    item-title="fullname"
                    item-value="id"
                    :items="mainStore.userCourses"
                >
                </v-autocomplete>-->
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <template v-if="isUserChosen">

          <table class="table">
            <thead>
            <tr>
              <th scope="col">
                {{ langStore.strings.quarter }}
              </th>
              <th scope="col">
                <div class="row">
                  <div class="col-2">
                    {{ langStore.strings.actions }}
                  </div>
                  <div class="col-3">
                    <button v-if="isUserChosen" class="btn btn-primary"
                            @click="notify(); mainStore.setAvailability(chosenUser.id)">
                      {{ langStore.strings.open_all_quarter }}
                    </button>
                  </div>
                  <div class="col-3">
                    <button v-if="isUserChosen" class="btn btn-secondary"
                            @click="mainStore.setAvailability(chosenUser.id, '', '', false)">
                      {{ langStore.strings.close_all_quarter }}
                    </button>
                  </div>
                </div>

              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(quarter, index) in quarters" :key="index">
              <td>{{ langStore.strings[quarter] }}</td>
              <td>
                <div class="d-flex ">
                  <div class="m-2">
                    <button @click="mainStore.setAvailability(chosenUser.id,
                      langStore.strings[quarters[index]],
                      langStore.strings[quarters[index+1]])"
                            class="btn btn-primary ">{{ langStore.strings.open }}
                    </button>
                  </div>

                  <div class="m-2">
                    <button @click="mainStore.setAvailability(chosenUser.id,
                      langStore.strings[quarters[index]],
                      langStore.strings[quarters[index+1] ?? ''], false)"
                            class="btn btn-secondary mr-2">{{ langStore.strings.close }}
                    </button>
                  </div>

                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </template>
      </div>
    </div>

  </div>
</template>

<style scoped>

</style>
