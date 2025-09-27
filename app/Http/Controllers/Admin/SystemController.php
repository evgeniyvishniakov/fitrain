<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SystemController extends BaseController
{
    /**
     * Главная страница системных функций
     */
    public function index()
    {
        // Информация о системе
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_space' => $this->getDiskSpace(),
        ];

        // Статус кэша
        $cacheStatus = [
            'config' => File::exists(base_path('bootstrap/cache/config.php')),
            'routes' => File::exists(base_path('bootstrap/cache/routes.php')),
            'services' => File::exists(base_path('bootstrap/cache/services.php')),
            'packages' => File::exists(base_path('bootstrap/cache/packages.php')),
        ];

        // Размер логов
        $logSize = 0;
        if (File::exists(storage_path('logs/laravel.log'))) {
            $logSize = File::size(storage_path('logs/laravel.log'));
        }

        return view('admin.system.index', compact('systemInfo', 'cacheStatus', 'logSize'));
    }

    /**
     * Очистка кэша
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()
                ->route('admin.system.index')
                ->with('success', 'Кэш успешно очищен');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.system.index')
                ->with('error', 'Ошибка при очистке кэша: ' . $e->getMessage());
        }
    }

    /**
     * Оптимизация системы
     */
    public function optimize()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return redirect()
                ->route('admin.system.index')
                ->with('success', 'Система успешно оптимизирована');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.system.index')
                ->with('error', 'Ошибка при оптимизации: ' . $e->getMessage());
        }
    }

    /**
     * Просмотр логов
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return redirect()
                ->route('admin.system.index')
                ->with('error', 'Файл логов не найден');
        }

        // Читаем последние 1000 строк
        $logs = File::get($logFile);
        $logLines = explode("\n", $logs);
        $logLines = array_slice($logLines, -1000);
        $logLines = array_reverse($logLines);

        return view('admin.system.logs', compact('logLines'));
    }

    /**
     * Создание резервной копии
     */
    public function backup()
    {
        try {
            // Создаем папку для бэкапов если её нет
            $backupDir = storage_path('backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Экспортируем базу данных
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . '/' . $filename;

            // Получаем данные из .env
            $dbHost = env('DB_HOST', 'localhost');
            $dbPort = env('DB_PORT', '3306');
            $dbDatabase = env('DB_DATABASE');
            $dbUsername = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');

            // Команда mysqldump
            $command = "mysqldump -h {$dbHost} -P {$dbPort} -u {$dbUsername}";
            if ($dbPassword) {
                $command .= " -p{$dbPassword}";
            }
            $command .= " {$dbDatabase} > {$filepath}";

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                return redirect()
                    ->route('admin.system.index')
                    ->with('success', 'Резервная копия создана: ' . $filename);
            } else {
                return redirect()
                    ->route('admin.system.index')
                    ->with('error', 'Ошибка при создании резервной копии');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.system.index')
                ->with('error', 'Ошибка при создании резервной копии: ' . $e->getMessage());
        }
    }

    /**
     * Получение информации о дисковом пространстве
     */
    private function getDiskSpace()
    {
        $bytes = disk_free_space(base_path());
        $totalBytes = disk_total_space(base_path());
        
        return [
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($totalBytes - $bytes),
            'percentage' => round((($totalBytes - $bytes) / $totalBytes) * 100, 2)
        ];
    }

    /**
     * Форматирование байтов в читаемый вид
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

