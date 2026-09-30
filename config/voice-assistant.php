<?php

return [

    'enabled' => env('VOICE_ASSISTANT_ENABLED', true),

    'search_enabled' => env('VOICE_SEARCH_ENABLED', true),

    'openai_api_key' => env('OPENAI_API_KEY'),

    'transcribe_model' => env('VOICE_ASSISTANT_TRANSCRIBE_MODEL', 'gpt-4o-mini-transcribe'),

    'transcribe_fallback_model' => env('VOICE_ASSISTANT_TRANSCRIBE_FALLBACK', 'whisper-1'),

    'tts_model' => env('VOICE_ASSISTANT_TTS_MODEL', 'tts-1'),

    'tts_voice' => env('VOICE_ASSISTANT_TTS_VOICE', 'nova'),

    'nlp_model' => env('VOICE_ASSISTANT_NLP_MODEL', 'gpt-4o-mini'),

    'reply_max_tokens' => (int) env('VOICE_ASSISTANT_REPLY_MAX_TOKENS', 120),

    'nlp_max_tokens' => (int) env('VOICE_ASSISTANT_NLP_MAX_TOKENS', 500),

    'cache_ttl' => (int) env('VOICE_ASSISTANT_CACHE_TTL', 3600),

    'max_audio_seconds' => (int) env('VOICE_ASSISTANT_MAX_AUDIO_SECONDS', 25),

    'voice_results_limit' => (int) env('VOICE_ASSISTANT_RESULTS_LIMIT', 8),

    'synonyms' => [
        'soy sauce' => ['soy sauce'],
        'cooking oil' => ['vegetable oil'],
        'dried fish' => ['dried herring'],
        'soda' => ['coke', 'cola', 'coca cola', 'soft drink'],
        'vinegar' => ['vinegar'],
        'fish sauce' => ['fish sauce'],
        'baka' => ['cow'],
        'vegan' => ['vegan', 'plant-based'],
        'oat' => ['oat milk', 'oat', 'vegan'],
        'nestle' => ['nestle'],
    ],

    'unit_aliases' => [
        'liter' => 'l',
        'litro' => 'l',
        'liters' => 'l',
        'litres' => 'l',
        'milliliter' => 'ml',
        'milliliters' => 'ml',
        'millilitre' => 'ml',
        'kilo' => 'kg',
        'kilogram' => 'kg',
        'kilograms' => 'kg',
        'gram' => 'g',
        'grams' => 'g',
        'ounce' => 'oz',
        'ounces' => 'oz',
        'piece' => 'pcs',
        'pieces' => 'pcs',
        'pc' => 'pcs',
    ],

];
