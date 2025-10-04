<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends BaseController
{
    /**
     * Список языков
     */
    public function index()
    {
        $languages = Language::ordered()->paginate(20);
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Форма создания языка
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Создание языка
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $language = Language::create($request->all());

        // Если установлен как дефолтный, снимаем флаг с других
        if ($request->boolean('is_default')) {
            $language->setAsDefault();
        }

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Язык успешно создан');
    }

    /**
     * Просмотр языка
     */
    public function show($id)
    {
        $language = Language::findOrFail($id);
        return view('admin.languages.show', compact('language'));
    }

    /**
     * Форма редактирования языка
     */
    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Обновление языка
     */
    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $language->update($request->all());

        // Если установлен как дефолтный, снимаем флаг с других
        if ($request->boolean('is_default')) {
            $language->setAsDefault();
        }

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Язык успешно обновлен');
    }

    /**
     * Удаление языка
     */
    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        
        // Нельзя удалить дефолтный язык
        if ($language->is_default) {
            return redirect()
                ->route('admin.languages.index')
                ->with('error', 'Нельзя удалить язык по умолчанию');
        }

        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Язык успешно удален');
    }

    /**
     * Установить как язык по умолчанию
     */
    public function setDefault($id)
    {
        try {
            $language = Language::findOrFail($id);
            $language->setAsDefault();

            return response()->json([
                'success' => true,
                'message' => 'Язык установлен по умолчанию'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Переключить статус
     */
    public function toggleStatus($id)
    {
        $language = Language::findOrFail($id);
        
        // Нельзя деактивировать дефолтный язык
        if ($language->is_default && $language->is_active) {
            return response()->json([
                'error' => 'Нельзя деактивировать язык по умолчанию'
            ], 403);
        }

        $language->update(['is_active' => !$language->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $language->is_active,
            'message' => $language->is_active ? 'Язык активирован' : 'Язык деактивирован'
        ]);
    }
}
