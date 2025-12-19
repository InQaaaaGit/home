import { createRouter, createWebHashHistory } from 'vue-router';
// ВАЖНО: Прямые импорты вместо динамических для избежания чанков
import MainQualityAssessment from '../components/admin/MainQualityAssessment.vue';
import SurveyMainApp from '../components/survey/SurveyMainApp.vue';

/**
 * Конфигурация маршрутов приложения
 * Используем прямые импорты для сборки в один файл
 */
const routes = [
    {
        path: '/',
        redirect: '/admin'
    },
    {
        path: '/admin',
        name: 'Admin',
        component: MainQualityAssessment,  // Прямой импорт - не создает чанк
        meta: {
            title: 'Администрирование',
            requiresAdmin: true
        }
    },
    {
        path: '/survey',
        name: 'Survey',
        component: SurveyMainApp,  // Прямой импорт - не создает чанк
        meta: {
            title: 'Опрос',
            requiresAuth: true
        }
    }
];

/**
 * Создание роутера
 * Используем hash mode для совместимости с Moodle
 */
const router = createRouter({
    history: createWebHashHistory(),
    routes
});

/**
 * Навигационный guard для проверки доступа
 */
router.beforeEach((to, from, next) => {
    // Здесь можно добавить проверку прав доступа
    // Например, проверка capabilities из store
    next();
});

export default router;

