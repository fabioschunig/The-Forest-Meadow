<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $entries = [];

        foreach (['home', 'projects.index', 'notes.index', 'about'] as $route) {
            $entries = [...$entries, ...$this->entries($route)];
        }

        foreach (Project::published()->orderBy('sort_order')->get() as $project) {
            $entries = [...$entries, ...$this->entries('projects.show', $project)];
        }

        foreach (Note::published()->latest('published_at')->get() as $note) {
            $entries = [...$entries, ...$this->entries('notes.show', $note)];
        }

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * One entry per locale the page exists in, each listing all its alternates.
     * Untranslated content has no /en entry: its /en page points to /pt as canonical.
     *
     * @return list<array{url: string, alternates: array<string, string>, lastmod: ?string}>
     */
    private function entries(string $route, ?Model $model = null): array
    {
        $alternates = [];

        foreach (config('app.locales') as $prefix => $locale) {
            if ($model === null || $prefix === default_locale_prefix() || $model->hasTranslation('title', $locale)) {
                $parameters = $model ? ['slug' => $model->slug] : [];
                $alternates[str_replace('_', '-', $locale)] = localized_route($route, $parameters, $prefix);
            }
        }

        return array_map(fn (string $url) => [
            'url' => $url,
            'alternates' => $alternates,
            'lastmod' => $model?->updated_at?->toAtomString(),
        ], array_values($alternates));
    }
}
