<?php

namespace App\Filament\Resources\Notes\Tables;

use App\Enums\PublicationState;
use App\Models\Note;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('project.title')
                    ->label(__('admin.fields.project'))
                    // Redundant inside a project's own devlog.
                    ->hidden(fn ($livewire): bool => $livewire instanceof RelationManager),
                TextColumn::make('publication_state')
                    ->label(__('admin.fields.publication_state'))
                    ->state(fn (Note $record): PublicationState => $record->publicationState())
                    ->badge(),
                TextColumn::make('published_at')
                    ->label(__('admin.fields.published_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('admin.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('project')
                    ->label(__('admin.fields.project'))
                    ->relationship('project', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->title)
                    ->hidden(fn ($livewire): bool => $livewire instanceof RelationManager),
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
