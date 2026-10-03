<?php

use App\Models\Note;

return [

    /*
    |--------------------------------------------------------------------------
    | Feeds
    |--------------------------------------------------------------------------
    |
    | One feed per locale, keyed by URL prefix. Routes are registered inside
    | each locale's group in routes/web.php (named "{prefix}.feeds.{prefix}"),
    | so the locale is already set when the items are resolved. Titles are
    | plain strings because config files are loaded before the translator.
    |
    */

    'feeds' => [
        'pt' => [
            'items' => [Note::class, 'feedItems'],
            'url' => '',
            'title' => 'The Forest Meadow · Anotações',
            'description' => 'Avanços, estudos e bastidores de jogos, desenhos e textos.',
            'language' => 'pt-BR',
            'format' => 'rss',
            'view' => 'feed::rss',
            'image' => '',
            'type' => '',
            'contentType' => '',
        ],

        'en' => [
            'items' => [Note::class, 'feedItems'],
            'url' => '',
            'title' => 'The Forest Meadow · Notes',
            'description' => 'Progress, studies and behind the scenes of games, drawings and writing.',
            'language' => 'en',
            'format' => 'rss',
            'view' => 'feed::rss',
            'image' => '',
            'type' => '',
            'contentType' => '',
        ],
    ],

];
