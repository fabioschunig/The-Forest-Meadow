<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/**
 * Translatable title plus a single slug shared by every locale. While creating,
 * the slug follows the default-locale (Portuguese) title until it's edited by hand.
 */
class TitleAndSlug
{
    /** @return array<int, TextInput> */
    public static function make(): array
    {
        return [
            TextInput::make('title')
                ->label(__('admin.fields.title'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state, string $operation, Page $livewire): void {
                    $isDefaultLocale = $livewire->activeLocale === $livewire::getResource()::getDefaultTranslatableLocale();
                    $slugWasEditedByHand = ($get('slug') ?? '') !== Str::slug($old ?? '');

                    if ($operation !== 'create' || ! $isDefaultLocale || $slugWasEditedByHand) {
                        return;
                    }

                    $set('slug', Str::slug($state ?? ''));
                }),
            TextInput::make('slug')
                ->label(__('admin.fields.slug'))
                ->helperText(__('admin.fields.slug_help'))
                ->required()
                ->maxLength(255)
                ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                ->unique(ignoreRecord: true),
        ];
    }
}
