<?php

return [
    'required' => 'Поле :attribute обязательно для заполнения.',
    'email' => 'Поле :attribute должно содержать корректный email адрес.',
    'unique' => 'Значение поля :attribute уже используется.',
    'min' => [
        'string' => 'Поле :attribute должно содержать минимум :min символов.',
    ],
    'max' => [
        'string' => 'Поле :attribute не должно превышать :max символов.',
    ],
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'date' => 'Поле :attribute должно содержать корректную дату.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'boolean' => 'Поле :attribute должно быть true или false.',
    'in' => 'Выбранное значение для :attribute недопустимо.',
    'exists' => 'Выбранное значение для :attribute не существует.',
    'image' => 'Поле :attribute должно быть изображением.',
    'mimes' => 'Поле :attribute должно быть файлом одного из типов: :values.',
    'size' => [
        'file' => 'Размер файла :attribute должен быть :size килобайт.',
    ],
    
    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'password' => 'пароль',
        'phone' => 'телефон',
        'age' => 'возраст',
        'weight' => 'вес',
        'height' => 'рост',
        'gender' => 'пол',
        'birth_date' => 'дата рождения',
        'avatar' => 'аватар',
        'language_code' => 'язык',
        'currency_code' => 'валюта',
        'timezone' => 'часовой пояс',
    ],
];
