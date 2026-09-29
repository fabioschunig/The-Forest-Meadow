<?php

namespace Tests\Feature;

use App\Enums\PublicationState;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishableTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_state_follows_published_at(): void
    {
        $this->assertSame(PublicationState::Draft, Post::factory()->make()->publicationState());
        $this->assertSame(PublicationState::Scheduled, Post::factory()->scheduled()->make()->publicationState());
        $this->assertSame(PublicationState::Published, Post::factory()->published()->make()->publicationState());
    }

    public function test_published_scope_only_returns_live_records(): void
    {
        Post::factory()->create();
        Post::factory()->scheduled()->create();
        $live = Post::factory()->published()->create();

        $this->assertSame([$live->id], Post::published()->pluck('id')->all());
    }

    public function test_scheduled_post_goes_live_without_any_job(): void
    {
        $post = Post::factory()->create(['published_at' => now()->addHour()]);

        $this->assertFalse(Post::published()->whereKey($post)->exists());

        $this->travel(2)->hours();

        $this->assertTrue(Post::published()->whereKey($post)->exists());
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
        $post = Post::factory()->create([
            'title' => ['pt_BR' => 'Diário de bordo'],
            'content' => ['pt_BR' => [['type' => 'quote', 'data' => ['text' => 'Olá']]]],
        ]);

        app()->setLocale('en');

        $this->assertSame('Diário de bordo', $post->title);
        $this->assertSame('Olá', $post->content[0]['data']['text']);
        $this->assertFalse($post->hasTranslation('title', 'en'));
    }
}
