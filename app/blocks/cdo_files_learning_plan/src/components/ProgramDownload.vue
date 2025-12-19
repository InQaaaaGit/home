<template>
  <div>
    <b-link :href="downloadLink" target="_blank">
      <b-icon icon="cloud-download-fill" animation="cylon-vertical" variant="info" font-scale="2"></b-icon>
      Скачать файлы ОП одним архивом
    </b-link>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "ProgramDownload",
  computed: {
    ...mapGetters('PROGRAM', [
        'getSelectedProgram'
    ]),
    downloadLink() {
      return '/blocks/cdo_files_learning_plan/get_file.php?mode=all_files&doc_number=' + this.getSelectedProgram
    }
  }
}
</script>

<style scoped>

</style>