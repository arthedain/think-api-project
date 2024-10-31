<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'articles';

    private static string $mediaCollectionName = 'image';

    protected $fillable = ['title', 'think_api_id', 'think_api_image_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->getMediaCollectionName());
    }

    public static function getMediaCollectionName(): string
    {
        return self::$mediaCollectionName;
    }
}
