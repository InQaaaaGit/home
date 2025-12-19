<template>
  <div class="schedule-container">
    <div class="toolbar">
      <div v-if="teachers.length > 0" class="filter-container">
        <label for="teacher-select" class="filter-label">Преподаватель:</label>
        <select id="teacher-select" v-model="selectedTeacher" @change="onTeacherSelect" class="filter-select">
          <option :value="null">Все преподаватели</option>
          <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
            {{ teacher.name }}
          </option>
        </select>
      </div>
    </div>
    <SchedulerComponent
        class="scheduler-component"
        :events="filteredEvents"
        :key="schedulerKey"
    />
  </div>
</template>

<script>
import SchedulerComponent from "./SchedulerComponent.vue";
import { useParamsStore } from "../store/storeParams";
import { onMounted, ref, computed } from 'vue';

export default {
  name: "Main",
  components: { SchedulerComponent },
  setup() {
    const paramsStore = useParamsStore();
    const schedulerKey = ref(0);
    const isLoading = ref(false);
    const selectedTeacher = ref(null);

    const teachers = computed(() => paramsStore.getTeachers);
    const filteredEvents = computed(() => paramsStore.getFilteredEvents);

    const getScheduleData = async (type = '') => {
      if (isLoading.value) return;
      isLoading.value = true;
      try {
        await paramsStore.getScheduleData(type);
        selectedTeacher.value = null;
        schedulerKey.value++;
      } catch (error) {
        console.error('Ошибка при получении данных расписания:', error);
      } finally {
        isLoading.value = false;
      }
    };

    const onTeacherSelect = () => {
      paramsStore.selectTeacher(selectedTeacher.value);
    };
    
    onMounted(async () => {
      await getScheduleData();
    });

    return {
      paramsStore,
      schedulerKey,
      isLoading,
      teachers,
      selectedTeacher,
      filteredEvents,
      getScheduleData,
      onTeacherSelect,
    };
  }
};
</script>

<style scoped>
.schedule-container {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.toolbar {
  display: flex;
  gap: 20px;
  align-items: center;
  padding: 10px;
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}


.filter-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-label {
  font-weight: 500;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  min-width: 200px;
}

.scheduler-component {
  flex: 1;
  overflow: hidden;
  width: 100%;
}
</style>
