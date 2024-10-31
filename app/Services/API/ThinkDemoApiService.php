<?php

namespace App\Services\API;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThinkDemoApiService
{
    private PendingRequest $client;


    public function __construct()
    {
        $this->client = Http::baseUrl(config('services.think_demo_api.base_url'));
    }

    /**
     * Retrieves the Bearer Token from the ThinkDemo API, caching it for future use.
     *
     * @return string
     * @throws Exception If unable to retrieve the Bearer Token from the ThinkDemo API.
     */
    public function getBearerToken(): string
    {
        try {
            if(Cache::has('think_demo_api_bearer_token')) {
                return Cache::get('think_demo_api_bearer_token');
            }

            $response = $this->client->post('oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.think_demo_api.client_id'),
                'client_secret' => config('services.think_demo_api.client_secret'),
            ]);

            $token = $response->json()['access_token'];

            Cache::put('think_demo_api_bearer_token', $token, Carbon::parse($response->json()['expires_in']));

            return $token;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Can't get bearer token from ThinkDemo API");
        }
    }

    /**
     *
     * @throws Exception
     */
    public function getArticles(int $page = 1): Collection
    {
        try {
            $response = $this->client->withToken($this->getBearerToken())->get('api/articles', [
                'page' => $page,
            ]);

            if($response->successful()) {
                return $response->collect();
            }

            throw new Exception("Can't get articles from ThinkDemo API");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Can't get articles from ThinkDemo API");
        }
    }

    /**
     * @throws Exception
     */
    public function getArticle(int $id): Collection
    {
        try {
            $response = $this->client->withToken($this->getBearerToken())->get('api/articles/' . $id);

            if($response->successful()) {
                return $response->collect();
            }

            throw new Exception("Article not found");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Can't get article from ThinkDemo API");
        }
    }
}
