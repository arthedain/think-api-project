<?php

namespace Tests\Services\API;

use App\Services\API\ThinkDemoApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

test('It retrieves the Bearer Token and caches it for future use', function () {
    Http::fake([
        config('services.think_demo_api.base_url').'oauth/token' => Http::response([
            'access_token' => 'test_token',
            'expires_in' => now()->addHour(),
        ])
    ]);

    $api = new ThinkDemoApiService();

    $token = $api->getBearerToken();

    expect($token)->toBe('test_token');
    expect(Cache::get('think_demo_api_bearer_token'))->toBe('test_token');
});

test('It retrieves the Bearer Token from cache if already exists', function () {
    Cache::put('think_demo_api_bearer_token', 'cached access token', now()->addHour());

    $api = new ThinkDemoApiService();

    $token = $api->getBearerToken();
    expect($token)->toBe('cached access token');
});
