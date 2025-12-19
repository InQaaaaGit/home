<script>
import {mapGetters, mapState} from "vuex";

export default {
  name: "not-found-open-sheet",
  computed: {
    ...mapState(['strings', 'sheets']),
    ...mapGetters(['haveSheets'])
  }
};
</script>

<template>
  <div class="row" v-show="!haveSheets">
    <div class="col-12">
      <div class="alert alert-primary">{{ strings.list_sheet_not_found_open_sheet }}</div>
    </div>
  </div>
</template>

<style scoped>

</style>