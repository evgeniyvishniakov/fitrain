@extends('admin.layouts.app')

@section('title', 'Редактировать: ' . $user->name)
@section('page-title', 'Редактировать пользователя')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Редактировать пользователя</h3>
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Имя -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Имя пользователя
                </label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $user->name) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Введите имя пользователя"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email адрес
                </label>
                <input type="email" name="email" id="email" 
                       value="{{ old('email', $user->email) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                       placeholder="user@example.com"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Пароль -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Новый пароль (оставьте пустым, чтобы не менять)
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="Минимум 8 символов">
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Телефон -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2"></i>Телефон
                </label>
                <input type="tel" name="phone" id="phone" 
                       value="{{ old('phone', $user->phone) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                       placeholder="+7 (999) 123-45-67">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Роль -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-2"></i>Роль пользователя
                </label>
                <select name="role" id="role" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror"
                        required>
                    <option value="">Выберите роль</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Дополнительные поля для тренера -->
            @if($user->hasRole('trainer'))
                <div class="border-t pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">
                        <i class="fas fa-user-tie mr-2"></i>Информация о тренере
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Специализация</label>
                            <input type="text" name="specialization" id="specialization" 
                                   value="{{ old('specialization', $user->specialization) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Силовые тренировки">
                        </div>
                        
                        <div>
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">Опыт работы (лет)</label>
                            <input type="number" name="experience_years" id="experience_years" 
                                   value="{{ old('experience_years', $user->experience_years) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="5">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Дополнительные поля для спортсмена -->
            @if($user->hasRole('athlete'))
                <div class="border-t pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">
                        <i class="fas fa-running mr-2"></i>Информация о спортсмене
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Возраст</label>
                            <input type="number" name="age" id="age" 
                                   value="{{ old('age', $user->age) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="25">
                        </div>
                        
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                            <select name="gender" id="gender" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Выберите пол</option>
                                <option value="Мужской" {{ old('gender', $user->gender) == 'Мужской' ? 'selected' : '' }}>Мужской</option>
                                <option value="Женский" {{ old('gender', $user->gender) == 'Женский' ? 'selected' : '' }}>Женский</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="current_weight" class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                            <input type="number" step="0.1" name="current_weight" id="current_weight" 
                                   value="{{ old('current_weight', $user->current_weight) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="65.5">
                        </div>
                        
                        <div>
                            <label for="current_height" class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                            <input type="number" step="0.1" name="current_height" id="current_height" 
                                   value="{{ old('current_height', $user->current_height) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="170.0">
                        </div>
                        
                        <div>
                            <label for="sport_level" class="block text-sm font-medium text-gray-700 mb-2">Уровень подготовки</label>
                            <select name="sport_level" id="sport_level" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Выберите уровень</option>
                                <option value="Начинающий" {{ old('sport_level', $user->sport_level) == 'Начинающий' ? 'selected' : '' }}>Начинающий</option>
                                <option value="Средний" {{ old('sport_level', $user->sport_level) == 'Средний' ? 'selected' : '' }}>Средний</option>
                                <option value="Продвинутый" {{ old('sport_level', $user->sport_level) == 'Продвинутый' ? 'selected' : '' }}>Продвинутый</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">Тренер</label>
                            <select name="trainer_id" id="trainer_id" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Без тренера</option>
                                @foreach(\App\Models\Shared\User::whereHas('roles', function($query) {
                                    $query->where('name', 'trainer');
                                })->get() as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id', $user->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Статус -->
            <div class="border-t pt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">
                        <i class="fas fa-check-circle mr-1"></i>Активный пользователь
                    </span>
                </label>
                <p class="mt-1 text-xs text-gray-500">Неактивные пользователи не смогут войти в систему</p>
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection

