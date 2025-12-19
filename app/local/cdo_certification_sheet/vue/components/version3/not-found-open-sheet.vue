<script setup>
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';

const certificationStore = useCertificationStore();
const { strings, haveSheets } = storeToRefs(certificationStore);
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
