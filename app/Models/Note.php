<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

#[Fillable(['project_id', 'slug', 'title', 'excerpt', 'content', 'cover_image', 'published_at'])]
class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use HasFactory, HasTranslations, Publishable;

    /** @var array<int, string> */
    public array $translatable = ['title', 'excerpt', 'content'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
