<?php

namespace App\DTO;

use Exception;
use Illuminate\Support\Facades\Validator;

class ArticleDTO
{
    /**
     * @throws Exception
     */
    public function __construct(
        public string $title,
        public string $thinkApiId,
        public string $thinkApiImageUrl,
    )
    {
        $this->validate();
    }

    /**
     * @throws Exception
     */
    private function validate(): void
    {
        $validator = Validator::make([
            'title' => $this->title,
            'thinkApiId' => $this->thinkApiId,
            'thinkApiImageUrl' => $this->thinkApiImageUrl,
        ], [
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'thinkApiId' => ['required', 'string', 'max:255', 'min:1'],
            'thinkApiImageUrl' => ['required', 'string', 'max:255', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}
