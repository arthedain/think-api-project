<?php

namespace App\Console\Commands;

use App\DTO\ArticleDTO;
use App\Services\API\ThinkDemoApiService;
use App\Services\ArticleService;
use Exception;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class SyncArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-articles {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Synchronizes the database with articles from the Think API.";

    /**
     * @var array
     */
    private array $articlesIds = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $id = $this->argument('id');

            if ($id) {
                $this->syncArticleWithThinkApi($id);
            } else {
                $this->syncArticlesWithThinkApi();
            }

            if($this->articlesIds) {
                $this->removeArticles($this->articlesIds);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Synchronizes the local database with articles from the Think API.
     *
     * This method retrieves articles from the Think API in a paginated manner and updates
     * or creates corresponding records in the local database.
     *
     * @param int $page The page number of results to retrieve from the Think API. Default is 1.
     * @throws Exception
     */
    private function syncArticlesWithThinkApi(int $page = 1): void
    {
        /**
         * @var ThinkDemoApiService $thinkApi
         */
        $thinkApi = resolve(ThinkDemoApiService::class);
        /**
         * @var ArticleService $articleService
         */
        $articleService = resolve(ArticleService::class);

        $response = $thinkApi->getArticles($page);

        $articles = $response['data'] ?? [];
        $meta = $response['meta'] ?? [];

        if ($articles && count($articles)) {
            foreach ($articles as $article) {
                $dto = new ArticleDTO(
                    title: $article['title'],
                    thinkApiId: $article['id'],
                    thinkApiImageUrl: $article['image']
                );

                if ($articleService->existThinkApiArticle($dto)) {
                    $articleService->update($dto);
                } else {
                    $articleService->create($dto);
                }
            }
            $count = count($articles);
            $this->articlesIds = array_merge($this->articlesIds, array_column($articles, 'id'));
            $this->info("Successfully synced $count articles from page $page");
        }

        if ($meta && $meta['last_page'] > $page) {
            $this->syncArticlesWithThinkApi($page + 1);
        }
    }


    /**
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws Exception
     */
    private function syncArticleWithThinkApi(int $id): void
    {
        /**
         * @var ThinkDemoApiService $thinkApi
         */
        $thinkApi = resolve(ThinkDemoApiService::class);
        /**
         * @var ArticleService $articleService
         */
        $articleService = resolve(ArticleService::class);

        $response = $thinkApi->getArticle($id);

        $article = $response['data'] ?? [];

        if($article) {
            $dto = new ArticleDTO(
                title: $article['title'],
                thinkApiId: $article['id'],
                thinkApiImageUrl: $article['image']
            );

            if ($articleService->existThinkApiArticle($dto)) {
                $articleService->update($dto);
            } else {
                $articleService->create($dto);
            }

            $this->info("Successfully synced article with id $id");
        } else {
            $this->info("Article with id $id not found");
        }
    }


    /**
     * @throws Exception
     */
    private function removeArticles(array $ids): void
    {
        /**
         * @var ArticleService $service
         */
        $service = resolve(ArticleService::class);
        $service->removeOldArticles($ids);
    }
}
