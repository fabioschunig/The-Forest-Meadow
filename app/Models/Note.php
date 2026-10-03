<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Translatable\HasTranslations;

#[Fillable(['project_id', 'slug', 'title', 'excerpt', 'content', 'cover_image', 'published_at'])]
class Note extends Model implements Feedable
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

    /**
     * Items for the active locale's feed. Feeds other than the default locale
     * only list notes translated into that locale.
     *
     * @return Collection<int, self>
     */
    public static function feedItems(): Collection
    {
        $locale = App::getLocale();

        return static::query()->published()
            ->when($locale !== default_locale(), fn ($query) => $query->whereNotNull("title->{$locale}"))
            ->latest('published_at')
            ->take(20)
            ->get();
    }

    public function toFeedItem(): FeedItem
    {
        $url = localized_route('notes.show', ['slug' => $this->slug]);

        return FeedItem::create()
            ->id($url)
            ->title($this->title)
            ->summary($this->excerpt ?? '')
            ->updated($this->published_at)
            ->link($url)
            ->authorName(config('app.name'));
    }
}
