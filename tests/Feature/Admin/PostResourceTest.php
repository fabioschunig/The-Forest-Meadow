<?php

namespace Tests\Feature\Admin;

use App\Filament\Actions\CopyFromDefaultLocaleAction;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\RelationManagers\PostsRelationManager;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_admin_pages_render(): void
    {
        $post = Post::factory()->create();

        $this->get('/admin/posts')->assertOk();
        $this->get('/admin/posts/create')->assertOk();
        $this->get("/admin/posts/{$post->id}/edit")->assertOk();
    }

    public function test_list_shows_posts(): void
    {
        $posts = Post::factory()->count(2)->create();

        Livewire::test(ListPosts::class)->assertCanSeeTableRecords($posts);
    }

    public function test_creates_post_in_portuguese_with_blocks(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Primeiro diário',
                'slug' => 'primeiro-diario',
                'excerpt' => 'Começando.',
                'content' => [
                    ['type' => 'quote', 'data' => ['text' => 'Tudo começa com uma ideia.', 'attribution' => 'Eu']],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::where('slug', 'primeiro-diario')->firstOrFail();

        $this->assertSame('Primeiro diário', $post->getTranslation('title', 'pt_BR'));
        $this->assertSame('Tudo começa com uma ideia.', $post->getTranslation('content', 'pt_BR')[0]['data']['text']);
        $this->assertNull($post->published_at);
    }

    public function test_slug_follows_portuguese_title_on_create(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'Ação e coração'])
            ->assertSchemaStateSet(['slug' => 'acao-e-coracao']);
    }

    public function test_slug_must_be_unique_and_well_formed(): void
    {
        Post::factory()->create(['slug' => 'ja-existe']);

        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'X', 'slug' => 'ja-existe'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);

        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'X', 'slug' => 'Com Espaço'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'regex']);
    }

    public function test_copy_from_portuguese_fills_english_and_saves(): void
    {
        // Switching locale validates the current form, and the image block needs its file.
        Storage::fake('uploads');
        Storage::disk('uploads')->put('content/esboco.png', 'image');

        $post = Post::factory()->create([
            'title' => ['pt_BR' => 'Esboços da floresta'],
            'excerpt' => ['pt_BR' => 'Primeiros esboços.'],
            'content' => ['pt_BR' => [
                ['type' => 'image', 'data' => ['image' => 'content/esboco.png', 'alt' => 'Esboço', 'caption' => null, 'layout' => 'wide']],
            ]],
        ]);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->assertActionHidden(CopyFromDefaultLocaleAction::getDefaultName())
            ->set('activeLocale', 'en')
            ->assertSchemaStateSet(['title' => ''])
            ->assertActionVisible(CopyFromDefaultLocaleAction::getDefaultName())
            ->callAction(CopyFromDefaultLocaleAction::getDefaultName())
            ->assertSchemaStateSet(['title' => 'Esboços da floresta', 'excerpt' => 'Primeiros esboços.'])
            ->fillForm(['title' => 'Forest sketches'])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();

        $this->assertSame('Forest sketches', $post->getTranslation('title', 'en'));
        $this->assertSame('Esboços da floresta', $post->getTranslation('title', 'pt_BR'));
        // The image is reused by path: no second upload for the translation.
        $this->assertSame('content/esboco.png', $post->getTranslation('content', 'en')[0]['data']['image']);
    }

    public function test_devlog_lists_project_posts_and_prefills_project_on_create(): void
    {
        $project = Project::factory()->create();
        $devlog = Post::factory()->count(2)->for($project)->create();
        $other = Post::factory()->create();

        // The page always hands its active locale down to the relation manager.
        Livewire::test(PostsRelationManager::class, ['ownerRecord' => $project, 'pageClass' => EditProject::class, 'activeLocale' => 'pt_BR'])
            ->assertCanSeeTableRecords($devlog)
            ->assertCanNotSeeTableRecords([$other]);

        Livewire::withQueryParams(['project' => $project->id])
            ->test(CreatePost::class)
            ->assertSchemaStateSet(['project_id' => $project->id]);
    }
}
