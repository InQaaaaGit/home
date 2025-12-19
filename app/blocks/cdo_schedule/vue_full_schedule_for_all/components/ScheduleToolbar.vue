<template>
  <div class="schedule-toolbar">
    <div class="toolbar-container">

      <!-- Фильтры (отображаются по флагу showFilters) -->
      <div v-if="shouldShowFilters" class="filters-section">
        <!-- Выбор курса -->
        <div class="filter-group">
          <label class="filter-label">Курс:</label>
          <select 
            v-model="selectedCourse" 
            @change="onCourseChange"
            class="filter-select"
            :disabled="isLoading"
          >
            <option :value="null" disabled>Выберите курс</option>
            <option 
              v-for="course in courses" 
              :key="course.id" 
              :value="course.id"
            >
              {{ course.name }}
            </option>
          </select>
        </div>

        <!-- Выбор группы -->
        <div class="filter-group">
          <label class="filter-label">Группа:</label>
          <div class="searchable-dropdown">
            <input
              v-model="groupSearch"
              @input="filterGroups"
              @focus="showGroupDropdown = true"
              @blur="onGroupBlur"
              :placeholder="selectedCourse ? 'Выберите группу...' : 'Сначала выберите курс'"
              class="filter-input"
              :disabled="!selectedCourse || isLoading"
            />
            <div 
              v-if="showGroupDropdown" 
              class="dropdown-list"
            >
              <!-- Индикатор загрузки -->
              <div v-if="isLoading" class="dropdown-loading">
                <span class="loading-spinner"></span>
                Загрузка групп...
              </div>
              
              <!-- Сообщение если нет групп -->
              <div v-else-if="filteredGroups.length === 0" class="dropdown-empty">
                {{ groupSearch ? 'Группы не найдены' : 'Нет доступных групп' }}
              </div>
              
              <!-- Список групп -->
              <div
                v-else
                v-for="group in filteredGroups"
                :key="group.id"
                @click="selectGroup(group)"
                class="dropdown-item"
                :class="{ 'selected': selectedGroup?.id === group.id }"
              >
                {{ group.name }}
              </div>
            </div>
          </div>
        </div>

        <!-- Выбор даты/периода -->
        <div class="filter-group">
          <label class="filter-label">Период:</label>
          <div class="date-range-picker">
            <input
              v-model="startDate"
              type="date"
              @change="onDateChange"
              class="date-input"
              :disabled="isLoading"
            />
            <span class="date-separator">—</span>
            <input
              v-model="endDate"
              type="date"
              @change="onDateChange"
              class="date-input"
              :disabled="isLoading"
            />
          </div>
        </div>

        <!-- Быстрые фильтры дат -->
        <div class="filter-group">
          <div class="quick-filters">
            <button 
              @click="setDateRange('today')"
              class="quick-filter-btn"
              :class="{ 'active': isQuickFilterActive('today') }"
              :disabled="isLoading"
            >
              Сегодня
            </button>
            <button 
              @click="setDateRange('week')"
              class="quick-filter-btn"
              :class="{ 'active': isQuickFilterActive('week') }"
              :disabled="isLoading"
            >
              Неделя
            </button>
            <button 
              @click="setDateRange('month')"
              class="quick-filter-btn"
              :class="{ 'active': isQuickFilterActive('month') }"
              :disabled="isLoading"
            >
              Месяц
            </button>
          </div>
        </div>

        <!-- Кнопка показа расписания -->
        <div class="filter-group">
          <button 
            @click="updateSchedule"
            class="show-schedule-btn"
            :disabled="isLoading || !selectedCourse"
          >
            <span v-if="isLoading" class="loading-spinner"></span>
            Показать расписание
          </button>
        </div>

        <!-- Автообновление -->
        <div class="filter-group">
          <label class="auto-update-label">
            <input 
              v-model="autoUpdate" 
              type="checkbox" 
              class="auto-update-checkbox"
            />
            Автообновление
          </label>
        </div>
      </div>

      <!-- Активные фильтры -->
      <div v-if="shouldShowFilters && hasActiveFilters" class="active-filters">
        <div class="active-filters-header">
          <span>Активные фильтры:</span>
          <button @click="clearFilters" class="clear-filters-btn">Очистить</button>
        </div>
        <div class="active-filters-list">
          <span v-if="selectedCourse" class="filter-tag">
            Курс: {{ getCourseName(selectedCourse) }}
          </span>
          <span v-if="selectedGroup" class="filter-tag">
            Группа: {{ selectedGroup.name }}
          </span>
          <span v-if="startDate && endDate" class="filter-tag">
            Период: {{ formatDate(startDate) }} — {{ formatDate(endDate) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useParamsStore } from "../store/storeParams";

export default {
  name: 'ScheduleToolbar',
  props: {
    isLoading: {
      type: Boolean,
      default: false
    },
    showFilters: {
      type: Boolean,
      default: true
    },
    showRoleSelector: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      selectedCourse: null, // Изменено на null
      selectedGroup: null,
      groupSearch: '',
      showGroupDropdown: false,
      startDate: '',
      endDate: '',
      autoUpdate: true,
      currentQuickFilter: 'week',
      filteredGroups: [],
      internalShowFilters: true // Внутренний флаг для отображения фильтров
    }
  },
    computed: {
    paramsStore() {
      return useParamsStore();
    },
    
    courses() {
      return this.paramsStore.getCourses;
    },
    
    groups() {
      return this.paramsStore.getGroups;
    },
    
    // Управление отображением на основе props и внутреннего состояния
    shouldShowFilters() {
      return this.showFilters && this.internalShowFilters;
    },
    
    hasActiveFilters() {
      return this.selectedCourse || this.selectedGroup || this.startDate || this.endDate;
    },
    
    getFilterInfo() {
      const parts = [];
      
      if (this.selectedCourse) {
        const course = this.courses.find(c => c.id == this.selectedCourse);
        parts.push(`Курс: ${course?.name}`);
      }
      
      if (this.selectedGroup) {
        parts.push(`Группа: ${this.selectedGroup.name}`);
      }
      
      if (this.startDate && this.endDate) {
        parts.push(`Период: ${this.formatDate(this.startDate)} - ${this.formatDate(this.endDate)}`);
      }
      
      return parts.join(', ');
    }
  },
  methods: {
    async onCourseChange() {
      this.selectedGroup = null;
      this.groupSearch = '';
      
      // Загружаем группы для выбранного курса
      if (this.selectedCourse) {
        console.log('Загружаем группы для курса:', this.selectedCourse);
        await this.paramsStore.loadGroups(this.selectedCourse);
        console.log('Загруженные группы:', this.groups);
      }
      
      this.filterGroups();
      console.log('Отфильтрованные группы:', this.filteredGroups);
      
      if (this.autoUpdate) {
        this.updateSchedule();
      }
    },
    
    filterGroups() {
      if (!this.selectedCourse) {
        this.filteredGroups = [];
        return;
      }
      
      // Получаем все группы (они уже загружены для выбранного курса)
      let groups = this.groups;
      console.log('Все группы для фильтрации:', groups);
      
      // Фильтруем по поисковому запросу
      if (this.groupSearch) {
        groups = groups.filter(group => 
          group.name.toLowerCase().includes(this.groupSearch.toLowerCase())
        );
        console.log('Группы после фильтрации по поиску:', groups);
      }
      
      this.filteredGroups = groups;
      console.log('Итоговые отфильтрованные группы:', this.filteredGroups);
    },
    
    selectGroup(group) {
      this.selectedGroup = group;
      this.groupSearch = group.name;
      this.showGroupDropdown = false;
      
      if (this.autoUpdate) {
        this.updateSchedule();
      }
    },
    
    onGroupBlur() {
      // Увеличиваем задержку для лучшего UX
      setTimeout(() => {
        this.showGroupDropdown = false;
      }, 300);
    },
    
    onDateChange() {
      if (this.autoUpdate) {
        this.updateSchedule();
      }
    },
    
    setDateRange(type) {
      this.currentQuickFilter = type;
      const today = new Date();
      
      switch (type) {
        case 'today':
          this.startDate = this.formatDateForInput(today);
          this.endDate = this.formatDateForInput(today);
          break;
        case 'week':
          const weekStart = new Date(today);
          weekStart.setDate(today.getDate() - today.getDay() + 1);
          const weekEnd = new Date(weekStart);
          weekEnd.setDate(weekStart.getDate() + 6);
          this.startDate = this.formatDateForInput(weekStart);
          this.endDate = this.formatDateForInput(weekEnd);
          break;
        case 'month':
          const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
          const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
          this.startDate = this.formatDateForInput(monthStart);
          this.endDate = this.formatDateForInput(monthEnd);
          break;
      }
      
      if (this.autoUpdate) {
        this.updateSchedule();
      }
    },
    
    isQuickFilterActive(type) {
      return this.currentQuickFilter === type;
    },
    
    updateSchedule() {
      if (!this.selectedCourse) {
        // Можно добавить уведомление для пользователя, что нужно выбрать курс
        //alert('Пожалуйста, выберите курс.');
        return;
      }

      const filters = {
        courseId: this.selectedCourse,
        groupId: this.selectedGroup?.id,
        startDate: this.formatDateForAPI(this.startDate),
        endDate: this.formatDateForAPI(this.endDate),
        role: '' // Роль больше не используется
      };
      
      this.$emit('update-schedule', filters);
    },
    
    // Метод для форматирования даты в формат YYYYMMDD
    formatDateForAPI(dateString) {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      
      return `${year}${month}${day}`;
    },
    
    clearFilters() {
      this.selectedCourse = null;
      this.selectedGroup = null;
      this.groupSearch = '';
      this.startDate = '';
      this.endDate = '';
      this.currentQuickFilter = 'week';
      this.filteredGroups = [];
      
      this.updateSchedule();
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('ru-RU');
    },
    
    formatDateForInput(date) {
      return date.toISOString().split('T')[0];
    },

    // Метод для получения имени курса по ID
    getCourseName(courseId) {
      const course = this.courses.find(c => c.id == courseId);
      return course ? course.name : 'Неизвестный курс';
    }
  },
  
  async mounted() {
    // Загружаем курсы при монтировании
    await this.paramsStore.loadCourses();
    
    // Устанавливаем текущую неделю по умолчанию
    this.setDateRange('week');
    
    // Инициализируем фильтры групп
    this.filterGroups();
  }
}
</script>

<style scoped>
.schedule-toolbar {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 2px solid #dee2e6;
  padding: 16px 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toolbar-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  align-items: stretch;
}


.filters-section {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: flex-end; /* Прижимаем все элементы к низу */
  background-color: #fff;
  border: 2px solid #ced4da;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 140px;
  flex: 1; /* Равномерно распределяем пространство */
}

.filter-label {
  font-size: 14px;
  font-weight: 500;
  color: #495057;
  margin-bottom: 2px;
}

/* Специальные стили для групп с кнопками */
.filter-group:has(.quick-filters),
.filter-group:has(.show-schedule-btn),
.filter-group:has(.auto-update-label) {
  justify-content: flex-end; /* Прижимаем кнопки к низу */
  min-height: 60px; /* Минимальная высота для выравнивания */
}

/* Стили для кнопок, чтобы они занимали всю доступную высоту */
.quick-filters {
  display: flex;
  gap: 8px;
  align-items: flex-end; /* Прижимаем к низу */
}

.show-schedule-btn {
  padding: 10px 20px;
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 160px;
  justify-content: center;
  height: 42px; /* Фиксированная высота для выравнивания */
}

.auto-update-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #495057;
  cursor: pointer;
  height: 42px; /* Фиксированная высота для выравнивания */
  align-items: flex-end; /* Прижимаем к низу */
}

.filter-select,
.filter-input,
.date-input {
  padding: 8px 12px;
  border: 2px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
  background-color: #fff;
  transition: all 0.2s ease;
  height: 42px; /* Фиксированная высота для выравнивания */
  box-sizing: border-box;
}

.filter-select:focus,
.filter-input:focus,
.date-input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.filter-select:disabled,
.filter-input:disabled,
.date-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: #f8f9fa;
}

/* Searchable Dropdown */
.searchable-dropdown {
  position: relative;
}

.dropdown-list {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #fff;
  border: 2px solid #ced4da;
  border-top: none;
  border-radius: 0 0 6px 6px;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.2s ease;
  color: #495057; /* Явно задаем цвет текста */
}

.dropdown-item:hover {
  background-color: #e3f2fd; /* Светло-голубой фон при наведении */
  color: #1976d2; /* Темно-синий текст при наведении */
}

.dropdown-item.selected {
  background-color: #007bff;
  color: #fff;
}

.dropdown-item.selected:hover {
  background-color: #0056b3; /* Темнее при наведении на выбранный элемент */
  color: #fff;
}

.dropdown-loading {
  padding: 12px;
  text-align: center;
  color: #6c757d;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.dropdown-loading .loading-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #6c757d;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.dropdown-empty {
  padding: 12px;
  text-align: center;
  color: #6c757d;
  font-size: 14px;
  font-style: italic;
}

/* Date Range Picker */
.date-range-picker {
  display: flex;
  align-items: center;
  gap: 8px;
}

.date-input {
  padding: 8px 12px;
  border: 2px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
  background-color: #fff;
  transition: all 0.2s ease;
  min-width: 130px;
}

.date-input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.date-separator {
  font-weight: 600;
  color: #6c757d;
}

/* Quick Filters */
.quick-filters {
  display: flex;
  gap: 8px;
}

.quick-filter-btn {
  padding: 8px 12px;
  border: 2px solid #ced4da;
  border-radius: 6px;
  background-color: #fff;
  color: #495057;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  height: 42px; /* Фиксированная высота для выравнивания */
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 60px;
}

.quick-filter-btn:hover {
  border-color: #007bff;
  color: #007bff;
}

.quick-filter-btn.active {
  background-color: #007bff;
  border-color: #007bff;
  color: #fff;
}

.quick-filter-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Show Schedule Button */
.show-schedule-btn {
  padding: 10px 20px;
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 160px;
  justify-content: center;
  height: 42px; /* Фиксированная высота для выравнивания */
}

.show-schedule-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.show-schedule-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Loading Spinner */
.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #fff;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Auto Update Checkbox */
.auto-update-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #495057;
  cursor: pointer;
  height: 42px; /* Фиксированная высота для выравнивания */
  align-items: flex-end; /* Прижимаем к низу */
}

.auto-update-checkbox {
  width: 16px;
  height: 16px;
  accent-color: #007bff;
}

/* Active Filters */
.active-filters {
  margin-top: 16px;
  padding: 12px 16px;
  background-color: #e3f2fd;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.active-filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.active-filters-header span {
  font-size: 14px;
  font-weight: 500;
  color: #1976d2;
}

.active-filters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
  background-color: #e0f2f7;
  color: #007bff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 4px;
}

.filter-tag .clear-filters-btn {
  padding: 2px 6px;
  background-color: #dc3545;
  border: none;
  border-radius: 3px;
  color: #fff;
  font-size: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-tag .clear-filters-btn:hover {
  background-color: #c82333;
  transform: translateY(-1px);
}

.clear-filters-btn {
  padding: 6px 12px;
  background-color: #dc3545;
  border: none;
  border-radius: 4px;
  color: #fff;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-filters-btn:hover {
  background-color: #c82333;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .toolbar-container {
    gap: 16px;
  }
  
  .filter-group {
    min-width: 140px;
  }
}

@media (max-width: 768px) {
  .schedule-toolbar {
    padding: 12px 16px;
  }
  
  .toolbar-container {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  
  .filters-section {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  
  .filter-group {
    min-width: auto;
    flex: none;
  }
  
  /* На мобильных устройствах убираем фиксированную высоту */
  .filter-select,
  .filter-input,
  .date-input,
  .quick-filter-btn,
  .show-schedule-btn {
    height: auto;
    min-height: 42px;
  }
  
  .quick-filters {
    justify-content: center;
  }


  .active-filters {
    flex-direction: column;
    gap: 8px;
    text-align: center;
  }
}

@media (max-width: 576px) {
  .date-range-picker {
    flex-direction: column;
    gap: 4px;
  }
  
  .date-input {
    min-width: auto;
  }
  
  .show-schedule-btn {
    min-width: auto;
  }
}
</style>
