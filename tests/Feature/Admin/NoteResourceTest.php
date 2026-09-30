<?php

namespace Tests\Feature\Admin;

use App\Filament\Actions\CopyFromDefaultLocaleAction;
use App\Filament\Resources\Notes\Pages\CreateNote;
use App\Filament\Resources\Notes\Pages\EditNote;
use App\Filament\Resources\Notes\Pages\ListNotes;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\RelationManagers\NotesRelationManager;
use App\Models\Note;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NoteResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_admin_pages_render(): void
    {
        $note = Note::factory()->create();

        $this->get('/admin/notes')->assertOk();
        $this->get('/admin/notes/create')->assertOk();
        $this->get("/admin/notes/{$note->id}/edit")->assertOk();
    }

    public function test_list_shows_notes(): void
    {
        $notes = Note::factory()->count(2)->create();

        Livewire::test(ListNotes::class)->assertCanSeeTableRecords($notes);
    }

    public function test_creates_note_in_portuguese_with_blocks(): void
    {
        Livewire::test(CreateNote::class)
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

        $note = Note::where('slug', 'primeiro-diario')->firstOrFail();

        $this->assertSame('Primeiro diário', $note->getTranslation('title', 'pt_BR'));
        $this->assertSame('Tudo começa com uma ideia.', $note->getTranslation('content', 'pt_BR')[0]['data']['text']);
        $this->assertNull($note->published_at);
    }

    public function test_slug_follows_portuguese_title_on_create(): void
    {
        Livewire::test(CreateNote::class)
            ->fillForm(['title' => 'Ação e coração'])
            ->assertSchemaStateSet(['slug' => 'acao-e-coracao']);
    }

    public function test_slug_must_be_unique_and_well_formed(): void
    {
        Note::factory()->create(['slug' => 'ja-existe']);

        Livewire::test(CreateNote::class)
            ->fillForm(['title' => 'X', 'slug' => 'ja-existe'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);

        Livewire::test(CreateNote::class)
            ->fillForm(['title' => 'X', 'slug' => 'Com Espaço'])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'regex']);
    }

    public function test_copy_from_portuguese_fills_english_and_saves(): void
    {
        // Switching locale validates the current form, and the image block needs its file.
        Storage::fake('uploads');
        Storage::disk('uploads')->put('content/esboco.png', 'image');

        $note = Note::factory()->create([
            'title' => ['pt_BR' => 'Esboços da floresta'],
            'excerpt' => ['pt_BR' => 'Primeiros esboços.'],
            'content' => ['pt_BR' => [
                ['type' => 'image', 'data' => ['image' => 'content/esboco.png', 'alt' => 'Esboço', 'caption' => null, 'layout' => 'wide']],
            ]],
        ]);

        Livewire::test(EditNote::class, ['record' => $note->getRouteKey()])
            ->assertActionHidden(CopyFromDefaultLocaleAction::getDefaultName())
            ->set('activeLocale', 'en')
            ->assertSchemaStateSet(['title' => ''])
            ->assertActionVisible(CopyFromDefaultLocaleAction::getDefaultName())
            ->callAction(CopyFromDefaultLocaleAction::getDefaultName())
            ->assertSchemaStateSet(['title' => 'Esboços da floresta', 'excerpt' => 'Primeiros esboços.'])
            ->fillForm(['title' => 'Forest sketches'])
            ->call('save')
            ->assertHasNoFormErrors();

        $note->refresh();

        $this->assertSame('Forest sketches', $note->getTranslation('title', 'en'));
        $this->assertSame('Esboços da floresta', $note->getTranslation('title', 'pt_BR'));
        // The image is reused by path: no second upload for the translation.
        $this->assertSame('content/esboco.png', $note->getTranslation('content', 'en')[0]['data']['image']);
    }

    public function test_devlog_lists_project_notes_and_prefills_project_on_create(): void
    {
        $project = Project::factory()->create();
        $devlog = Note::factory()->count(2)->for($project)->create();
        $other = Note::factory()->create();

        // The page always hands its active locale down to the relation manager.
        Livewire::test(NotesRelationManager::class, ['ownerRecord' => $project, 'pageClass' => EditProject::class, 'activeLocale' => 'pt_BR'])
            ->assertCanSeeTableRecords($devlog)
            ->assertCanNotSeeTableRecords([$other]);

        Livewire::withQueryParams(['project' => $project->id])
            ->test(CreateNote::class)
            ->assertSchemaStateSet(['project_id' => $project->id]);
    }
}
