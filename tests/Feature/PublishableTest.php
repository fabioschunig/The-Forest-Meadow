<?php

namespace Tests\Feature;

use App\Enums\PublicationState;
use App\Models\Note;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishableTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_state_follows_published_at(): void
    {
        $this->assertSame(PublicationState::Draft, Note::factory()->make()->publicationState());
        $this->assertSame(PublicationState::Scheduled, Note::factory()->scheduled()->make()->publicationState());
        $this->assertSame(PublicationState::Published, Note::factory()->published()->make()->publicationState());
    }

    public function test_published_scope_only_returns_live_records(): void
    {
        Note::factory()->create();
        Note::factory()->scheduled()->create();
        $live = Note::factory()->published()->create();

        $this->assertSame([$live->id], Note::published()->pluck('id')->all());
    }

    public function test_scheduled_post_goes_live_without_any_job(): void
    {
        $note = Note::factory()->create(['published_at' => now()->addHour()]);

        $this->assertFalse(Note::published()->whereKey($note)->exists());

        $this->travel(2)->hours();

        $this->assertTrue(Note::published()->whereKey($note)->exists());
    }

    public function test_where_publication_state_scope_filters_each_state(): void
    {
        $draft = Project::factory()->create();
        $scheduled = Project::factory()->scheduled()->create();
        $published = Project::factory()->published()->create();

        $this->assertSame([$draft->id], Project::wherePublicationState(PublicationState::Draft)->pluck('id')->all());
        $this->assertSame([$scheduled->id], Project::wherePublicationState(PublicationState::Scheduled)->pluck('id')->all());
        $this->assertSame([$published->id], Project::wherePublicationState(PublicationState::Published)->pluck('id')->all());
    }

    public function test_untranslated_content_falls_back_to_portuguese(): void
    {
        $note = Note::factory()->create([
            'title' => ['pt_BR' => 'Diário de bordo'],
            'content' => ['pt_BR' => [['type' => 'quote', 'data' => ['text' => 'Olá']]]],
        ]);

        app()->setLocale('en');

        $this->assertSame('Diário de bordo', $note->title);
        $this->assertSame('Olá', $note->content[0]['data']['text']);
        $this->assertFalse($note->hasTranslation('title', 'en'));
    }
}
