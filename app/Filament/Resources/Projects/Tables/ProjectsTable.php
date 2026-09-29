<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\PublicationState;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('admin.fields.cover_image'))
                    ->square(),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge(),
                TextColumn::make('publication_state')
                    ->label(__('admin.fields.publication_state'))
                    ->state(fn (Project $record): PublicationState => $record->publicationState())
                    ->badge(),
                IconColumn::make('is_featured')
                    ->label(__('admin.fields.is_featured'))
                    ->boolean(),
            ])
            // Drag and drop defines the portfolio order on the public site.
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('admin.fields.type'))
                    ->options(ProjectType::class),
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(ProjectStatus::class),
                SelectFilter::make('publication_state')
                    ->label(__('admin.fields.publication_state'))
                    ->options(PublicationState::class)
                    ->query(fn (Builder $query, array $data) => filled($data['value'])
                        ? $query->wherePublicationState(PublicationState::from($data['value']))
                        : $query),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
