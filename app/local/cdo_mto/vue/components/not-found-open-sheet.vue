<script setup>
import {ref} from 'vue';
const count = ref(0);
function increment() {
  count.value++;
}
</script>

<template>
  <div class="row">
    <div class="col-12">
      <div class="alert alert-primary">32452423423423</div>
      <button @click="increment">Счётчик: {{ count }}</button>
    </div>
  </div>
</template>

<style scoped>

</style>