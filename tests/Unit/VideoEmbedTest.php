<?php

namespace Tests\Unit;

use App\Support\VideoEmbed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    /** @return array<string, array{string, ?string}> */
    public static function urls(): array
    {
        return [
            'youtube watch' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ'],
            'youtube watch with extra params' => ['https://youtube.com/watch?t=42&v=dQw4w9WgXcQ', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ'],
            'youtube short link' => ['https://youtu.be/dQw4w9WgXcQ', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ'],
            'youtube mobile' => ['https://m.youtube.com/watch?v=dQw4w9WgXcQ', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ'],
            'youtube shorts' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ'],
            'vimeo' => ['https://vimeo.com/76979871', 'https://player.vimeo.com/video/76979871'],
            'vimeo player' => ['https://player.vimeo.com/video/76979871', 'https://player.vimeo.com/video/76979871'],
            'other site' => ['https://example.com/video/123', null],
            'youtube without id' => ['https://www.youtube.com/watch', null],
            'injection attempt' => ['https://youtu.be/abc"onload="x', null],
        ];
    }

    #[DataProvider('urls')]
    public function test_converts_video_links_to_player_urls(string $url, ?string $expected): void
    {
        $this->assertSame($expected, VideoEmbed::url($url));
    }
}
