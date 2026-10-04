<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    | Your Google Gemini API key from https://aistudio.google.com/apikey
    */
    'api_key' => env('GEMINI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Gemini Model
    |--------------------------------------------------------------------------
    | Model to use for vision analysis. Recommended: gemini-1.5-flash
    */
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),

    /*
    |--------------------------------------------------------------------------
    | Generation Config
    |--------------------------------------------------------------------------
    */
    'max_tokens'  => (int) env('GEMINI_MAX_TOKENS', 2048),
    'temperature' => (float) env('GEMINI_TEMPERATURE', 0.2),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    */
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models',
];
