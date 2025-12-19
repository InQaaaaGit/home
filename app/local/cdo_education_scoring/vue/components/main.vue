<script>
import { provide, ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAppStore } from '../store/app';

export default {
    name: 'Main',
    props: {
        userId: {
            type: Number,
            default: null,
        },
        capabilities: {
            type: Object,
            default: () => ({
                isAdmin: false,
                isStudent: false,
            }),
        },
    },
    setup(props) {
        const router = useRouter();
        const route = useRoute();
        const appStore = useAppStore();
        const transitionName = ref('fade');
        const previousRoute = ref(null);

        // Проверка наличия disciplineId
        // Для администратора disciplineId не обязателен
        // Используем store для проверки роли, так как props могут не обновиться реактивно при init
        const isAdmin = computed(() => appStore.isAdmin);

        const hasDisciplineIdError = computed(() => {
            if (isAdmin.value) {
                return false;
            }
            return !appStore.disciplineId;
        });

        // Определяем роль пользователя
        const currentCapabilities = computed(() => {
            return props.capabilities || { isAdmin: false, isStudent: false };
        });

        // Предоставляем userId и capabilities для дочерних компонентов через provide/inject
        provide('userId', props.userId);
        provide('capabilities', currentCapabilities);

        // Определяем тип анимации в зависимости от маршрута
        const determineTransition = (toPath, fromPath) => {
            if (!fromPath) {
                return 'fade';
            }

            const toDepth = toPath.split('/').filter(Boolean).length;
            const fromDepth = fromPath.split('/').filter(Boolean).length;

            // Если переходим на более глубокий уровень (например, со списка на страницу анкеты)
            if (toDepth > fromDepth) {
                return 'slide-left';
            }
            // Если возвращаемся назад (например, со страницы анкеты на список)
            if (toDepth < fromDepth) {
                return 'slide-right';
            }
            // Для переходов на том же уровне используем fade
            return 'fade';
        };

        // Отслеживаем изменения маршрута для анимации
        watch(
            () => route.path,
            (newPath) => {
                if (previousRoute.value !== null) {
                    transitionName.value = determineTransition(newPath, previousRoute.value);
                }
                previousRoute.value = newPath;
            },
            { immediate: true }
        );

        // Перенаправление на правильный маршрут при загрузке
        onMounted(() => {
            // Если мы на главной или на странице списка анкет (не важно админской или студенческой)
            // проверяем права и перенаправляем куда нужно
            if (route.name === 'home' || route.path === '/' || route.name === 'student-surveys' || route.name === 'admin-surveys') {
                if (isAdmin.value) {
                    if (route.name !== 'admin-surveys') {
                        router.replace('/admin/surveys');
                    }
                } else {
                    if (route.name !== 'student-surveys') {
                        router.replace('/surveys');
                    }
                }
            }
        });

        // Отслеживаем изменения роли и перенаправляем
        watch(isAdmin, (newIsAdmin) => {
            if (route.name === 'home' || route.name === 'student-surveys' || route.name === 'admin-surveys') {
                if (newIsAdmin) {
                    router.replace('/admin/surveys');
                } else {
                    router.replace('/surveys');
                }
            }
        });

        return {
            isAdmin,
            currentCapabilities,
            transitionName,
            hasDisciplineIdError,
        };
    },
};
</script>

<template>
    <div class="main-container">
        <!-- Ошибка отсутствия disciplineId -->
        <div v-if="hasDisciplineIdError" class="error-container">
            <div class="error-message">
                <div class="error-icon">⚠️</div>
                <div class="error-content">
                    <h2 class="error-title">Ошибка инициализации</h2>
                    <p class="error-text">
                        Не передано обязательное значение <strong>disciplineId</strong>.
                        Приложение не может быть запущено без указания идентификатора дисциплины.
                    </p>
                </div>
            </div>
        </div>

        <!-- Основной контент отображается только если disciplineId передан -->
        <template v-else>
            <router-view v-slot="{ Component, route: currentRoute }">
                <transition 
                    :name="transitionName" 
                    mode="out-in"
                    appear
                >
                    <component :is="Component" :key="currentRoute.path" />
                </transition>
            </router-view>
        </template>
    </div>
</template>

<style scoped>
.main-container {
    width: 100%;
    background-color: #f5f5f5;
    box-sizing: border-box;
}

.main-container > * {
    width: 100%;
    box-sizing: border-box;
}

.main-container :deep(.router-view),
.main-container :deep(router-view) {
    width: 100%;
    display: block;
}

.test-controls {
    display: none;
}

.test-controls-panel {
    display: none;
}

.test-label {
    display: none;
}

.btn-test-toggle {
    display: none;
}

.btn-test-reset {
    display: none;
}

.test-indicator {
    display: none;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

/* Slide left transition (вперед) */
.slide-left-enter-active {
    transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-left-leave-active {
    transition: all 0.3s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-left-enter-from {
    transform: translateX(30px);
    opacity: 0;
}

.slide-left-leave-to {
    transform: translateX(-30px);
    opacity: 0;
}

.slide-left-enter-to,
.slide-left-leave-from {
    transform: translateX(0);
    opacity: 1;
}

/* Slide right transition (назад) */
.slide-right-enter-active {
    transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-right-leave-active {
    transition: all 0.3s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-right-enter-from {
    transform: translateX(-30px);
    opacity: 0;
}

.slide-right-leave-to {
    transform: translateX(30px);
    opacity: 0;
}

.slide-right-enter-to,
.slide-right-leave-from {
    transform: translateX(0);
    opacity: 1;
}

/* Обеспечиваем правильное позиционирование во время транзита */
.main-container {
    position: relative;
    overflow-x: hidden;
}

/* Улучшаем плавность анимации */
* {
    backface-visibility: hidden;
    perspective: 1000px;
}

/* Стили для сообщения об ошибке */
.error-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background-color: #f5f5f5;
}

.error-message {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    max-width: 600px;
    padding: 30px;
    background-color: #fff;
    border: 2px solid #dc3545;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
}

.error-icon {
    font-size: 48px;
    flex-shrink: 0;
}

.error-content {
    flex: 1;
}

.error-title {
    margin: 0 0 12px 0;
    font-size: 24px;
    font-weight: 600;
    color: #dc3545;
}

.error-text {
    margin: 0;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.error-text strong {
    color: #dc3545;
    font-weight: 600;
}
</style>