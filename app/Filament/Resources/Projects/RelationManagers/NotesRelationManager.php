<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Notes\NoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\RelationManagers\Concerns\Translatable;
use Livewire\Attributes\Reactive;

/**
 * The project's devlog: its notes, listed with NoteResource's own table.
 */
class NotesRelationManager extends RelationManager
{
    use Translatable;

    protected static string $relationship = 'notes';

    protected static ?string $relatedResource = NoteResource::class;

    #[Reactive]
    public ?string $activeLocale = null;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => NoteResource::getUrl('create', ['project' => $this->getOwnerRecord()->getKey()])),
            ]);
    }
}
