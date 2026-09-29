<?php

namespace App\Models\Concerns;

use App\Enums\PublicationState;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Publication is driven only by published_at: null is a draft, a future date is
 * scheduled and a past date is live. Scheduling needs no cron job.
 */
trait Publishable
{
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    #[Scope]
    protected function wherePublicationState(Builder $query, PublicationState $state): void
    {
        match ($state) {
            PublicationState::Draft => $query->whereNull('published_at'),
            PublicationState::Scheduled => $query->where('published_at', '>', now()),
            PublicationState::Published => $query->where('published_at', '<=', now()),
        };
    }

    public function publicationState(): PublicationState
    {
        return match (true) {
            $this->published_at === null => PublicationState::Draft,
            $this->published_at->isFuture() => PublicationState::Scheduled,
            default => PublicationState::Published,
        };
    }
}
