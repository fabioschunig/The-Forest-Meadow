<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Forms\ContentBuilder;
use App\Filament\Forms\TitleAndSlug;
use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('admin.sections.identity'))
                    ->columnSpan(2)
                    ->schema([
                        ...TitleAndSlug::make(),
                        Textarea::make('excerpt')
                            ->label(__('admin.fields.excerpt'))
                            ->rows(3),
                    ]),
                Section::make(__('admin.sections.publication'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('project_id')
                            ->label(__('admin.fields.project'))
                            ->relationship('project', 'slug')
                            ->getOptionLabelFromRecordUsing(fn (Project $record): string => $record->title)
                            ->preload()
                            // Set when a post is created from a project's devlog.
                            ->default(fn (): ?string => request()->query('project')),
                        DateTimePicker::make('published_at')
                            ->label(__('admin.fields.published_at'))
                            ->helperText(__('admin.fields.published_at_help'))
                            ->seconds(false),
                        FileUpload::make('cover_image')
                            ->label(__('admin.fields.cover_image'))
                            ->image()
                            ->directory('covers'),
                    ]),
                Section::make(__('admin.sections.content'))
                    ->columnSpanFull()
                    ->schema([
                        ContentBuilder::make(),
                    ]),
            ]);
    }
}
