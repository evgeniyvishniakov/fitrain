<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'numeric' => 'The :attribute must be a number.',
    'integer' => 'The :attribute must be an integer.',
    'boolean' => 'The :attribute field must be true or false.',
    'in' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'image' => 'The :attribute must be an image.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'size' => [
        'file' => 'The :attribute must be :size kilobytes.',
    ],
    
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'phone' => 'phone',
        'age' => 'age',
        'weight' => 'weight',
        'height' => 'height',
        'gender' => 'gender',
        'birth_date' => 'birth date',
        'avatar' => 'avatar',
        'language_code' => 'language',
        'currency_code' => 'currency',
        'timezone' => 'timezone',
    ],
];
