<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * Block-based content shared by projects and notes. Each block type gets its
 * own hand-made layout on the public site.
 */
class ContentBuilder
{
    public static function make(string $name = 'content'): Builder
    {
        return Builder::make($name)
            ->label(__('admin.fields.content'))
            ->blocks([
                static::textBlock(),
                static::imageBlock(),
                static::galleryBlock(),
                static::videoBlock(),
                static::gameBlock(),
                static::quoteBlock(),
            ])
            ->collapsible()
            ->cloneable()
            ->blockNumbers(false)
            ->columnSpanFull();
    }

    protected static function textBlock(): Block
    {
        return Block::make('text')
            ->label(__('admin.blocks.text'))
            ->icon(Heroicon::OutlinedBars3BottomLeft)
            ->schema([
                RichEditor::make('body')
                    ->label(__('admin.blocks.body'))
                    // Images go through the image/gallery blocks, never inline.
                    ->toolbarButtons([
                        ['bold', 'italic', 'strike', 'link'],
                        ['h2', 'h3'],
                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->required(),
            ]);
    }

    protected static function imageBlock(): Block
    {
        return Block::make('image')
            ->label(__('admin.blocks.image'))
            ->icon(Heroicon::OutlinedPhoto)
            ->schema([
                FileUpload::make('image')
                    ->label(__('admin.blocks.image'))
                    ->image()
                    ->directory('content')
                    ->required(),
                TextInput::make('alt')
                    ->label(__('admin.blocks.alt'))
                    ->helperText(__('admin.blocks.alt_help')),
                TextInput::make('caption')
                    ->label(__('admin.blocks.caption')),
                Select::make('layout')
                    ->label(__('admin.blocks.layout'))
                    ->options(__('admin.blocks.layouts'))
                    ->default('contained')
                    ->selectablePlaceholder(false),
            ]);
    }

    protected static function galleryBlock(): Block
    {
        return Block::make('gallery')
            ->label(__('admin.blocks.gallery'))
            ->icon(Heroicon::OutlinedSquares2x2)
            ->schema([
                FileUpload::make('images')
                    ->label(__('admin.blocks.images'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('content')
                    ->required(),
                TextInput::make('caption')
                    ->label(__('admin.blocks.caption')),
            ]);
    }

    protected static function videoBlock(): Block
    {
        return Block::make('video')
            ->label(__('admin.blocks.video'))
            ->icon(Heroicon::OutlinedFilm)
            ->schema([
                TextInput::make('url')
                    ->label(__('admin.blocks.video_url'))
                    ->helperText(__('admin.blocks.video_url_help'))
                    ->url()
                    ->required(),
                TextInput::make('caption')
                    ->label(__('admin.blocks.caption')),
            ]);
    }

    protected static function gameBlock(): Block
    {
        return Block::make('game')
            ->label(__('admin.blocks.game'))
            ->icon(Heroicon::OutlinedPuzzlePiece)
            ->schema([
                TextInput::make('url')
                    ->label(__('admin.blocks.game_url'))
                    ->helperText(__('admin.blocks.game_url_help'))
                    ->url()
                    ->required(),
                Select::make('aspect_ratio')
                    ->label(__('admin.blocks.aspect_ratio'))
                    ->options(['16:9' => '16:9', '4:3' => '4:3', '1:1' => '1:1'])
                    ->default('16:9')
                    ->selectablePlaceholder(false),
                TextInput::make('caption')
                    ->label(__('admin.blocks.caption')),
            ]);
    }

    protected static function quoteBlock(): Block
    {
        return Block::make('quote')
            ->label(__('admin.blocks.quote'))
            ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
            ->schema([
                Textarea::make('text')
                    ->label(__('admin.blocks.quote_text'))
                    ->rows(3)
                    ->required(),
                TextInput::make('attribution')
                    ->label(__('admin.blocks.attribution')),
            ]);
    }
}
