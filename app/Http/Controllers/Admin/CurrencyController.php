<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends BaseController
{
    /**
     * Список валют
     */
    public function index()
    {
        $currencies = Currency::ordered()->paginate(20);
        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Форма создания валюты
     */
    public function create()
    {
        return view('admin.currencies.create');
    }

    /**
     * Создание валюты
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $currency = Currency::create($request->all());

        // Если установлена как дефолтная, снимаем флаг с других
        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Валюта успешно создана');
    }

    /**
     * Просмотр валюты
     */
    public function show($id)
    {
        $currency = Currency::findOrFail($id);
        return view('admin.currencies.show', compact('currency'));
    }

    /**
     * Форма редактирования валюты
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Обновление валюты
     */
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $currency->update($request->all());

        // Если установлена как дефолтная, снимаем флаг с других
        if ($request->boolean('is_default')) {
            $currency->setAsDefault();
        }

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Валюта успешно обновлена');
    }

    /**
     * Удаление валюты
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        
        // Нельзя удалить дефолтную валюту
        if ($currency->is_default) {
            return redirect()
                ->route('admin.currencies.index')
                ->with('error', 'Нельзя удалить валюту по умолчанию');
        }

        $currency->delete();

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Валюта успешно удалена');
    }

    /**
     * Установить как валюту по умолчанию
     */
    public function setDefault($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Валюта установлена по умолчанию'
        ]);
    }

    /**
     * Переключить статус
     */
    public function toggleStatus($id)
    {
        $currency = Currency::findOrFail($id);
        
        // Нельзя деактивировать дефолтную валюту
        if ($currency->is_default && $currency->is_active) {
            return response()->json([
                'error' => 'Нельзя деактивировать валюту по умолчанию'
            ], 403);
        }

        $currency->update(['is_active' => !$currency->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $currency->is_active,
            'message' => $currency->is_active ? 'Валюта активирована' : 'Валюта деактивирована'
        ]);
    }

    /**
     * Обновить курсы валют
     */
    public function updateRates(Request $request)
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*' => 'numeric|min:0'
        ]);

        foreach ($request->rates as $currencyId => $rate) {
            Currency::where('id', $currencyId)->update(['exchange_rate' => $rate]);
        }

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Курсы валют обновлены');
    }
}
