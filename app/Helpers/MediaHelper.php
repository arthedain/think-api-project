<?php

namespace App\Helpers;

class MediaHelper
{
    public static function existsImageByUrl(string $url): bool
    {
        $headers = @get_headers($url);
        return is_array($headers) && str_contains($headers[0], '200');
    }
}
