<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitrain CRM - Система управления тренировками</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-blue-600 mb-4">Fitrain CRM</h1>
                <p class="text-gray-600 mb-8">Система управления тренировками</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="space-y-4">
                    <a href="{{ route('crm.login') }}" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Войти в систему
                    </a>
                    
                    <a href="{{ route('crm.register') }}" class="w-full bg-gray-200 text-gray-800 py-3 px-4 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Зарегистрироваться
                    </a>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Для тренеров и спортсменов
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>