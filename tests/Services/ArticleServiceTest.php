<?php

it('creates an article successfully with valid image url', function () {

    $articleDto = new \App\DTO\ArticleDTO("Test title", "123Test", "https://fastly.picsum.photos/id/172/200/300.jpg?hmac=Z5LhQNM9g-UdcCGfryluTaIrxwIaAD_pMo_izKB2nuA");
    $articleService = app(\App\Services\ArticleService::class);
    \Illuminate\Support\Facades\DB::shouldReceive('beginTransaction')->once();
    \Illuminate\Support\Facades\DB::shouldReceive('commit')->once();
    \Illuminate\Support\Facades\DB::shouldReceive('rollBack')->never();


    $createdArticle = $articleService->create($articleDto);

    expect($createdArticle->title)->toBe($articleDto->title)
        ->and($createdArticle->think_api_id)->toBe($articleDto->thinkApiId)
        ->and($createdArticle->think_api_image_url)->toBe($articleDto->thinkApiImageUrl);
});

it('creates an article successfully with invalid image url',
function () {
    $articleDto = new \App\DTO\ArticleDTO("Test title", "123Test", "http://invalid_image_url.jpg");
    $articleService = app(\App\Services\ArticleService::class);

    \Illuminate\Support\Facades\DB::shouldReceive('beginTransaction')->once();
    \Illuminate\Support\Facades\DB::shouldReceive('commit')->once();
    \Illuminate\Support\Facades\DB::shouldReceive('rollBack')->never();

    $createdArticle = $articleService->create($articleDto);

    expect($createdArticle->title)->toBe($articleDto->title)
        ->and($createdArticle->think_api_id)->toBe($articleDto->thinkApiId)
        ->and($createdArticle->think_api_image_url)->toBe($articleDto->thinkApiImageUrl);
});

test('updates an article successfully with valid image url', function () {
    $articleDto = new \App\DTO\ArticleDTO("Updated title", "123Test", "https://fastly.picsum.photos/id/172/200/300.jpg?hmac=Z5LhQNM9g-UdcCGfryluTaIrxwIaAD_pMo_izKB2nuA");
    $articleService = app(\App\Services\ArticleService::class);
    \Illuminate\Support\Facades\DB::shouldReceive('beginTransaction')->twice();
    \Illuminate\Support\Facades\DB::shouldReceive('commit')->twice();
    \Illuminate\Support\Facades\DB::shouldReceive('rollBack')->never();

    $createdArticle = $articleService->create($articleDto);

    $updatedArticle = $articleService->update($articleDto);
    expect($updatedArticle->title)->toBe($articleDto->title)
        ->and($updatedArticle->think_api_id)->toBe($articleDto->thinkApiId)
        ->and($updatedArticle->think_api_image_url)->toBe($articleDto->thinkApiImageUrl);
});


test('updates an article successfully with invalid image url', function () {
    $articleDto = new \App\DTO\ArticleDTO("Updated title", "123Test", "http://invalid_image_url.jpg");
    $articleService = app(\App\Services\ArticleService::class);
    \Illuminate\Support\Facades\DB::shouldReceive('beginTransaction')->twice();
    \Illuminate\Support\Facades\DB::shouldReceive('commit')->twice();
    \Illuminate\Support\Facades\DB::shouldReceive('rollBack')->never();

    $createdArticle = $articleService->create($articleDto);

    $updatedArticle = $articleService->update($articleDto);
    expect($updatedArticle->title)->toBe($articleDto->title)
        ->and($updatedArticle->think_api_id)->toBe($articleDto->thinkApiId)
        ->and($updatedArticle->think_api_image_url)->toBe($articleDto->thinkApiImageUrl);
});
