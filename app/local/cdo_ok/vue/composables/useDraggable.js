import { ref } from 'vue';

/**
 * Composable для drag and drop функциональности
 * @returns {Object} Методы и состояния для drag and drop
 */
export function useDraggable() {
    const isDragging = ref(false);
    const draggedIndex = ref(null);

    /**
     * Начало перетаскивания
     */
    const handleDragStart = (index) => {
        isDragging.value = true;
        draggedIndex.value = index;
    };

    /**
     * Завершение перетаскивания
     */
    const handleDragEnd = () => {
        isDragging.value = false;
        draggedIndex.value = null;
    };

    /**
     * Отпускание элемента
     */
    const handleDrop = (targetIndex, items, callback) => {
        if (draggedIndex.value === null || draggedIndex.value === targetIndex) {
            handleDragEnd();
            return;
        }

        const newItems = [...items];
        const draggedItem = newItems[draggedIndex.value];
        newItems.splice(draggedIndex.value, 1);
        newItems.splice(targetIndex, 0, draggedItem);

        // Обновляем сортировку
        newItems.forEach((item, index) => {
            item.sort = index;
        });

        if (callback) {
            callback(newItems);
        }

        handleDragEnd();
    };

    return {
        isDragging,
        draggedIndex,
        handleDragStart,
        handleDragEnd,
        handleDrop
    };
}









