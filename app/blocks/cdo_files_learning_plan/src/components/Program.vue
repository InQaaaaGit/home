<template>
  <b-container fluid>

    <b-card v-if="getLoadingProgram">
      <b-skeleton animation="wave" width="85%"></b-skeleton>
      <b-skeleton animation="wave" width="55%"></b-skeleton>
      <b-skeleton animation="wave" width="70%"></b-skeleton>
    </b-card>

    <b-tabs v-if="!getLoadingProgram && getProgramData.isLoad && getSelectedProgram" pills card>

      <b-tab title="Файлы образовательной программы" active>
        <ProgramFiles/>
      </b-tab>

      <b-tab title="Файлы дисциплин">
        <ProgramDiscipline/>
      </b-tab>

      <b-tab title="Скачать файлы">
        <ProgramDownload/>
      </b-tab>

    </b-tabs>

  </b-container>
</template>

<script>
import ProgramFiles from "./ProgramFiles.vue";
import ProgramDiscipline from "./ProgramDiscipline.vue";
import ProgramDownload from "./ProgramDownload.vue";
import {mapGetters} from "vuex";

export default {
  name: "Program",
  components: {ProgramFiles, ProgramDiscipline, ProgramDownload},
  data() {
    return {
    }
  },
  computed: {
    ...mapGetters('PROGRAM', [
      'getLoadingProgram',
      'getProgramData',
      'getSelectedProgram',
    ])
  },
}
</script>

<style scoped>

</style>