<!-- Универсальный компонент уведомлений -->
<div x-data="notificationApp()" x-show="show" x-transition:enter="transform ease-out duration-300 transition" x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 fixed top-4 right-4 z-50">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Иконка успеха -->
                <svg x-show="type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                
                <!-- Иконка ошибки -->
                <svg x-show="type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                
                <!-- Иконка предупреждения -->
                <svg x-show="type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                
                <!-- Иконка информации -->
                <svg x-show="type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900" x-text="title"></p>
                <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
            </div>
            
            <div class="ml-4 flex flex-shrink-0">
                <button @click="hide()" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span class="sr-only">Закрыть</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function notificationApp() {
    return {
        show: false,
        type: 'success', // success, error, warning, info
        title: '',
        message: '',
        timeout: null,
        
        init() {
            // Слушаем глобальные события уведомлений
            window.addEventListener('show-notification', (event) => {
                this.showNotification(event.detail.type, event.detail.title, event.detail.message);
            });
        },
        
        showNotification(type, title, message, duration = 5000) {
            this.type = type;
            this.title = title;
            this.message = message;
            this.show = true;
            
            // Автоматически скрываем через указанное время
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            
            this.timeout = setTimeout(() => {
                this.hide();
            }, duration);
        },
        
        hide() {
            this.show = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
        }
    }
}
</script>

<style>
/* Исправление только для мобильных устройств */
@media (max-width: 639px) {
    .fixed.top-4.right-4 {
        left: 1rem !important;
        right: 1rem !important;
        width: calc(100vw - 2rem) !important;
        max-width: none !important;
    }
}
</style>

