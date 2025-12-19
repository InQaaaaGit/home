/**
 * Хелпер для drag and drop функциональности
 */
export class DragHelper {
    constructor() {
        this.draggedElement = null;
        this.placeholder = null;
    }

    /**
     * Начало перетаскивания
     */
    onDragStart(event, data) {
        this.draggedElement = event.target;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', event.target.innerHTML);
        
        event.target.classList.add('dragging');
    }

    /**
     * Завершение перетаскивания
     */
    onDragEnd(event) {
        event.target.classList.remove('dragging');
        this.draggedElement = null;
    }

    /**
     * При наведении на элемент
     */
    onDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        return false;
    }

    /**
     * При входе в область элемента
     */
    onDragEnter(event) {
        event.target.classList.add('drag-over');
    }

    /**
     * При выходе из области элемента
     */
    onDragLeave(event) {
        event.target.classList.remove('drag-over');
    }
}

export default new DragHelper();









