<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_url_segments_are_translated(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clareira']);
        $note = Note::factory()->published()->create(['slug' => 'primeiro-diario']);

        $this->get('/pt/projetos')->assertOk();
        $this->get('/en/projects')->assertOk();
        $this->get('/pt/projetos/clareira')->assertOk();
        $this->get('/en/projects/clareira')->assertOk();
        $this->get('/pt/anotacoes/primeiro-diario')->assertOk();
        $this->get('/en/notes/primeiro-diario')->assertOk();
        $this->get('/pt/sobre')->assertOk();
        $this->get('/en/about')->assertOk();

        // Segments from the other language don't exist.
        $this->get('/pt/projects')->assertNotFound();
        $this->get('/en/projetos')->assertNotFound();

        // The section was renamed from "Diário / Journal" before launch.
        $this->get('/pt/diario/primeiro-diario')->assertNotFound();
        $this->get('/en/journal/primeiro-diario')->assertNotFound();
    }

    public function test_language_switcher_points_to_the_same_page_in_the_other_language(): void
    {
        Project::factory()->published()->create(['slug' => 'clareira']);

        $this->get('/pt/projetos/clareira')->assertSee('href="'.url('/en/projects/clareira').'"', false);
        $this->get('/en/projects/clareira')->assertSee('href="'.url('/pt/projetos/clareira').'"', false);
    }

    public function test_home_shows_featured_project_on_the_pedestal_and_hides_drafts(): void
    {
        Project::factory()->published()->create(['title' => ['pt_BR' => 'Clareira', 'en' => 'Clearing'], 'is_featured' => true]);
        Project::factory()->create(['title' => ['pt_BR' => 'Rascunho secreto']]);
        Project::factory()->scheduled()->create(['title' => ['pt_BR' => 'Agendado secreto']]);

        $this->get('/pt')
            ->assertOk()
            ->assertSee('class="altar"', false)
            ->assertSee('Clareira')
            ->assertDontSee('Rascunho secreto')
            ->assertDontSee('Agendado secreto');

        $this->get('/en')->assertSee('Clearing');
    }

    public function test_unpublished_projects_and_posts_are_not_found(): void
    {
        Project::factory()->create(['slug' => 'rascunho']);
        Project::factory()->scheduled()->create(['slug' => 'agendado']);
        Note::factory()->create(['slug' => 'anotacao-rascunho']);
        Note::factory()->scheduled()->create(['slug' => 'anotacao-agendada']);

        $this->get('/pt/projetos/rascunho')->assertNotFound();
        $this->get('/pt/projetos/agendado')->assertNotFound();
        $this->get('/pt/anotacoes/anotacao-rascunho')->assertNotFound();
        $this->get('/pt/anotacoes/anotacao-agendada')->assertNotFound();
    }

    public function test_project_devlog_lists_only_its_published_posts_newest_first(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clareira']);
        Note::factory()->for($project)->create(['title' => ['pt_BR' => 'Entrada antiga'], 'published_at' => now()->subDays(10)]);
        Note::factory()->for($project)->create(['title' => ['pt_BR' => 'Entrada nova'], 'published_at' => now()->subDay()]);
        Note::factory()->for($project)->create(['title' => ['pt_BR' => 'Entrada rascunho']]);
        Note::factory()->published()->create(['title' => ['pt_BR' => 'De outro projeto']]);

        $this->get('/pt/projetos/clareira')
            ->assertOk()
            ->assertSeeInOrder(['Entrada nova', 'Entrada antiga'])
            ->assertDontSee('Entrada rascunho')
            ->assertDontSee('De outro projeto');
    }

    public function test_note_renders_every_block_type(): void
    {
        Note::factory()->published()->create([
            'slug' => 'blocos',
            'content' => ['pt_BR' => [
                ['type' => 'text', 'data' => ['body' => '<p>Texto <strong>forte</strong></p><script>alert(1)</script>']],
                ['type' => 'image', 'data' => ['image' => 'content/a.png', 'alt' => 'Esboço', 'caption' => 'Legenda da imagem', 'layout' => 'wide']],
                ['type' => 'gallery', 'data' => ['images' => ['content/b.png', 'content/c.png'], 'caption' => 'Legenda da galeria']],
                ['type' => 'video', 'data' => ['url' => 'https://youtu.be/dQw4w9WgXcQ', 'caption' => null]],
                ['type' => 'game', 'data' => ['url' => 'https://example.itch.io/embed/1', 'aspect_ratio' => '4:3', 'caption' => 'Build 0.3']],
                ['type' => 'quote', 'data' => ['text' => 'Tudo começa com uma ideia.', 'attribution' => 'Eu']],
                ['type' => 'unknown', 'data' => []],
            ]],
        ]);

        $this->get('/pt/anotacoes/blocos')
            ->assertOk()
            ->assertSee('<strong>forte</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('class="block-figure is-wide"', false)
            ->assertSee(url('/uploads/content/a.png'), false)
            ->assertSee('alt="Esboço"', false)
            ->assertSee(url('/uploads/content/c.png'), false)
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('data-game-src="https://example.itch.io/embed/1"', false)
            ->assertSee('--ratio: 4 / 3', false)
            ->assertSee('Tudo começa com uma ideia.');
    }

    public function test_game_block_rejects_non_https_urls(): void
    {
        Note::factory()->published()->create([
            'slug' => 'jogo',
            'content' => ['pt_BR' => [['type' => 'game', 'data' => ['url' => 'javascript:alert(1)']]]],
        ]);

        $this->get('/pt/anotacoes/jogo')->assertOk()->assertDontSee('data-game-src', false);
    }

    public function test_untranslated_notice_only_appears_when_english_is_missing(): void
    {
        Note::factory()->published()->create(['slug' => 'so-portugues', 'title' => ['pt_BR' => 'Só em português']]);
        Note::factory()->published()->create(['slug' => 'traduzido', 'title' => ['pt_BR' => 'Traduzido', 'en' => 'Translated']]);

        $this->get('/en/notes/so-portugues')
            ->assertSee('This note is only available in Portuguese.')
            ->assertSee('Só em português')
            ->assertSee('lang="pt-BR"', false);

        $this->get('/en/notes/traduzido')->assertDontSee('This note is only available in Portuguese.');
        $this->get('/pt/anotacoes/so-portugues')->assertDontSee('disponível só em português');
    }

    public function test_dates_are_shown_in_brasilia_time_and_the_active_language(): void
    {
        // 01:30 UTC on Sept 30 is still Sept 29 in Brasília (UTC-3).
        Note::factory()->create(['slug' => 'madrugada', 'published_at' => '2026-09-30 01:30:00']);
        $this->travelTo('2026-10-01 12:00:00');

        $this->get('/pt/anotacoes/madrugada')->assertSee('29 de setembro de 2026');
        $this->get('/en/notes/madrugada')->assertSee('September 29, 2026');
    }

    public function test_unknown_page_inside_a_locale_shows_a_localized_404(): void
    {
        $this->get('/en/nowhere')->assertNotFound()->assertSee('This trail got lost in the woods');
        $this->get('/pt/lugar-nenhum')->assertNotFound()->assertSee('Esta trilha se perdeu na mata');
    }

    public function test_notes_are_paginated(): void
    {
        Note::factory()->count(12)->published()->create();

        $this->get('/pt/anotacoes')->assertOk()->assertSee('rel="next"', false);
        $this->get('/pt/anotacoes?page=2')->assertOk()->assertSee('rel="prev"', false);
    }
}
