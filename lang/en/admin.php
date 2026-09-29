<?php

return [
    'resources' => [
        'project' => ['singular' => 'project', 'plural' => 'projects'],
        'post' => ['singular' => 'post', 'plural' => 'posts'],
    ],

    'sections' => [
        'identity' => 'Identity',
        'dates_links' => 'Dates and links',
        'publication' => 'Publication',
        'content' => 'Content',
    ],

    'fields' => [
        'title' => 'Title',
        'slug' => 'Slug',
        'slug_help' => 'Last part of the URL, shared by both languages. Generated from the Portuguese title.',
        'type' => 'Type',
        'status' => 'Status',
        'summary' => 'Summary',
        'excerpt' => 'Excerpt',
        'content' => 'Blocks',
        'cover_image' => 'Cover',
        'links' => 'Links',
        'link_label' => 'Label',
        'link_url' => 'URL',
        'started_on' => 'Started on',
        'released_on' => 'Released on',
        'is_featured' => 'Featured',
        'sort_order' => 'Order',
        'published_at' => 'Publish at',
        'published_at_help' => 'Empty = draft. Future date = scheduled. Brasília time.',
        'project' => 'Project',
        'publication_state' => 'State',
        'updated_at' => 'Updated at',
    ],

    'blocks' => [
        'text' => 'Text',
        'image' => 'Image',
        'gallery' => 'Gallery',
        'video' => 'Video',
        'game' => 'Embedded game',
        'quote' => 'Quote',
        'body' => 'Text',
        'alt' => 'Alt text',
        'alt_help' => 'Image description for screen readers.',
        'caption' => 'Caption',
        'layout' => 'Layout',
        'layouts' => ['contained' => 'Contained', 'wide' => 'Wide', 'full' => 'Full width'],
        'images' => 'Images',
        'video_url' => 'Video URL',
        'video_url_help' => 'YouTube or Vimeo.',
        'game_url' => 'Game URL',
        'game_url_help' => 'Web build or itch.io embed.',
        'aspect_ratio' => 'Aspect ratio',
        'quote_text' => 'Quote',
        'attribution' => 'Attribution',
    ],

    'actions' => [
        'copy_from_default' => [
            'label' => 'Copy from Portuguese',
            'description' => 'This language\'s translatable fields will be replaced by the Portuguese version. Images are reused; then just translate the texts. Nothing is saved until you click Save.',
            'done' => 'Content copied from Portuguese.',
        ],
    ],
];
