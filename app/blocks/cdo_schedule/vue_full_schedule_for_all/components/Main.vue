<template>
  <div class="schedule-main-container" :style="containerStyle">
    <!-- Панель инструментов для фильтрации -->
    <ScheduleToolbar 
      :isLoading="isLoading"
      @update-schedule="handleScheduleUpdate"
      :show-role-selector="false"
    />
    
    <div v-if="!isScheduleShown" class="placeholder-container">
      <div class="placeholder-content">
        <p class="placeholder-text">Выберите параметры в фильтре и нажмите "Показать расписание", чтобы загрузить данные.</p>
      </div>
    </div>
    
    <SchedulerComponent 
      v-if="isScheduleShown"
      class="scheduler-main" 
      :events="scheduleEvents"
      :key="schedulerKey"
    ></SchedulerComponent>
  </div>
</template>

<script>

import {scheduler} from "dhtmlx-scheduler";
import SchedulerComponent from "./SchedulerComponent.vue";
import ScheduleToolbar from "./ScheduleToolbar.vue";
import {useParamsStore} from "../store/storeParams";

export default {
  name: "Main",
  components: {SchedulerComponent, ScheduleToolbar},
  data() {
    return {
      schedulerKey: 0,
      isLoading: false,
      isScheduleShown: false, // Новое состояние для контроля отображения
      menuHeight: 60, // Базовая высота меню Moodle
      windowWidth: window.innerWidth,
      currentFilters: {
        courseId: '',
        groupId: null,
        startDate: '',
        endDate: ''
      }
    }
  },
  computed: {
    paramsStore() {
      return useParamsStore();
    },
    scheduleEvents() {
      const events = this.paramsStore.getEvents;
      return events;
    },
    containerStyle() {
      return {
        top: `${this.menuHeight}px`,
        height: `calc(100vh - ${this.menuHeight}px)`
      };
    }
  },
  methods: {
    async getScheduleData(type = '') {
      if (this.isLoading) {
        return; // Предотвращаем множественные запросы
      }
      
      this.isLoading = true;
      try {
        await this.paramsStore.getScheduleData(type);
        // Обновляем ключ для принудительного пересоздания компонента при необходимости
        this.schedulerKey++;
      } catch (error) {
        console.error('Ошибка при получении данных расписания:', error);
      } finally {
        this.isLoading = false;
      }
    },
    
    async handleScheduleUpdate(filters) {
      this.isScheduleShown = true; // Показываем виджет при первом запросе
      this.currentFilters = filters;
      this.isLoading = true;
      
      try {
        // Здесь будет логика получения данных с учетом фильтров
        await this.paramsStore.getScheduleDataWithFilters(filters);
        
        this.schedulerKey++;
      } catch (error) {
        console.error('Ошибка при обновлении расписания с фильтрами:', error);
      } finally {
        this.isLoading = false;
      }
    },
    
    
    calculateMenuHeight() {
      // Определяем высоту меню Moodle
      const navbar = document.querySelector('.navbar');
      const header = document.querySelector('header');
      const nav = document.querySelector('nav');
      
      let height = 60; // Базовая высота
      
      if (navbar) {
        height = Math.max(height, navbar.offsetHeight);
      }
      
      if (header) {
        height = Math.max(height, header.offsetHeight);
      }
      
      if (nav) {
        height = Math.max(height, nav.offsetHeight);
      }
      
      // Проверяем дополнительные элементы меню
      const menuElements = document.querySelectorAll('.navbar-nav, .nav, .breadcrumb, .page-header');
      menuElements.forEach(element => {
        if (element.offsetTop < 100) { // Элементы в верхней части страницы
          height = Math.max(height, element.offsetTop + element.offsetHeight);
        }
      });
      
      // Добавляем небольшой отступ для безопасности
      height += 10;
      
      this.menuHeight = height;
    },
    
    handleResize() {
      this.windowWidth = window.innerWidth;
      this.calculateMenuHeight();
    }
  },
  
  mounted() {
    // Вычисляем высоту меню при монтировании
    this.$nextTick(() => {
      this.calculateMenuHeight();
    });
    
    // Слушаем изменения размера окна
    window.addEventListener('resize', this.handleResize);
    
    // Слушаем изменения в DOM (для динамических меню)
    const observer = new MutationObserver(() => {
      this.calculateMenuHeight();
    });
    
    observer.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ['class', 'style']
    });
    
    // Сохраняем observer для очистки
    this.observer = observer;
  },
  
  beforeUnmount() {
    // Очищаем слушатели
    window.removeEventListener('resize', this.handleResize);
    if (this.observer) {
      this.observer.disconnect();
    }
  },
  
  async created() {
    // Начальная загрузка удалена
  }
};
</script>

<style scoped>
.schedule-main-container {
  width: 100vw !important;
  max-width: none !important;
  min-width: 1200px !important;
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border-radius: 0 !important;
  box-shadow: none !important;
  overflow: hidden;
  position: fixed !important;
  left: 0 !important;
  z-index: 1000 !important;
}


.scheduler-main {
  flex: 1;
  overflow: hidden;
  width: 100%;
}

/* Адаптивность для больших экранов */
@media (min-width: 1400px) {
  .schedule-main-container {
    min-width: 1400px !important;
  }
}

@media (min-width: 1920px) {
  .schedule-main-container {
    min-width: 1600px !important;
  }
}

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
  .schedule-main-container {
    min-width: 100vw !important;
    position: relative !important;
  }
}

/* Стили для предотвращения конфликтов с Moodle */
#page-content .schedule-main-container {
  margin: 0 !important;
  padding: 0 !important;
  max-width: none !important;
  width: 100vw !important;
  position: fixed !important;
  left: 0 !important;
  z-index: 1000 !important;
}

/* Стили для полноэкранного режима */
.schedule-main-container.fullscreen {
  width: 100vw !important;
  height: 100vh !important;
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  z-index: 9999 !important;
  background-color: #fff !important;
}

.placeholder-container {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f8f9fa;
  width: 100%;
}

.placeholder-content {
  text-align: center;
  padding: 40px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  max-width: 500px;
}

.placeholder-text {
  font-size: 18px;
  color: #6c757d;
  font-weight: 500;
}
</style>
