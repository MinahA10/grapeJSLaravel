<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class TemplateService
{
    function convertImagesToBase64(string $html): string
{
    $appUrl = config('app.url');

    return preg_replace_callback(
        '/<img[^>]+src=["\'](' . preg_quote($appUrl, '/') . '\/storage\/[^"\']+)["\']/i',
        function ($matches) use ($appUrl) {
            $url = $matches[1];
            $relativePath = str_replace("$appUrl/storage/", '', $url);
            $filePath = public_path('storage/' . $relativePath);

            if (File::exists($filePath)) {
                $mimeType = File::mimeType($filePath);
                $base64 = base64_encode(File::get($filePath));
                return str_replace($url, "data:$mimeType;base64,$base64", $matches[0]);
            }

            return $matches[0];
        },
        $html
    );
}
}