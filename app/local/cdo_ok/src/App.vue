<template>
  <div id="app">
    <main-quality-assessment></main-quality-assessment>
  </div>
</template>

<script>
import Main from "@/Components/MainQualityAssessment.vue";
import MainQualityAssessment from "@/Components/MainQualityAssessment.vue";

export default {
  name: "App",
  components: {
    MainQualityAssessment,
    Main
  },
  mounted() {

  }
}

</script>

