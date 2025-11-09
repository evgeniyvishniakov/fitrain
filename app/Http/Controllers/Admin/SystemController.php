<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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

        // Получаем список резервных копий
        $backups = $this->getBackupsList();

        return view('admin.system.index', compact('systemInfo', 'cacheStatus', 'logSize', 'backups'));
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

            // Получаем правильный URL с портом из текущего запроса
            $host = request()->header('Host') ?: request()->getHttpHost();
            $scheme = request()->getScheme();
            $url = $scheme . '://' . $host . '/system';
            return redirect()
                ->to($url)
                ->with('success', 'Кэш успешно очищен');
        } catch (\Exception $e) {
            $host = request()->header('Host') ?: request()->getHttpHost();
            $scheme = request()->getScheme();
            $url = $scheme . '://' . $host . '/system';
            return redirect()
                ->to($url)
                ->with('error', 'Ошибка при очистке кэша: ' . $e->getMessage());
        }
    }

    /**
     * Оптимизация системы
     */
    public function optimize()
    {
        try {
            $messages = [];
            
            // Кэшируем конфигурацию
            try {
                Artisan::call('config:cache');
                $messages[] = 'Конфигурация закэширована';
            } catch (\Exception $e) {
                $messages[] = 'Ошибка кэширования конфигурации: ' . $e->getMessage();
            }
            
            // Очищаем кэш маршрутов (не кэшируем, так как это может вызывать проблемы)
            try {
                Artisan::call('route:clear');
                $messages[] = 'Кэш маршрутов очищен';
            } catch (\Exception $e) {
                // Игнорируем ошибку очистки
            }
            
            // Очищаем кэш представлений
            try {
                Artisan::call('view:clear');
                $messages[] = 'Кэш представлений очищен';
            } catch (\Exception $e) {
                $messages[] = 'Ошибка очистки кэша представлений: ' . $e->getMessage();
            }
            
            // Оптимизируем автозагрузку (если доступно)
            try {
                Artisan::call('optimize');
                $messages[] = 'Автозагрузка оптимизирована';
            } catch (\Exception $e) {
                // Игнорируем, если команда не доступна
            }

            $message = 'Система оптимизирована. ' . implode('. ', $messages);

            // Получаем правильный URL с портом из текущего запроса
            $host = request()->header('Host') ?: request()->getHttpHost();
            $scheme = request()->getScheme();
            $url = $scheme . '://' . $host . '/system';
            return redirect()
                ->to($url)
                ->with('success', $message);
        } catch (\Exception $e) {
            $host = request()->header('Host') ?: request()->getHttpHost();
            $scheme = request()->getScheme();
            $url = $scheme . '://' . $host . '/system';
            return redirect()
                ->to($url)
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
            $host = request()->header('Host') ?: request()->getHttpHost();
            $scheme = request()->getScheme();
            $url = $scheme . '://' . $host . '/system';
            return redirect()
                ->to($url)
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
     * Создание резервной копии базы данных
     */
    public function backupDb()
    {
        try {
            // Создаем папку для бэкапов если её нет
            $backupDir = storage_path('backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Экспортируем базу данных
            $filename = 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Получаем данные из конфигурации
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort = config('database.connections.mysql.port', '3306');
            $dbDatabase = config('database.connections.mysql.database');
            $dbUsername = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            if (empty($dbDatabase)) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Не указано имя базы данных в конфигурации');
            }

            // Сохраняем оригинальное имя базы данных для альтернативного метода
            $dbDatabaseOriginal = $dbDatabase;
            
            // Экранируем специальные символы для безопасности
            $dbHost = escapeshellarg($dbHost);
            $dbPort = escapeshellarg($dbPort);
            $dbDatabaseEscaped = escapeshellarg($dbDatabase);
            $dbUsername = escapeshellarg($dbUsername);
            $filepathEscaped = escapeshellarg($filepath);

            // Формируем команду mysqldump
            // Для Windows используем полный путь, для Linux - просто mysqldump
            $mysqldump = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Попытка найти mysqldump в стандартных местах Windows
                $possiblePaths = [
                    'D:\\OSPanel\\modules\\database\\MySQL-8.0\\bin\\mysqldump.exe',
                    'D:\\OSPanel\\modules\\database\\MySQL-5.7\\bin\\mysqldump.exe',
                    'D:\\OSPanel\\modules\\database\\MySQL-5.6\\bin\\mysqldump.exe',
                    'C:\\OSPanel\\modules\\database\\MySQL-8.0\\bin\\mysqldump.exe',
                    'C:\\OSPanel\\modules\\database\\MySQL-5.7\\bin\\mysqldump.exe',
                    'C:\\OSPanel\\modules\\database\\MySQL-5.6\\bin\\mysqldump.exe',
                    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                    'C:\\wamp\\bin\\mysql\\mysql' . substr($dbPort, 0, 1) . '\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                ];
                
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $mysqldump = $path;
                        break;
                    }
                }
            }

            // Пробуем использовать mysqldump, если доступен
            $useMysqldump = false;
            if (file_exists($mysqldump) || $mysqldump === 'mysqldump') {
                // Команда mysqldump
                $command = "{$mysqldump} -h {$dbHost} -P {$dbPort} -u {$dbUsername}";
                
                // Пароль передаем через переменную окружения для безопасности
                if (!empty($dbPassword)) {
                    $dbPasswordEscaped = escapeshellarg($dbPassword);
                    // Для Windows используем set, для Linux - export
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $command = "set MYSQL_PWD={$dbPasswordEscaped} && {$command} {$dbDatabaseEscaped} > {$filepathEscaped} 2>&1";
                    } else {
                        $command = "MYSQL_PWD={$dbPasswordEscaped} {$command} {$dbDatabaseEscaped} > {$filepathEscaped} 2>&1";
                    }
                } else {
                    $command .= " {$dbDatabaseEscaped} > {$filepathEscaped} 2>&1";
                }

                // Выполняем команду
                exec($command, $output, $returnCode);

                // Проверяем результат
                if ($returnCode === 0 && File::exists($filepath) && File::size($filepath) > 0) {
                    $useMysqldump = true;
                }
            }

            // Если mysqldump не сработал, используем альтернативный метод через Laravel DB
            if (!$useMysqldump) {
                $this->backupDbViaLaravel($filepath, $dbDatabaseOriginal);
            }

            // Проверяем финальный результат
            if (File::exists($filepath) && File::size($filepath) > 0) {
                $fileSize = File::size($filepath);
                $fileSizeFormatted = $this->formatBytes($fileSize);
                
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('success', 'Резервная копия БД создана: ' . $filename . ' (' . $fileSizeFormatted . ')');
            } else {
                $errorMessage = 'Ошибка при создании резервной копии БД';
                if (!empty($output)) {
                    $errorMessage .= ': ' . implode(' ', array_slice($output, -3));
                }
                
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
            return redirect()
                ->to($url)
                ->with('error', 'Ошибка при создании резервной копии БД: ' . $e->getMessage());
        }
    }

    /**
     * Создание резервной копии файлов проекта
     */
    public function backupFiles(Request $request)
    {
        try {
            // Увеличиваем время выполнения для больших проектов
            @set_time_limit(0); // Убираем лимит времени
            @ini_set('max_execution_time', 0);
            @ini_set('memory_limit', '1024M'); // Увеличиваем память до 1GB
            ignore_user_abort(true); // Продолжаем работу даже если соединение прервано
            
            $includeImages = $request->input('include_images', 0);

            // Создаем папку для бэкапов если её нет
            $backupDir = storage_path('backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $filename = $includeImages ? 'files_backup_with_images_' . date('Y-m-d_H-i-s') . '.zip' : 'files_backup_' . date('Y-m-d_H-i-s') . '.zip';
            $filepath = $backupDir . '/' . $filename;

            // Проверяем наличие ZipArchive
            if (!class_exists('ZipArchive')) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Класс ZipArchive не доступен. Установите расширение php-zip');
            }

            $zip = new \ZipArchive();
            
            if ($zip->open($filepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Не удалось создать ZIP архив');
            }

            // Папки и файлы для исключения из бэкапа
            $exclude = [
                'node_modules',
                'vendor',
                '.git',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
                'storage/backups',
                '.env',
                '.gitignore',
                'composer.lock',
                'package-lock.json',
            ];
            
            // Если не включаем картинки, исключаем папки с медиа-файлами
            if (!$includeImages) {
                $exclude[] = 'storage/app/public'; // Загруженные файлы (картинки, видео и т.д.)
                $exclude[] = 'public/storage'; // Символическая ссылка на storage/app/public
                $exclude[] = 'public/uploads'; // Если есть папка uploads
            }

            // Функция для добавления файлов в архив
            $addToZip = function($dir, $zip, $basePath = '') use (&$addToZip, $exclude) {
                $files = scandir($dir);
                
                foreach ($files as $file) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    
                    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                    $relativePath = ($basePath ? $basePath . '/' : '') . $file;
                    
                    // Нормализуем путь для сравнения (заменяем \ на /)
                    $normalizedPath = str_replace('\\', '/', $relativePath);
                    
                    // Проверяем исключения
                    $shouldExclude = false;
                    foreach ($exclude as $excludePath) {
                        $normalizedExclude = str_replace('\\', '/', $excludePath);
                        if (strpos($normalizedPath, $normalizedExclude) === 0) {
                            $shouldExclude = true;
                            break;
                        }
                    }
                    
                    if ($shouldExclude) {
                        continue;
                    }
                    
                    if (is_dir($filePath)) {
                        $addToZip($filePath, $zip, $relativePath);
                    } else {
                        // Пропускаем очень большие файлы (> 50MB)
                        if (filesize($filePath) > 50 * 1024 * 1024) {
                            continue;
                        }
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            };

            // Добавляем файлы проекта
            $projectPath = base_path();
            $addToZip($projectPath, $zip);

            $zip->close();

            if (File::exists($filepath)) {
                $fileSize = File::size($filepath);
                $fileSizeFormatted = $this->formatBytes($fileSize);
                
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('success', 'Резервная копия файлов создана: ' . $filename . ' (' . $fileSizeFormatted . ')');
            } else {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Ошибка при создании резервной копии файлов');
            }
        } catch (\Exception $e) {
            $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
            return redirect()
                ->to($url)
                ->with('error', 'Ошибка при создании резервной копии файлов: ' . $e->getMessage());
        }
    }

    /**
     * Создание резервной копии БД через Laravel DB (альтернативный метод)
     */
    private function backupDbViaLaravel($filepath, $database)
    {
        try {
            $handle = fopen($filepath, 'w');
            
            if (!$handle) {
                throw new \Exception('Не удалось создать файл для резервной копии');
            }

            // Записываем заголовок
            fwrite($handle, "-- MySQL dump created via Laravel\n");
            fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET time_zone = \"+00:00\";\n\n");
            fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
            fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
            fwrite($handle, "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
            fwrite($handle, "/*!40101 SET NAMES utf8mb4 */;\n\n");

            // Получаем список таблиц
            $tables = DB::select("SHOW TABLES");
            $tableKey = "Tables_in_{$database}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Получаем структуру таблицы
                fwrite($handle, "\n-- Структура таблицы `{$tableName}`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableSql = $createTable[0]->{'Create Table'};
                fwrite($handle, $createTableSql . ";\n\n");

                // Получаем данные таблицы
                $rows = DB::table($tableName)->get();
                
                if (count($rows) > 0) {
                    fwrite($handle, "-- Дамп данных таблицы `{$tableName}`\n");
                    fwrite($handle, "LOCK TABLES `{$tableName}` WRITE;\n");
                    fwrite($handle, "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n");

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if ($value === null) {
                                $values[] = 'NULL';
                            } else {
                                $value = addslashes($value);
                                $value = str_replace(["\n", "\r", "\t"], ["\\n", "\\r", "\\t"], $value);
                                $values[] = "'{$value}'";
                            }
                        }
                        fwrite($handle, "INSERT INTO `{$tableName}` VALUES (" . implode(',', $values) . ");\n");
                    }

                    fwrite($handle, "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n");
                    fwrite($handle, "UNLOCK TABLES;\n\n");
                }
            }

            // Закрываем файл
            fwrite($handle, "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n");
            fwrite($handle, "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n");
            fwrite($handle, "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n");
            
            fclose($handle);
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            throw $e;
        }
    }

    /**
     * Получение списка резервных копий
     */
    private function getBackupsList($limit = 10)
    {
        $backupDir = storage_path('backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'type' => strpos($file->getFilename(), 'db_backup_') === 0 ? 'db' : 'files',
                ];
            }

            // Сортируем по дате создания (новые первыми)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Ограничиваем количество
            $backups = array_slice($backups, 0, $limit);
        }

        return $backups;
    }

    /**
     * Скачивание резервной копии
     */
    public function downloadBackup($filename)
    {
        try {
            $backupDir = storage_path('backups');
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Проверяем безопасность имени файла
            if (preg_match('/[^a-zA-Z0-9._-]/', $filename) || strpos($filename, '..') !== false) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Некорректное имя файла');
            }

            if (!File::exists($filepath)) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Файл не найден');
            }

            return response()->download($filepath, $filename);
        } catch (\Exception $e) {
            $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
            return redirect()
                ->to($url)
                ->with('error', 'Ошибка при скачивании файла: ' . $e->getMessage());
        }
    }

    /**
     * Удаление резервной копии
     */
    public function deleteBackup($filename)
    {
        try {
            $backupDir = storage_path('backups');
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Проверяем безопасность имени файла
            if (preg_match('/[^a-zA-Z0-9._-]/', $filename) || strpos($filename, '..') !== false) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Некорректное имя файла');
            }

            if (!File::exists($filepath)) {
                $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
                return redirect()
                    ->to($url)
                    ->with('error', 'Файл не найден');
            }

            File::delete($filepath);

            $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
            return redirect()
                ->to($url)
                ->with('success', 'Резервная копия удалена: ' . $filename);
        } catch (\Exception $e) {
            $url = request()->getScheme() . '://' . request()->getHttpHost() . '/system';
            return redirect()
                ->to($url)
                ->with('error', 'Ошибка при удалении файла: ' . $e->getMessage());
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

