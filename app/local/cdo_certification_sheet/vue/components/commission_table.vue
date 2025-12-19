<script>
import {mapState} from "vuex";
import {store} from "../store";

export default {
  props: ['sheet'],
  name: "commission_table",
  computed: {
    ...mapState(['strings', 'sheets', 'userID']),
    isAgreedAllow() {
      return store.getters.isAgreedAllowInSheet(this.sheet.guid);
    },
    currentTeacher() {
      if (this.isAgreedAllow) {
        return !!sheet.teachers.find(teacher => {
          return parseInt(teacher.user_id) === parseInt(this.userID);
        });
      }
      return false;
    }
  },

};
</script>

<template>
  <div v-if="isAgreedAllow">
    <div class="row">
      <div class="col-12">
        <h3>Состав комиссии</h3>
        <table class="table">
          <thead>
          <tr>
            <th scope="col">{{ strings.commission_sheet_user_full_name }}</th>
            <th scope="col">{{ strings.commission_sheet_activity }}</th>
            <th scope="col">{{ strings.commission_sheet_chairman }}</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="teacher in sheet.teachers" :key="teacher.guid"
              :class="parseInt(userID)===parseInt(teacher.user_id) ? 'table-success' : ''">
            <th scope="row">{{ teacher.full_name }}</th>
            <td>
              {{
                teacher.agreed ? strings.commission_sheet_agreed_message_yes : strings.commission_sheet_agreed_message_no
              }}
            </td>
            <td><i v-if="teacher.chairman" class="fas fa-check"></i></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div v-else></div>
</template>

<style scoped>

</style>