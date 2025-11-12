@extends('admin.layouts.app')

@section('title', 'Настройки сайта')
@section('page-title', 'Настройки сайта')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <form action="{{ route('admin.site.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Основные данные</h3>
                        <p class="text-sm text-gray-500 mt-1">Используются в заголовках, письмах и публичных разделах.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Название сайта *</label>
                        <input type="text"
                               name="site_name"
                               value="{{ old('site_name', $settings['site_name']) }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('site_name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                        <textarea name="site_description"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('site_description', $settings['site_description']) }}</textarea>
                        @error('site_description')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">SEO-настройки</h3>
                        <p class="text-sm text-gray-500 mt-1">Заполняются для мета-тегов и улучшения поисковой выдачи.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               value="{{ old('meta_title', $settings['meta_title']) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('meta_title')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text"
                               name="meta_keywords"
                               value="{{ old('meta_keywords', $settings['meta_keywords']) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('meta_keywords')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description"
                              rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                    @error('meta_description')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Медиа</h3>
                        <p class="text-sm text-gray-500 mt-1">Загруженные файлы используются в админке и CRM.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип (админка)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo" accept="image/*" class="hidden">
                        </label>
                        @if(!empty($settings['logo']))
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Текущий логотип" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        @endif
                        @error('logo')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Favicon</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Загрузить favicon</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/ICO, 32×32 или 64×64, до 1 МБ</span>
                            </div>
                            <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg" class="hidden">
                        </label>
                        @if(!empty($settings['favicon']))
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Текущий favicon" class="h-10 w-10 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        @endif
                        @error('favicon')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип CRM (светлая тема)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo_light" accept="image/*" class="hidden">
                        </label>
                        @if(!empty($settings['logo_light']))
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="Логотип для светлой темы" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        @endif
                        @error('logo_light')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип CRM (тёмная тема)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo_dark" accept="image/*" class="hidden">
                        </label>
                        @if(!empty($settings['logo_dark']))
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="Логотип для тёмной темы" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        @endif
                        @error('logo_dark')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
@endsection

