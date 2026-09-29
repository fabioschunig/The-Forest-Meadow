<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Filament\Forms\ContentBuilder;
use App\Filament\Forms\TitleAndSlug;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('admin.sections.identity'))
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        ...TitleAndSlug::make(),
                        Select::make('type')
                            ->label(__('admin.fields.type'))
                            ->options(ProjectType::class)
                            ->required(),
                        Select::make('status')
                            ->label(__('admin.fields.status'))
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::Idea)
                            ->required(),
                        Textarea::make('summary')
                            ->label(__('admin.fields.summary'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.publication'))
                    ->columnSpan(1)
                    ->schema([
                        DateTimePicker::make('published_at')
                            ->label(__('admin.fields.published_at'))
                            ->helperText(__('admin.fields.published_at_help'))
                            ->seconds(false),
                        Toggle::make('is_featured')
                            ->label(__('admin.fields.is_featured')),
                        FileUpload::make('cover_image')
                            ->label(__('admin.fields.cover_image'))
                            ->image()
                            ->directory('covers'),
                    ]),
                Section::make(__('admin.sections.dates_links'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        DatePicker::make('started_on')
                            ->label(__('admin.fields.started_on')),
                        DatePicker::make('released_on')
                            ->label(__('admin.fields.released_on')),
                        Repeater::make('links')
                            ->label(__('admin.fields.links'))
                            ->schema([
                                TextInput::make('label')
                                    ->label(__('admin.fields.link_label'))
                                    ->required(),
                                TextInput::make('url')
                                    ->label(__('admin.fields.link_url'))
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.content'))
                    ->columnSpanFull()
                    ->schema([
                        ContentBuilder::make(),
                    ]),
            ]);
    }
}
