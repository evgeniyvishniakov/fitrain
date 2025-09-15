<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд - Fitrain CRM</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl w-full">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-blue-600 mb-4">Добро пожаловать в Fitrain CRM!</h1>
                    <p class="text-gray-600">Система управления тренировками</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <i class="fas fa-dumbbell text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Тренировки</h3>
                                <p class="text-gray-600">Управление тренировками</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <i class="fas fa-chart-line text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Прогресс</h3>
                                <p class="text-gray-600">Отслеживание прогресса</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <i class="fas fa-users text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Спортсмены</h3>
                                <p class="text-gray-600">Управление клиентами</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <i class="fas fa-utensils text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Питание</h3>
                                <p class="text-gray-600">Планирование питания</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-500">Выберите роль для продолжения</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
