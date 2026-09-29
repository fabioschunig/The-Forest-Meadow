<?php

namespace Tests\Feature\Admin;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_admin_pages_render(): void
    {
        $project = Project::factory()->create();

        $this->get('/admin/projects')->assertOk();
        $this->get('/admin/projects/create')->assertOk();
        $this->get("/admin/projects/{$project->id}/edit")->assertOk();
    }

    public function test_list_shows_projects(): void
    {
        $projects = Project::factory()->count(2)->create();

        Livewire::test(ListProjects::class)->assertCanSeeTableRecords($projects);
    }

    public function test_creates_project(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Clareira',
                'slug' => 'clareira',
                'type' => ProjectType::Game->value,
                'status' => ProjectStatus::Prototype->value,
                'links' => [['label' => 'itch.io', 'url' => 'https://example.itch.io/clareira']],
                'published_at' => '2026-09-29 10:00:00',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::where('slug', 'clareira')->firstOrFail();

        $this->assertSame(ProjectType::Game, $project->type);
        $this->assertSame(ProjectStatus::Prototype, $project->status);
        $this->assertSame('https://example.itch.io/clareira', $project->links[0]['url']);
        // Typed in Brasília time, stored in UTC.
        $this->assertSame('2026-09-29 13:00:00', $project->getRawOriginal('published_at'));
    }

    public function test_type_and_status_are_required(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm(['title' => 'X', 'slug' => 'x', 'type' => null, 'status' => null])
            ->call('create')
            ->assertHasFormErrors(['type' => 'required', 'status' => 'required']);
    }
}
