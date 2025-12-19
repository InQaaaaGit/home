<template>
  <div>
    <!-- Вкладки -->
    <ul class="nav nav-tabs" role="tablist">
      <li
          class="nav-item"
          v-for="(subsection, index) in subsections"
          :key="'tab-' + index"
      >
        <button
            class="nav-link"
            :class="{ active: index === activeTab }"
            :id="'tab-' + index"
            data-bs-toggle="tab"
            role="tab"
            :aria-controls="'tabpanel-' + index"
            :aria-selected="index === activeTab"
            @click="setActiveTab(index)"
        >
          {{ subsection.title }}
        </button>
      </li>
    </ul>

    <!-- Контент вкладок -->
    <div class="tab-content mt-3">
      <div
          v-for="(subsection, index) in subsections"
          :key="'content-' + index"
          class="tab-pane fade"
          :class="{ show: index === activeTab, active: index === activeTab }"
          :id="'tabpanel-' + index"
          role="tabpanel"
          :aria-labelledby="'tab-' + index"
      >
        <div>
          <!-- Динамическая отрисовка компонента -->
          <component
              :is="components[subsection.component]"
              v-if="subsection.component"
              :data="subsection.data"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Импортируем компоненты для вкладок
import BuildingTable      from "./BuildingTable.vue";
import RoomTable          from './RoomTable.vue';
import EduProgramTable    from './EduProgramTable.vue';
import DisciplinesTable   from './DisciplinesTable.vue';

// Массив подразделов с их заголовками, данными и компонентами
const subsections = ref([
  { title: "Образовательные программы", data: [], loading: true, component: "EduProgramTable" },
  { title: "Дисциплины",                data: [], loading: true, component: "DisciplinesTable" },
  { title: "Корпуса",                   data: [], loading: true, component: "BuildingTable" },
  { title: "Аудиторный фонд",           data: [], loading: true, component: "RoomTable" },
]);

const components = {
  BuildingTable,
  RoomTable,
  DisciplinesTable,
  EduProgramTable,
};

// Активный таб (по умолчанию первый)
const activeTab = ref(0);
const setActiveTab = (index) => {
  activeTab.value = index;
};
</script>
