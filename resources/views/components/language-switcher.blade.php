@php
    $currentLocale = app()->getLocale();
    $languages = \App\Models\Language::getActive();
    $user = auth()->user();
@endphp

<div class="language-switcher" x-data="languageSwitcher()">
    <!-- Мобильная версия -->
    <div class="md:hidden">
        <button @click="isOpen = !isOpen" 
                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 transition-colors">
            <span class="text-lg">{{ $languages->where('code', $currentLocale)->first()->flag ?? '🌐' }}</span>
            <span class="text-sm font-medium">{{ $languages->where('code', $currentLocale)->first()->native_name ?? 'Language' }}</span>
            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            @foreach($languages as $language)
                <button @click="switchLanguage('{{ $language->code }}')"
                        class="w-full flex items-center space-x-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors {{ $currentLocale === $language->code ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <span class="text-lg">{{ $language->flag }}</span>
                    <div>
                        <div class="font-medium">{{ $language->native_name }}</div>
                        <div class="text-xs text-gray-500">{{ $language->name }}</div>
                    </div>
                    @if($currentLocale === $language->code)
                        <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- Десктопная версия -->
    <div class="hidden md:flex items-center space-x-2">
        @foreach($languages as $language)
            <button @click="switchLanguage('{{ $language->code }}')"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors {{ $currentLocale === $language->code ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <span class="text-lg">{{ $language->flag }}</span>
                <span class="text-sm font-medium">{{ $language->code }}</span>
            </button>
        @endforeach
    </div>
</div>

<script>
function languageSwitcher() {
    return {
        isOpen: false,
        isSwitching: false,
        
        async switchLanguage(locale) {
            if (this.isSwitching) return;
            
            this.isSwitching = true;
            this.isOpen = false;
            
            try {
                const response = await fetch('{{ route("language.switch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ locale: locale })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Перезагружаем страницу для применения нового языка
                    window.location.reload();
                } else {
                    console.error('Ошибка переключения языка:', result.message);
                }
            } catch (error) {
                console.error('Ошибка при переключении языка:', error);
            } finally {
                this.isSwitching = false;
            }
        }
    }
}
</script>
