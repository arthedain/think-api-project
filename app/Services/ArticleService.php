<?php

namespace App\Services;

use App\DTO\ArticleDTO;
use App\Helpers\MediaHelper;
use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ArticleService
{
    /**
     * @throws FileCannotBeAdded
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws Exception
     */
    public function create(ArticleDTO $articleDTO): Article
    {
        try {
            DB::beginTransaction();
            $article = Article::query()->create([
                'title' => $articleDTO->title,
                'think_api_id' => $articleDTO->thinkApiId,
                'think_api_image_url' => $articleDTO->thinkApiImageUrl,
            ]);

            if (MediaHelper::existsImageByUrl($articleDTO->thinkApiImageUrl)) {
                $article->addMediaFromUrl($articleDTO->thinkApiImageUrl)->toMediaCollection(Article::getMediaCollectionName());
            } else {
                $article->copyMedia(public_path('assets/1_Main.jpg'))->toMediaCollection(Article::getMediaCollectionName());
            }
            DB::commit();

            return $article;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new Exception('Error creating article');
        }
    }

    /**
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws Exception
     */
    public function update(ArticleDTO $articleDTO): Article
    {
        try {
            DB::beginTransaction();
            $article = Article::query()
                ->with('media')
                ->where('think_api_id', $articleDTO->thinkApiId)
                ->first();

            if ($article->think_api_image_url !== $articleDTO->thinkApiImageUrl && MediaHelper::existsImageByUrl($articleDTO->thinkApiImageUrl)) {
                $article->clearMediaCollection(Article::getMediaCollectionName());
                if (MediaHelper::existsImageByUrl($articleDTO->thinkApiImageUrl)) {
                    $article->addMediaFromUrl($articleDTO->thinkApiImageUrl)->toMediaCollection(Article::getMediaCollectionName());
                } else {
                    $article->copyMedia(public_path('assets/1_Main.jpg'))->toMediaCollection(Article::getMediaCollectionName());
                }
            }

            $article->update([
                'title' => $articleDTO->title,
                'think_api_id' => $articleDTO->thinkApiId,
                'think_api_image_url' => $articleDTO->thinkApiImageUrl,
            ]);

            DB::commit();
            return $article;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new Exception('Error updating article');
        }
    }


    /**
     * Checks if a think article exists in the database.
     *
     * @param ArticleDTO $articleDTO Data transfer object containing Article details.
     * @return bool Returns true if the article exists, false otherwise.
     */
    public function existThinkApiArticle(ArticleDTO $articleDTO): bool
    {
        return Article::query()
            ->where('think_api_id', $articleDTO->thinkApiId)
            ->exists();
    }

    /**
     * @param array $ids
     * @return void
     * @throws Exception
     */
    public function removeOldArticles(array $ids): void
    {
        try {
            DB::beginTransaction();

            Article::query()->whereNotIn('think_api_id', $ids)->get()->each(function (Article $article) {
                $article->delete();
            });

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new Exception('Error removing old articles');
        }

    }
}
