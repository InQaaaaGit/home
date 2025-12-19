<template>
  <div class="scheduler-wrapper">
    <div ref="SchedulerComponent" class="scheduler-container"></div>
  </div>
</template>

<script>
import * as dhtmlxScheduler from "dhtmlx-scheduler";
import { markRaw } from 'vue';
import "dhtmlx-scheduler/codebase/dhtmlxscheduler.css";

export default {
  props: {
    events: {
      type: Array,
      default() {
        return []
      },
    },
  },

  data() {
    return {
      isSchedulerInitialized: false,
      schedulerContainer: null,
      lastProcessedEvents: null,
      isUpdating: false,
      updateTimeout: null,
      isVueUpdating: false,
      // Используем markRaw для предотвращения реактивности
      schedulerInstance: markRaw(dhtmlxScheduler.scheduler)
    }
  },

  methods: {
    $_initDataProcessor: function() {
      if (!this.schedulerInstance.$_dataProcessorInitialized) {
        this.schedulerInstance.createDataProcessor((entity, action, data, id) => {
          // Предотвращаем реактивные обновления при обработке данных
          this.isVueUpdating = true;
          this.$emit(`${entity}-updated`, id, action, data);
          this.$nextTick(() => {
            this.isVueUpdating = false;
          });
        });
        this.schedulerInstance.$_dataProcessorInitialized = true;
      }
    },

    updateScheduler() {
      try {
        console.log('updateScheduler вызван');
        
        if (this.isUpdating || this.isVueUpdating) {
          console.log('updateScheduler: пропускаем из-за активного обновления');
          return; // Предотвращаем рекурсивные обновления
        }
        
        if (this.$refs.SchedulerComponent && this.isSchedulerInitialized && this.events) {
          console.log('updateScheduler: обновляем планировщик');
          
          // Проверяем, действительно ли данные изменились
          const eventsString = JSON.stringify(this.events);
          if (this.lastProcessedEvents === eventsString) {
            console.log('updateScheduler: данные не изменились, пропускаем');
            return; // Данные не изменились, пропускаем обновление
          }
          
          this.isUpdating = true;
          
          // Временно отключаем реактивность
          this.isVueUpdating = true;
          
          console.log('updateScheduler: очищаем планировщик');
          this.schedulerInstance.clearAll();
          
          console.log('updateScheduler: парсим события:', this.events);
          this.schedulerInstance.parse(this.events);
          
          this.lastProcessedEvents = eventsString;
          
          // Проверяем, что события добавились
          const allEvents = this.schedulerInstance.getEvents();
          console.log('updateScheduler: события в планировщике после парсинга:', allEvents);
          
          // Восстанавливаем реактивность
          this.$nextTick(() => {
            this.isUpdating = false;
            this.isVueUpdating = false;
          });
        } else {
          console.log('updateScheduler: условия не выполнены', {
            hasRef: !!this.$refs.SchedulerComponent,
            isInitialized: this.isSchedulerInitialized,
            hasEvents: !!this.events
          });
        }
      } catch (error) {
        console.warn('Ошибка при обновлении планировщика:', error);
        this.isUpdating = false;
        this.isVueUpdating = false;
      }
    },

    debouncedUpdateScheduler() {
      // Очищаем предыдущий таймаут
      if (this.updateTimeout) {
        clearTimeout(this.updateTimeout);
      }
      
      // Устанавливаем новый таймаут
      this.updateTimeout = setTimeout(() => {
        this.updateScheduler();
      }, 200); // Увеличиваем задержку до 200мс
    },



    showCustomModal(id) {
      console.log('=== showCustomModal НАЧАЛО ===');
      try {
        console.log('showCustomModal вызван с ID:', id);
        
        const event = this.schedulerInstance.getEvent(id);
        console.log('Событие для модального окна:', event);
        
        if (!event) {
          console.log('Событие не найдено для модального окна');
          return;
        }
        
        console.log('Создаем модальное окно для события:', event.text);
        
        // Удаляем существующий модальный диалог
        const existingModal = document.querySelector('.custom-event-modal');
        if (existingModal) {
          existingModal.remove();
        }
        
        // Создаем overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-event-modal-overlay';
        
        // Создаем модальное окно
        const modal = document.createElement('div');
        modal.className = 'custom-event-modal';
        modal.innerHTML = this.createEventDetailsModal(event);
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Обработчик закрытия по клику на overlay
        overlay.addEventListener('click', (e) => {
          if (e.target === overlay) {
            overlay.remove();
          }
        });
        
        // Обработчик закрытия по Escape
        const handleEscape = (e) => {
          if (e.key === 'Escape') {
            overlay.remove();
            document.removeEventListener('keydown', handleEscape);
          }
        };
        document.addEventListener('keydown', handleEscape);
        
        console.log('Кастомный модальный диалог создан');
        console.log('=== showCustomModal КОНЕЦ ===');
        
      } catch (error) {
        console.error('Ошибка при создании кастомного модального диалога:', error);
        console.log('=== showCustomModal ОШИБКА ===');
      }
    },

    customizeLightbox(id) {
      try {
        console.log('customizeLightbox вызван с ID:', id);
        
        const event = this.schedulerInstance.getEvent(id);
        console.log('Событие для кастомизации:', event);
        
        if (!event) {
          console.log('Событие не найдено для кастомизации');
          return;
        }
        
        // Ждем, пока lightbox отрендерится
        this.$nextTick(() => {
          const lightbox = document.querySelector('.dhx_cal_light');
          if (lightbox) {
            console.log('Lightbox найден, кастомизируем содержимое');
            console.log('Структура lightbox:', lightbox.innerHTML);
            
            // Создаем кастомное содержимое
            const customContent = this.createEventDetailsModal(event);
            
            // Пытаемся найти контейнер содержимого lightbox разными способами
            let lightboxContent = lightbox.querySelector('.dhx_cal_light_content');
            if (!lightboxContent) {
              lightboxContent = lightbox.querySelector('.dhx_cal_light_wide');
            }
            if (!lightboxContent) {
              lightboxContent = lightbox.querySelector('.dhx_cal_light_rtl');
            }
            if (!lightboxContent) {
              // Если не нашли специальный контейнер, используем весь lightbox
              lightboxContent = lightbox;
            }
            
            if (lightboxContent) {
              lightboxContent.innerHTML = customContent;
              console.log('Содержимое lightbox кастомизировано');
            } else {
              console.log('Контейнер содержимого lightbox не найден');
            }
          } else {
            console.log('Lightbox не найден в DOM');
          }
        });
        
      } catch (error) {
        console.error('Ошибка при кастомизации lightbox:', error);
      }
    },

    showEventDetails(id, e, node) {
      try {
        console.log('showEventDetails вызван с ID:', id);
        console.log('Параметры события:', { id, e, node });
        
        // Получаем все события для отладки
        const allEvents = this.schedulerInstance.getEvents();
        console.log('Все события в планировщике:', allEvents);
        
        const event = this.schedulerInstance.getEvent(id);
        console.log('Событие найдено:', event);
        
        if (!event) {
          console.log('Событие не найдено, ищем по содержимому...');
          
          // Пытаемся найти событие по содержимому элемента
          if (node && node.textContent) {
            const eventText = node.textContent.trim();
            console.log('Текст элемента:', eventText);
            
            for (const ev of allEvents) {
              if (ev.text && ev.text.includes(eventText)) {
                console.log('Найдено событие по тексту:', ev);
                this.createCustomLightbox(ev);
                return;
              }
            }
          }
          
          console.log('Событие не найдено ни по ID, ни по тексту');
          return;
        }
        
        // Создаем кастомный lightbox
        this.createCustomLightbox(event);
      } catch (error) {
        console.error('Ошибка при отображении деталей события:', error);
      }
    },

    createCustomLightbox(event) {
      try {
        // Удаляем существующий lightbox
        const existingLightbox = document.querySelector('.custom-lightbox');
        if (existingLightbox) {
          existingLightbox.remove();
        }
        
        // Создаем overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-lightbox-overlay';
        
        // Создаем модальное окно
        const modal = document.createElement('div');
        modal.className = 'custom-lightbox-modal';
        modal.innerHTML = this.createEventDetailsModal(event);
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Обработчик закрытия по клику на overlay
        overlay.addEventListener('click', (e) => {
          if (e.target === overlay) {
            overlay.remove();
          }
        });
        
        // Обработчик закрытия по Escape
        const handleEscape = (e) => {
          if (e.key === 'Escape') {
            overlay.remove();
            document.removeEventListener('keydown', handleEscape);
          }
        };
        document.addEventListener('keydown', handleEscape);
        
      } catch (error) {
        console.error('Ошибка при создании кастомного lightbox:', error);
      }
    },



    createEventDetailsModal(event) {
      // Парсим HTML из поля text для извлечения информации
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = event.text;
      
      const lines = tempDiv.textContent.split('\n').filter(line => line.trim());
      const discipline = lines[0] || '';
      const building = lines[1] || '';
      const room = lines[2] || '';
      const lessonType = lines[3] || '';
      const teacher = lines[4] || '';
      const subgroup = lines[5] || '';
      
      return `
        <div class="event-details-modal">
          <div class="event-details-header">
            <h3>${discipline}</h3>
            <button class="close-btn" onclick="this.closest('.custom-event-modal-overlay, .custom-lightbox-overlay').remove()">&times;</button>
          </div>
          <div class="event-details-content">
            <div class="detail-row">
              <span class="detail-label">Время:</span>
              <span class="detail-value">${event.start_date} - ${event.end_date}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Тип занятия:</span>
              <span class="detail-value">${lessonType}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Аудитория:</span>
              <span class="detail-value">${room}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Здание:</span>
              <span class="detail-value">${building}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Преподаватель:</span>
              <span class="detail-value">${teacher}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Подгруппа:</span>
              <span class="detail-value">${subgroup}</span>
            </div>
          </div>
        </div>
      `;
    },







    initializeScheduler() {
      try {
        
        if (!this.schedulerInstance) {
          console.error('SchedulerComponent: schedulerInstance не определен');
          return;
        }
        
        if (!this.schedulerInstance.config) {
          console.error('SchedulerComponent: schedulerInstance.config не определен');
          return;
        }
        
        // Настройка скина до инициализации
        this.schedulerInstance.skin = "material";
        
        // Конфигурация заголовка
        this.schedulerInstance.config.header = [
          "day",
          "week", 
          "month",
          "date",
          "prev",
          "today",
          "next",
        ];
        
        // Инициализация процессора данных
        this.$_initDataProcessor();
        
        // Настройка размеров и времени
        this.schedulerInstance.config.hour_size_px = 108;
        this.schedulerInstance.config.first_hour = 7;
        this.schedulerInstance.config.last_hour = 22;
        this.schedulerInstance.config.readonly = false; // Разрешаем взаимодействие
        
        console.log('Настройки планировщика:', {
          readonly: this.schedulerInstance.config.readonly,
          select: this.schedulerInstance.config.select,
          drag_resize: this.schedulerInstance.config.drag_resize,
          drag_move: this.schedulerInstance.config.drag_move
        });
        
        // Настройка lightbox
        this.schedulerInstance.config.lightbox.sections = [
          { name: "description", height: 130, map_to: "text", type: "textarea", focus: true },
          { name: "time", height: 72, type: "time", map_to: "auto" }
        ];
        
        // Дополнительные настройки для лучшего отображения
        this.schedulerInstance.config.scroll_hour = 8;
        this.schedulerInstance.config.preserve_scroll = true;
        this.schedulerInstance.config.drag_resize = false; // Отключаем изменение размера
        this.schedulerInstance.config.drag_move = false; // Отключаем перемещение
        this.schedulerInstance.config.select = true; // Разрешаем выделение событий
        
        console.log('Настройки drag и select установлены');
        
        // Отключаем автоматические обновления
        this.schedulerInstance.config.auto_scheduling = false;
        this.schedulerInstance.config.auto_scheduling_strict = false;
        
        // Отключаем автоматическое обновление при изменении данных
        this.schedulerInstance.config.auto_end_date = false;
        
        // Настройки для месячного вида
        this.schedulerInstance.config.month_dy = 160; // Высота строки дня в месячном виде
        this.schedulerInstance.config.month_date = "%j"; // Формат даты в месячном виде
        this.schedulerInstance.config.month_scale_height = 160; // Высота заголовка месяца
        this.schedulerInstance.config.month_events_limit = 10; // Лимит событий в ячейке дня
        this.schedulerInstance.config.month_show_events_text = true; // Показывать текст событий
        this.schedulerInstance.config.month_events_link = true; // Ссылки на события
        
        // Настройки отображения событий
        this.schedulerInstance.config.event_dy = 20; // Высота события
        this.schedulerInstance.config.event_min_dy = 18; // Минимальная высота события
        this.schedulerInstance.config.event_max_dy = 60; // Максимальная высота события
        
        // Настройки для лучшего отображения текста
        this.schedulerInstance.config.event_text_size = 12; // Размер шрифта событий
        this.schedulerInstance.config.event_text_line_height = 16; // Высота строки текста
        
        // Настройки для месячного вида - увеличенные размеры
        this.schedulerInstance.config.month_dy = 80; // Увеличиваем высоту строки дня
        this.schedulerInstance.config.month_scale_height = 70; // Увеличиваем высоту заголовка
        
        // Дополнительные настройки для лучшего отображения в месячном виде
        this.schedulerInstance.config.month_events_limit = 15; // Увеличиваем лимит событий
        this.schedulerInstance.config.month_show_events_text = true; // Показывать текст событий
        this.schedulerInstance.config.month_events_link = true; // Ссылки на события
        this.schedulerInstance.config.month_date = "%F %Y"; // Формат даты (Месяц Год)
        
        // Настройки для лучшего отображения событий
        this.schedulerInstance.config.event_dy = 22; // Увеличиваем высоту события
        this.schedulerInstance.config.event_min_dy = 20; // Минимальная высота события
        this.schedulerInstance.config.event_max_dy = 80; // Максимальная высота события
        
        // Настройки для лучшего отображения текста
        this.schedulerInstance.config.event_text_size = 13; // Увеличиваем размер шрифта событий
        this.schedulerInstance.config.event_text_line_height = 18; // Увеличиваем высоту строки текста
        
        // Настройки для улучшения читаемости
        this.schedulerInstance.config.event_text_color = "#ffffff"; // Цвет текста событий
        this.schedulerInstance.config.event_text_border = "1px solid rgba(255,255,255,0.3)"; // Граница текста
        
        // Настройки для месячного вида - дополнительные
        this.schedulerInstance.config.month_events_link = true; // Ссылки на события
        this.schedulerInstance.config.month_show_events_text = true; // Показывать текст событий
        this.schedulerInstance.config.month_events_limit = 20; // Еще больше событий в ячейке
        
        // Настройка обработчика для отображения детальной информации при клике
        this.schedulerInstance.attachEvent("onEventClick", (id, e, node) => {
          console.log('onEventClick сработал:', id, e, node);
          // Показываем кастомный модальный диалог
          this.showCustomModal(id);
          return false; // Предотвращаем стандартное поведение
        });
        
        // Также добавим обработчик для одинарного клика
        this.schedulerInstance.attachEvent("onClick", (id, e, node) => {
          console.log('onClick сработал:', id, e, node);
          if (id) {
            this.showCustomModal(id);
          }
        });
        
        // Настройка обработчика для кастомизации lightbox
        this.schedulerInstance.attachEvent("onLightbox", (id, e, node) => {
          console.log('onLightbox сработал:', id, e, node);
          this.customizeLightbox(id);
          return true; // Разрешаем стандартный lightbox
        });
        
        // Настройка обработчика для закрытия lightbox
        this.schedulerInstance.attachEvent("onLightboxClose", (id, e, node) => {
          console.log('onLightboxClose сработал:', id, e, node);
        });
        
        // Настройка обработчика для выделения событий
        this.schedulerInstance.attachEvent("onEventSelected", (id, e, node) => {
          console.log('onEventSelected сработал:', id, e, node);
          
          // Сохраняем ссылку на Vue компонент
          const vueComponent = this;
          console.log('Vue компонент в обработчике:', vueComponent);
          console.log('vueComponent.showCustomModal:', vueComponent.showCustomModal);
          
          // Показываем модальное окно при выделении события
          if (id) {
            console.log('Вызываем showCustomModal с ID:', id);
            console.log('Метод showCustomModal существует:', typeof vueComponent.showCustomModal);
            try {
              // Сначала проверим, что событие существует
              const event = vueComponent.schedulerInstance.getEvent(id);
              console.log('Событие найдено в обработчике:', event);
              
              if (event) {
                console.log('Вызываем vueComponent.showCustomModal...');
                vueComponent.showCustomModal(id);
                console.log('vueComponent.showCustomModal вызван');
              } else {
                console.log('Событие не найдено в планировщике');
              }
            } catch (error) {
              console.error('Ошибка при вызове showCustomModal:', error);
              console.error('Стек ошибки:', error.stack);
            }
          } else {
            console.log('ID события не найден');
          }
        });
        
        // Настройка обработчика для двойного клика
        this.schedulerInstance.attachEvent("onEventDblClick", (id, e, node) => {
          console.log('onEventDblClick сработал:', id, e, node);
          this.schedulerInstance.showLightbox(id);
          return false;
        });
        
        // Настройка обработчика для события перед изменением
        this.schedulerInstance.attachEvent("onBeforeEventChanged", (ev, e, is_new) => {
          console.log('onBeforeEventChanged сработал:', ev, e, is_new);
          if (ev && ev.id) {
            this.showCustomModal(ev.id);
            return false; // Предотвращаем изменение
          }
        });
        
        // Проверяем, что элемент существует
        if (!this.$refs.SchedulerComponent) {
          console.error('SchedulerComponent: Элемент SchedulerComponent не найден');
          return;
        }
        
        // Инициализация планировщика
        this.schedulerInstance.init(
            this.$refs.SchedulerComponent,
            new Date(),
            "week"
        );
        
        // Отмечаем, что планировщик инициализирован
        this.isSchedulerInitialized = true;
        
        // ТЕПЕРЬ настраиваем локализацию ПОСЛЕ инициализации
        
        // Проверяем, что i18n доступен
        if (this.schedulerInstance.i18n) {
          this.schedulerInstance.i18n.setLocale("ru");
          
          // Дополнительные настройки локализации для русских месяцев
          if (this.schedulerInstance.i18n.labels) {
            this.schedulerInstance.i18n.labels.month_tab = "Месяц";
            this.schedulerInstance.i18n.labels.week_tab = "Неделя";
            this.schedulerInstance.i18n.labels.day_tab = "День";
          }
          
        } else {
          console.warn('SchedulerComponent: i18n недоступен, пропускаем настройку локализации');
        }
        
        // Настройка форматов дат для русского языка
        this.schedulerInstance.config.month_date = "%F %Y"; // Полное название месяца и год
        this.schedulerInstance.config.day_date = "%j %F"; // День и месяц
        this.schedulerInstance.config.week_date = "%j %F"; // День и месяц для недели
        
        // Настройки для лучшего отображения русских дат
        this.schedulerInstance.config.date_format = "%d.%m.%Y"; // Формат даты DD.MM.YYYY
        this.schedulerInstance.config.time_format = "%H:%i"; // Формат времени HH:MM
        
        // Парсинг событий
        if (this.events && this.events.length > 0) {
          console.log('SchedulerComponent: События для парсинга:', this.events);
          this.updateScheduler();
        } else {
          console.log('SchedulerComponent: Нет начальных событий для парсинга');
        }
      } catch (error) {
        console.error('Ошибка при инициализации планировщика:', error);
        this.isSchedulerInitialized = false;
      }
    }
  },
  
  watch: {
    events: {
      handler(newEvents, oldEvents) {
        
        // Проверяем, что события действительно изменились и нет активных обновлений
        if (newEvents && this.isSchedulerInitialized && !this.isUpdating && !this.isVueUpdating) {
          const newEventsString = JSON.stringify(newEvents);
          const oldEventsString = JSON.stringify(oldEvents);
          
          if (newEventsString !== oldEventsString) {
            this.debouncedUpdateScheduler();
          }
        }
      },
      deep: false, // Отключаем глубокое отслеживание для предотвращения рекурсии
      flush: 'post' // Выполняем после обновления DOM
    }
  },
  
  mounted: function () {
    // Ждем следующего тика для гарантии, что DOM готов
    this.$nextTick(() => {
      this.initializeScheduler();
    });
  },
  
  beforeUnmount() {
    // Очистка при размонтировании компонента
    try {
      if (this.updateTimeout) {
        clearTimeout(this.updateTimeout);
      }
      

      
      // Очищаем обработчики событий DHTMLX Scheduler
      if (this.schedulerInstance) {
        this.schedulerInstance.detachEvent("onEventClick");
        this.schedulerInstance.detachEvent("onClick");
        this.schedulerInstance.detachEvent("onBeforeEventChanged");
        this.schedulerInstance.detachEvent("onLightbox");
        this.schedulerInstance.detachEvent("onLightboxClose");
        this.schedulerInstance.detachEvent("onEventSelected");
        this.schedulerInstance.detachEvent("onEventDblClick");
      }
      
      // Удаляем кастомные модальные окна
      const customLightboxes = document.querySelectorAll('.custom-lightbox-overlay');
      customLightboxes.forEach(lightbox => lightbox.remove());
      
      // Удаляем кастомные модальные диалоги
      const customModals = document.querySelectorAll('.custom-event-modal-overlay');
      customModals.forEach(modal => modal.remove());
      
      if (this.isSchedulerInitialized) {
        this.schedulerInstance.clearAll();
        this.isSchedulerInitialized = false;
      }
    } catch (error) {
      console.warn('Ошибка при очистке планировщика:', error);
    }
  }
};
</script>

<style scoped>
/* Изолированные стили для планировщика */
.scheduler-wrapper {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
  min-height: 600px;
}

.scheduler-container {
  width: 100%;
  height: 100%;
  min-height: 600px;
  max-height: none;
}

/* Переопределение стилей DHTMLX Scheduler для изоляции */
:deep(.dhx_calendar) {
  font-family: inherit !important;
  width: 100% !important;
  height: 100% !important;
  min-height: 600px !important;
}

:deep(.dhx_cal_header) {
  background-color: #f8f9fa !important;
  border-bottom: 1px solid #dee2e6 !important;
}

:deep(.dhx_cal_header div) {
  background-color: transparent !important;
  border: none !important;
  color: #495057 !important;
  font-weight: 500 !important;
}

:deep(.dhx_cal_event div) {
  border-radius: 4px !important;
  font-size: 12px !important;
}

:deep(.dhx_cal_event_line) {
  border-radius: 2px !important;
}

:deep(.dhx_cal_event_clear) {
  color: inherit !important;
}

:deep(.dhx_cal_light) {
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

:deep(.dhx_cal_light_rtl) {
  border-radius: 8px !important;
}

:deep(.dhx_cal_light_wide) {
  border-radius: 8px !important;
}

/* Стили для кнопок переключения */
:deep(.dhx_cal_tab) {
  background-color: #fff !important;
  border: 1px solid #dee2e6 !important;
  color: #495057 !important;
  border-radius: 4px !important;
  margin: 0 2px !important;
  padding: 6px 12px !important;
  font-size: 14px !important;
  transition: all 0.2s ease !important;
}

:deep(.dhx_cal_tab:hover) {
  background-color: #e9ecef !important;
  border-color: #adb5bd !important;
}

:deep(.dhx_cal_tab.active) {
  background-color: #007bff !important;
  border-color: #007bff !important;
  color: #fff !important;
}

/* Стили для ячеек времени */
:deep(.dhx_cal_event div) {
  border-radius: 3px !important;
  font-size: 11px !important;
  line-height: 1.2 !important;
}

/* Стили для заголовков колонок */
:deep(.dhx_cal_column) {
  border-right: 1px solid #dee2e6 !important;
}

:deep(.dhx_cal_column_header) {
  background-color: #f8f9fa !important;
  border-bottom: 1px solid #dee2e6 !important;
  color: #495057 !important;
  font-weight: 500 !important;
}

/* Стили для сетки времени */
:deep(.dhx_cal_data) {
  border-top: 1px solid #dee2e6 !important;
}

:deep(.dhx_cal_data div) {
  border-bottom: 1px solid #f1f3f4 !important;
}

/* Стили для навигации */
:deep(.dhx_cal_navline) {
  background-color: #fff !important;
  border-bottom: 1px solid #dee2e6 !important;
  padding: 8px 0 !important;
}

:deep(.dhx_cal_navline div) {
  background-color: transparent !important;
  border: none !important;
}

:deep(.dhx_cal_navline .dhx_cal_date) {
  font-weight: 600 !important;
  color: #212529 !important;
  font-size: 16px !important;
}

:deep(.dhx_cal_navline .dhx_cal_today_button) {
  background-color: #28a745 !important;
  border-color: #28a745 !important;
  color: #fff !important;
  border-radius: 4px !important;
  padding: 6px 12px !important;
  font-size: 14px !important;
}

:deep(.dhx_cal_navline .dhx_cal_today_button:hover) {
  background-color: #218838 !important;
  border-color: #1e7e34 !important;
}

:deep(.dhx_cal_navline .dhx_cal_prev_button),
:deep(.dhx_cal_navline .dhx_cal_next_button) {
  background-color: #6c757d !important;
  border-color: #6c757d !important;
  color: #fff !important;
  border-radius: 4px !important;
  padding: 6px 12px !important;
  font-size: 14px !important;
}

:deep(.dhx_cal_navline .dhx_cal_prev_button:hover),
:deep(.dhx_cal_navline .dhx_cal_next_button:hover) {
  background-color: #5a6268 !important;
  border-color: #545b62 !important;
}

/* Специальные стили для месячного вида */
:deep(.dhx_cal_month) {
  background-color: #fff !important;
  width: 100% !important;
  height: 100% !important;
}

:deep(.dhx_cal_month .dhx_cal_data) {
  border: 1px solid #dee2e6 !important;
  height: calc(100% - 70px) !important;
}

:deep(.dhx_cal_month .dhx_cal_data div) {
  border: 1px solid #f1f3f4 !important;
  min-height: 80px !important; /* Увеличиваем минимальную высоту ячеек */
  padding: 4px !important;
}

/* Стили для событий в месячном виде */
:deep(.dhx_cal_month .dhx_cal_event div) {
  font-size: 11px !important;
  line-height: 14px !important;
  padding: 2px 4px !important;
  margin: 1px 0 !important;
  border-radius: 3px !important;
  min-height: 16px !important;
  max-height: none !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}

/* Стили для заголовков дней в месячном виде */
:deep(.dhx_cal_month .dhx_cal_date) {
  font-size: 14px !important;
  font-weight: 600 !important;
  color: #212529 !important;
  padding: 8px 4px !important;
  background-color: #f8f9fa !important;
  border-bottom: 1px solid #dee2e6 !important;
}

/* Стили для заголовка месяца */
:deep(.dhx_cal_month .dhx_cal_header) {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
  color: #fff !important;
  font-size: 18px !important;
  font-weight: 700 !important;
  text-align: center !important;
  padding: 12px 0 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

:deep(.dhx_cal_month .dhx_cal_header div) {
  background-color: transparent !important;
  border: none !important;
  color: #fff !important;
  font-weight: 700 !important;
  font-size: 18px !important;
}

/* Стили для заголовков дней недели */
:deep(.dhx_cal_month .dhx_cal_week_header) {
  background-color: #e9ecef !important;
  border-bottom: 2px solid #dee2e6 !important;
}

:deep(.dhx_cal_month .dhx_cal_week_header div) {
  background-color: transparent !important;
  border: none !important;
  color: #495057 !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  padding: 10px 4px !important;
  text-align: center !important;
}

/* Стили для сегодняшнего дня */
:deep(.dhx_cal_month .dhx_cal_today) {
  background-color: #e3f2fd !important;
}

/* Стили для событий в месячном виде - улучшенная читаемость */
:deep(.dhx_cal_month .dhx_cal_event) {
  border-radius: 4px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
  margin: 1px 2px !important;
}

/* Стили для текста событий в месячном виде */
:deep(.dhx_cal_month .dhx_cal_event div) {
  font-weight: 500 !important;
  color: #fff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

/* Стили для ссылок на события в месячном виде */
:deep(.dhx_cal_month .dhx_cal_event_link) {
  color: #007bff !important;
  text-decoration: none !important;
}

:deep(.dhx_cal_month .dhx_cal_event_link:hover) {
  text-decoration: underline !important;
}

/* Стили для счетчика событий в месячном виде */
:deep(.dhx_cal_month .dhx_cal_event_counter) {
  background-color: #6c757d !important;
  color: #fff !important;
  border-radius: 12px !important;
  padding: 2px 6px !important;
  font-size: 10px !important;
  font-weight: 600 !important;
}

/* Адаптивность для месячного вида */
@media (max-width: 768px) {
  :deep(.dhx_cal_month .dhx_cal_data div) {
    min-height: 60px !important;
    padding: 2px !important;
  }
  
  :deep(.dhx_cal_month .dhx_cal_event div) {
    font-size: 10px !important;
    line-height: 12px !important;
    padding: 1px 2px !important;
  }
  
  :deep(.dhx_cal_month .dhx_cal_date) {
    font-size: 12px !important;
    padding: 4px 2px !important;
  }
}



/* Стили для кастомного модального диалога */
.custom-event-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  backdrop-filter: blur(4px);
}

.custom-event-modal {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  min-width: 400px;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  animation: modalFadeIn 0.3s ease-out;
}

/* Стили для кастомного lightbox */
.custom-lightbox-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  backdrop-filter: blur(4px);
}

.custom-lightbox-modal {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  min-width: 400px;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.event-details-modal {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  min-width: 400px;
  max-width: 500px;
}

.event-details-header {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: #fff;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.event-details-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 24px;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s ease;
}

.close-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.event-details-content {
  padding: 20px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #495057;
  min-width: 120px;
}

.detail-value {
  color: #212529;
  text-align: right;
  flex: 1;
  margin-left: 16px;
}

/* Адаптивность для модального окна */
@media (max-width: 768px) {
  .event-details-modal {
    min-width: 320px;
    max-width: 90vw;
  }
  
  .event-details-header {
    padding: 16px;
  }
  
  .event-details-header h3 {
    font-size: 16px;
  }
  
  .event-details-content {
    padding: 16px;
  }
  
  .detail-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  
  .detail-label {
    min-width: auto;
  }
  
  .detail-value {
    text-align: left;
    margin-left: 0;
  }
}

/* Стили для больших экранов */
@media (min-width: 1400px) {
  .scheduler-wrapper {
    min-height: 700px;
  }
  
  .scheduler-container {
    min-height: 700px;
  }
  
  :deep(.dhx_calendar) {
    min-height: 700px !important;
  }
}

@media (min-width: 1920px) {
  .scheduler-wrapper {
    min-height: 800px;
  }
  
  .scheduler-container {
    min-height: 800px;
  }
  
  :deep(.dhx_calendar) {
    min-height: 800px !important;
  }
}
</style>