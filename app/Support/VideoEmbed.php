<?php

namespace App\Support;

class VideoEmbed
{
    /**
     * Player URL for a YouTube or Vimeo link, or null when the link isn't recognized
     * (the page then shows a plain link instead of an embed).
     */
    public static function url(string $url): ?string
    {
        $parts = parse_url(trim($url));
        $host = strtolower(preg_replace('/^(www\.|m\.)/', '', $parts['host'] ?? ''));
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        $youtubeId = match (true) {
            $host === 'youtu.be' => trim($path, '/'),
            $host === 'youtube.com' && $path === '/watch' => $query['v'] ?? null,
            $host === 'youtube.com' && preg_match('#^/(embed|shorts|live)/([^/]+)#', $path, $m) === 1 => $m[2],
            default => null,
        };

        if ($youtubeId !== null && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $youtubeId) === 1) {
            // The no-cookie domain sets no cookies until the visitor plays the video.
            return "https://www.youtube-nocookie.com/embed/{$youtubeId}";
        }

        if (in_array($host, ['vimeo.com', 'player.vimeo.com'], true) && preg_match('#/(\d+)(?:/|$)#', $path, $m) === 1) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return null;
    }
}
