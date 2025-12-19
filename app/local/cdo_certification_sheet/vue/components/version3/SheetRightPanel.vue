<script setup>
import { ref, onMounted } from 'vue';
import SheetButtons from './sheet-buttons.vue';
import CommissionTable from './commission_table.vue';
import SheetInfo from './sheet-info.vue';

const props = defineProps({
  sheet: {
    type: Object,
    required: true
  },
  instruction: {
    type: String,
    default: ''
  }
});

const primaryColor = ref('#0f6cbf'); // Значение по умолчанию

// Получаем основной цвет темы Moodle
onMounted(() => {
  try {
    // Пробуем получить цвет из CSS переменных Moodle
    const root = document.documentElement;
    const computedStyle = getComputedStyle(root);
    
    // Пробуем различные варианты CSS переменных Moodle
    const color = computedStyle.getPropertyValue('--primary') ||
                  computedStyle.getPropertyValue('--brand-color') ||
                  computedStyle.getPropertyValue('--primary-color') ||
                  computedStyle.getPropertyValue('--color-primary');
    
    if (color && color.trim()) {
      primaryColor.value = color.trim();
    } else {
      // Если CSS переменные не найдены, пробуем получить цвет из кнопки primary
      const testButton = document.createElement('button');
      testButton.className = 'btn btn-primary';
      testButton.style.position = 'absolute';
      testButton.style.visibility = 'hidden';
      document.body.appendChild(testButton);
      
      const buttonStyle = getComputedStyle(testButton);
      const bgColor = buttonStyle.backgroundColor;
      
      if (bgColor && bgColor !== 'rgba(0, 0, 0, 0)' && bgColor !== 'transparent') {
        primaryColor.value = bgColor;
      }
      
      document.body.removeChild(testButton);
    }
  } catch (error) {
    console.warn('Не удалось определить основной цвет темы Moodle:', error);
  }
});
</script>

<template>
  <div>
    <CommissionTable :sheet="sheet" />
    
    <!-- Информация о ведомости и инструкция в двух колонках -->
    <div class="row mt-3">
      <div class="col-md-6">
        <SheetInfo :sheet="sheet" />
      </div>
      <div class="col-md-6">
        <div class="instruction-section" :style="{ borderLeftColor: primaryColor }">
          <h6 class="font-weight-bold mb-3">Инструкция</h6>
          <div class="instruction-content">
            <slot name="instruction">
              <div v-if="instruction" v-html="instruction"></div>
              <ol v-else>
                <li>Нет задолженности, пришел на ПА и сдал: в ПР и РПА ставятся компоненты рейтинга по 100-балльной шкале, отметка — положительная.</li>
                <li>Нет задолженности, пришел на ПА, но не сдал: в ПР — предварительный рейтинг, в РПА — от 0 до 20, отметка — отрицательная.</li>
                <li>Нет задолженности, не пришел на ПА по уважительной причине: в ПР — предварительный рейтинг, в РПА — 0, отметка — «Неявка ув.», в примечании — причина с указанием реквизитов и названия предоставленного документа.</li>
                <li>Нет задолженности, не пришел на ПА без уважительной причины: в ПР — предварительный рейтинг, в РПА — 0, отметка — «Неявка».</li>
                <li>Есть задолженность: в ПР — 0, в РПА — 0, отметка — отрицательная.</li>
              </ol>
            </slot>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Кнопки управления ведомостью внизу -->
    <div class="row mt-4">
      <div class="col-12">
        <SheetButtons :sheet="sheet" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.instruction-section {
  border-left: 3px solid;
  padding-left: 1rem;
}

.instruction-content {
  font-size: 0.9rem;
  line-height: 1.5;
}

.instruction-content >>> p {
  margin-bottom: 0.5rem;
}

.instruction-content >>> ul,
.instruction-content >>> ol {
  margin-bottom: 0.5rem;
  padding-left: 1.5rem;
}

.instruction-content >>> li {
  margin-bottom: 0.5rem;
}
</style>
