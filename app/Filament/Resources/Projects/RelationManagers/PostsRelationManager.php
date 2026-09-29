<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\RelationManagers\Concerns\Translatable;
use Livewire\Attributes\Reactive;

/**
 * The project's devlog: its posts, listed with PostResource's own table.
 */
class PostsRelationManager extends RelationManager
{
    use Translatable;

    protected static string $relationship = 'posts';

    protected static ?string $relatedResource = PostResource::class;

    #[Reactive]
    public ?string $activeLocale = null;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => PostResource::getUrl('create', ['project' => $this->getOwnerRecord()->getKey()])),
            ]);
    }
}
