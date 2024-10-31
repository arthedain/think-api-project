<?php

it('can create dto with valid data', function () {
    $dto = new \App\DTO\ArticleDTO('title', '123', "https://fastly.picsum.photos/id/172/200/300.jpg?hmac=Z5LhQNM9g-UdcCGfryluTaIrxwIaAD_pMo_izKB2nuA");
    expect($dto)->toBeInstanceOf(\App\DTO\ArticleDTO::class);
});

it('fails to create dto with invalid data', function () {
    expect(fn() => new \App\DTO\ArticleDTO('', '', ""))->toThrow(Exception::class);
});
