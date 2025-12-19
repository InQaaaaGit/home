<template>
  <div class="table-wrapper">
    <table class="table">
      <thead class="table-head">
        <tr>
          <th
            v-for="field in fields"
            :key="field.key"
            :style="field.thStyle"
            class="table-header"
          >
            {{ field.label }}
          </th>
        </tr>
      </thead>
      <tbody class="table-body">
        <slot></slot>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  fields: {
    type: Array,
    required: true,
    default: () => []
  },
  striped: {
    type: Boolean,
    default: false
  }
});
</script>

<style scoped>
.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
  border-collapse: collapse;
}

.table-head {
  border-bottom: 2px solid #dee2e6;
}

.table-header {
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  color: #495057;
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
}

.table-body {
  background-color: #fff;
}

:deep(.table-row) {
  border-bottom: 1px solid #dee2e6;
  transition: background-color 0.15s ease-in-out;
}

:deep(.table-row:hover) {
  background-color: #f8f9fa;
}

:deep(.table-cell) {
  padding: 0.75rem;
  vertical-align: middle;
}

:deep(.table-row.striped:nth-child(even)) {
  background-color: #f8f9fa;
}
</style>









