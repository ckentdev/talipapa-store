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
        'bugas' => ['rice', 'bugas'],
        'humay' => ['rice', 'humay'],
        'kan-on' => ['rice'],
        'kanon' => ['rice'],
        'mantika' => ['cooking oil', 'oil', 'mantika'],
        'lana' => ['cooking oil', 'oil'],
        'sabon' => ['soap', 'sabon'],
        'gatas' => ['milk', 'gatas'],
        'tuyo' => ['dried fish', 'dried herring', 'tuyo'],
        'daing' => ['dried fish', 'daing'],
        'baka' => ['cow', 'baka'],
        'vegan' => ['vegan', 'plant-based'],
        'oat' => ['oat milk', 'oat', 'vegan'],
        'nestle' => ['nestle'],
        'barato' => ['cheap', 'barato'],
        'mura' => ['cheap', 'mura'],
        'softdrinks' => ['soda', 'soft drink', 'softdrinks'],
        'soft drink' => ['soda', 'softdrinks'],
        'coke' => ['coca cola', 'coke', 'cola', 'soda'],
        'coca cola' => ['coke', 'cola', 'soda'],
        'tubig' => ['water'],
        'itlog' => ['egg', 'eggs'],
        'manok' => ['chicken'],
        'baboy' => ['pork'],
        'isda' => ['fish'],
        'asin' => ['salt'],
        'asukal' => ['sugar'],
        'kape' => ['coffee'],
        'tsaa' => ['tea'],
        'tinapay' => ['bread'],
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
