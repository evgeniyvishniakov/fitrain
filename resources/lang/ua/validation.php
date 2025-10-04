<?php

return [
    'required' => 'Поле :attribute обов\'язкове для заповнення.',
    'email' => 'Поле :attribute повинно містити коректну email адресу.',
    'unique' => 'Значення поля :attribute вже використовується.',
    'min' => [
        'string' => 'Поле :attribute повинно містити мінімум :min символів.',
    ],
    'max' => [
        'string' => 'Поле :attribute не повинно перевищувати :max символів.',
    ],
    'confirmed' => 'Підтвердження поля :attribute не збігається.',
    'date' => 'Поле :attribute повинно містити коректну дату.',
    'numeric' => 'Поле :attribute повинно бути числом.',
    'integer' => 'Поле :attribute повинно бути цілим числом.',
    'boolean' => 'Поле :attribute повинно бути true або false.',
    'in' => 'Вибране значення для :attribute неприпустиме.',
    'exists' => 'Вибране значення для :attribute не існує.',
    'image' => 'Поле :attribute повинно бути зображенням.',
    'mimes' => 'Поле :attribute повинно бути файлом одного з типів: :values.',
    'size' => [
        'file' => 'Розмір файлу :attribute повинен бути :size кілобайт.',
    ],
    
    'attributes' => [
        'name' => 'ім\'я',
        'email' => 'email',
        'password' => 'пароль',
        'phone' => 'телефон',
        'age' => 'вік',
        'weight' => 'вага',
        'height' => 'зріст',
        'gender' => 'стать',
        'birth_date' => 'дата народження',
        'avatar' => 'аватар',
        'language_code' => 'мова',
        'currency_code' => 'валюта',
        'timezone' => 'часовий пояс',
    ],
];
