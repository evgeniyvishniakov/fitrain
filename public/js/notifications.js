// Утилиты для работы с уведомлениями и подтверждениями

/**
 * Показать уведомление
 * @param {string} type - Тип уведомления: 'success', 'error', 'warning', 'info'
 * @param {string} title - Заголовок уведомления
 * @param {string} message - Текст уведомления
 * @param {number} duration - Время показа в миллисекундах (по умолчанию 5000)
 */
function showNotification(type, title, message, duration = 5000) {
    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: {
            type: type,
            title: title,
            message: message,
            duration: duration
        }
    }));
}

/**
 * Показать уведомление об успехе
 */
function showSuccess(title, message, duration) {
    showNotification('success', title, message, duration);
}

/**
 * Показать уведомление об ошибке
 */
function showError(title, message, duration) {
    showNotification('error', title, message, duration);
}

/**
 * Показать предупреждение
 */
function showWarning(title, message, duration) {
    showNotification('warning', title, message, duration);
}

/**
 * Показать информационное уведомление
 */
function showInfo(title, message, duration) {
    showNotification('info', title, message, duration);
}

/**
 * Показать модальное окно подтверждения
 * @param {Object} options - Опции подтверждения
 * @param {string} options.title - Заголовок
 * @param {string} options.message - Сообщение
 * @param {string} options.confirmText - Текст кнопки подтверждения
 * @param {string} options.cancelText - Текст кнопки отмены
 * @param {Function} options.onConfirm - Функция при подтверждении
 * @param {Function} options.onCancel - Функция при отмене
 */
function showConfirm(options) {
    window.dispatchEvent(new CustomEvent('show-confirm', {
        detail: options
    }));
}

/**
 * Простое подтверждение с кастомными текстами
 */
function confirmAction(title, message, onConfirm, onCancel) {
    showConfirm({
        title: title,
        message: message,
        confirmText: 'Подтвердить',
        cancelText: 'Отмена',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
}

/**
 * Подтверждение удаления
 */
function confirmDelete(itemName, onConfirm, onCancel) {
    showConfirm({
        title: 'Подтверждение удаления',
        message: `Вы уверены, что хотите удалить "${itemName}"? Это действие нельзя отменить.`,
        confirmText: 'Удалить',
        cancelText: 'Отмена',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
}

// Экспорт для использования в других файлах
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showNotification,
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showConfirm,
        confirmAction,
        confirmDelete
    };
}
