<template>
  <div class="d-inline-flex">
    <b-form-select :options="this.$store.state.years"
                   :value="this.$store.state.years[0]"
                   class="mr-2"
                   v-model="currentYear"
    >

    </b-form-select>
    <b-form-select :options="this.$store.state.reports" class="mr-2" v-model="currentReport"></b-form-select>
    <b-btn :href="`print.php?report=${currentReport}`" variant="primary">{{ this.$store.state.langStrings['buttons:construct'] }}</b-btn>
  </div>
</template>

<script>
export default {
  name: "FooterActionsQualityAssessment",
  data: () => ({
    currentReport: 'local_cdo_ok\\reports\\variants\\report1',
    currentYear: 2023
  })
}
</script>

<style scoped>

</style>