<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_translated_page_links_both_languages(): void
    {
        Project::factory()->published()->create(['slug' => 'clareira', 'title' => ['pt_BR' => 'Clareira', 'en' => 'Clearing']]);

        $this->get('/en/projects/clareira')
            ->assertSee('<link rel="canonical" href="'.url('/en/projects/clareira').'">', false)
            ->assertSee('<link rel="alternate" hreflang="pt-BR" href="'.url('/pt/projetos/clareira').'">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en/projects/clareira').'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/pt/projetos/clareira').'">', false)
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSee('<meta property="og:locale:alternate" content="pt_BR">', false);
    }

    public function test_untranslated_english_page_points_to_the_portuguese_original(): void
    {
        Project::factory()->published()->create(['slug' => 'raizes', 'title' => ['pt_BR' => 'Raízes']]);

        $this->get('/en/projects/raizes')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/pt/projetos/raizes').'">', false)
            ->assertDontSee('<link rel="alternate" hreflang="en"', false)
            // The content shown is Portuguese, whatever the URL says.
            ->assertSee('<meta property="og:locale" content="pt_BR">', false)
            ->assertDontSee('og:locale:alternate', false);
    }

    public function test_fixed_pages_link_clean_urls_in_both_languages(): void
    {
        // Route::view() carries "view" and "status" as route parameters; they must not leak into links.
        $this->get('/pt/sobre')
            ->assertSee('<link rel="canonical" href="'.url('/pt/sobre').'">', false)
            ->assertSee('hreflang="en" href="'.url('/en/about').'"', false)
            ->assertDontSee('?view=', false);
    }

    public function test_link_preview_uses_the_cover_or_the_default_image(): void
    {
        Note::factory()->published()->create(['slug' => 'com-capa', 'cover_image' => 'covers/capa.png']);
        Note::factory()->published()->create(['slug' => 'sem-capa']);

        $this->get('/pt/anotacoes/com-capa')
            ->assertSee('<meta property="og:image" content="'.url('/uploads/covers/capa.png').'">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('article:published_time', false);

        $this->get('/pt/anotacoes/sem-capa')
            ->assertSee('<meta property="og:image" content="'.asset('images/og-default.jpg').'">', false);
    }

    public function test_not_found_page_is_not_indexed(): void
    {
        $this->get('/pt/lugar-nenhum')->assertNotFound()->assertSee('<meta name="robots" content="noindex">', false);
    }

    public function test_portuguese_feed_lists_published_notes(): void
    {
        Note::factory()->published()->create(['title' => ['pt_BR' => 'Só em português']]);
        Note::factory()->published()->create(['title' => ['pt_BR' => 'Traduzida', 'en' => 'Translated']]);
        Note::factory()->create(['title' => ['pt_BR' => 'Rascunho']]);

        $this->get('/pt/anotacoes/feed')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml;charset=UTF-8')
            ->assertSee('Só em português')
            ->assertSee('Traduzida')
            ->assertDontSee('Rascunho');
    }

    public function test_english_feed_only_lists_translated_notes(): void
    {
        Note::factory()->published()->create(['slug' => 'so-pt', 'title' => ['pt_BR' => 'Só em português']]);
        Note::factory()->published()->create(['slug' => 'traduzida', 'title' => ['pt_BR' => 'Traduzida', 'en' => 'Translated']]);

        $this->get('/en/notes/feed')
            ->assertOk()
            ->assertSee('Translated')
            ->assertSee(url('/en/notes/traduzida'), false)
            ->assertDontSee('Só em português');
    }

    public function test_pages_advertise_the_feed_of_their_language(): void
    {
        $this->get('/pt')->assertSee('type="application/rss+xml" title="The Forest Meadow · Anotações" href="'.url('/pt/anotacoes/feed').'"', false);
        $this->get('/en')->assertSee('type="application/rss+xml" title="The Forest Meadow · Notes" href="'.url('/en/notes/feed').'"', false);
    }

    public function test_sitemap_lists_published_pages_with_their_alternates(): void
    {
        Project::factory()->published()->create(['slug' => 'clareira', 'title' => ['pt_BR' => 'Clareira', 'en' => 'Clearing']]);
        Note::factory()->published()->create(['slug' => 'so-pt', 'title' => ['pt_BR' => 'Só em português']]);
        Project::factory()->create(['slug' => 'rascunho']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.url('/pt').'</loc>', false)
            ->assertSee('<loc>'.url('/en/about').'</loc>', false)
            ->assertSee('<loc>'.url('/pt/projetos/clareira').'</loc>', false)
            ->assertSee('<loc>'.url('/en/projects/clareira').'</loc>', false)
            ->assertSee('<xhtml:link rel="alternate" hreflang="en" href="'.url('/en/projects/clareira').'"/>', false)
            ->assertSee('<loc>'.url('/pt/anotacoes/so-pt').'</loc>', false)
            ->assertDontSee(url('/en/notes/so-pt'), false)
            ->assertDontSee('rascunho');
    }

    public function test_robots_blocks_the_admin_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }
}
