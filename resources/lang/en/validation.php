<?php

return [
    'accepted' => 'The :attribute field must be accepted.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'email' => 'The :attribute field must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
    ],
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
    ],
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'attributes' => [
        'email' => 'email',
        'password' => 'password',
        'name' => 'name',
        'title' => 'title',
        'content' => 'content',
        'rating' => 'rating',
    ],
];
