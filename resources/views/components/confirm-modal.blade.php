<!-- Универсальный компонент подтверждения -->
<div x-data="confirmModalApp()" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancel()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Иконка предупреждения -->
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button @click="confirm()" type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" x-text="confirmText">
                    </button>
                    <button @click="cancel()" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" x-text="cancelText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmModalApp() {
    return {
        show: false,
        title: 'Подтверждение',
        message: 'Вы уверены, что хотите выполнить это действие?',
        confirmText: 'Подтвердить',
        cancelText: 'Отмена',
        onConfirm: null,
        onCancel: null,
        
        init() {
            // Слушаем глобальные события подтверждения
            window.addEventListener('show-confirm', (event) => {
                this.showConfirm(event.detail);
            });
        },
        
        showConfirm(options) {
            this.title = options.title || 'Подтверждение';
            this.message = options.message || 'Вы уверены, что хотите выполнить это действие?';
            this.confirmText = options.confirmText || 'Подтвердить';
            this.cancelText = options.cancelText || 'Отмена';
            this.onConfirm = options.onConfirm || null;
            this.onCancel = options.onCancel || null;
            this.show = true;
        },
        
        confirm() {
            this.show = false;
            if (this.onConfirm && typeof this.onConfirm === 'function') {
                this.onConfirm();
            }
            this.reset();
        },
        
        cancel() {
            this.show = false;
            if (this.onCancel && typeof this.onCancel === 'function') {
                this.onCancel();
            }
            this.reset();
        },
        
        reset() {
            this.title = 'Подтверждение';
            this.message = 'Вы уверены, что хотите выполнить это действие?';
            this.confirmText = 'Подтвердить';
            this.cancelText = 'Отмена';
            this.onConfirm = null;
            this.onCancel = null;
        }
    }
}
</script>
