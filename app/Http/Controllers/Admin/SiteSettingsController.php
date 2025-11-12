<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends BaseController
{
    /**
     * Показ страницы настроек сайта.
     */
    public function index()
    {
        $settings = [
            'site_name'        => SystemSetting::get('site.name', ''),
            'site_description' => SystemSetting::get('site.description', ''),
            'meta_title'       => SystemSetting::get('site.meta_title', ''),
            'meta_description' => SystemSetting::get('site.meta_description', ''),
            'meta_keywords'    => SystemSetting::get('site.meta_keywords', ''),
            'logo'             => SystemSetting::get('site.logo', ''),
            'logo_light'       => SystemSetting::get('site.logo_light', ''),
            'logo_dark'        => SystemSetting::get('site.logo_dark', ''),
            'favicon'          => SystemSetting::get('site.favicon', ''),
        ];

        return view('admin.site.index', compact('settings'));
    }

    /**
     * Сохранение настроек сайта.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'        => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'logo'             => ['nullable', 'image', 'max:2048'],
            'logo_light'       => ['nullable', 'image', 'max:2048'],
            'logo_dark'        => ['nullable', 'image', 'max:2048'],
            'favicon'          => ['nullable', 'file', 'mimetypes:image/png,image/x-png,image/apng,image/jpeg,image/jpg,image/pjpeg,image/x-icon,image/vnd.microsoft.icon', 'max:1024'],
        ]);

        SystemSetting::set('site.name', $data['site_name'], 'string', 'Название сайта', true);
        SystemSetting::set('site.description', $data['site_description'] ?? '', 'string', 'Описание сайта', true);
        SystemSetting::set('site.meta_title', $data['meta_title'] ?? '', 'string', 'SEO meta title', true);
        SystemSetting::set('site.meta_description', $data['meta_description'] ?? '', 'string', 'SEO meta description', true);
        SystemSetting::set('site.meta_keywords', $data['meta_keywords'] ?? '', 'string', 'SEO meta keywords', true);

        if ($request->hasFile('logo')) {
            $this->storeImageSetting($request->file('logo'), 'site.logo', 'Логотип сайта');
        }

        if ($request->hasFile('logo_light')) {
            $this->storeImageSetting($request->file('logo_light'), 'site.logo_light', 'Логотип CRM (светлая тема)');
        }

        if ($request->hasFile('logo_dark')) {
            $this->storeImageSetting($request->file('logo_dark'), 'site.logo_dark', 'Логотип CRM (тёмная тема)');
        }

        if ($request->hasFile('favicon')) {
            $this->storeImageSetting($request->file('favicon'), 'site.favicon', 'Favicon сайта');
        }

        return redirect()
            ->route('admin.site.index')
            ->with('success', 'Настройки сайта обновлены');
    }

    private function storeImageSetting($file, string $key, string $description): void
    {
        $previous = SystemSetting::get($key);
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        $path = $file->store('site', 'public');
        SystemSetting::set($key, $path, 'string', $description, true);
    }
}

