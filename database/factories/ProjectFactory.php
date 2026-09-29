<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'type' => fake()->randomElement(ProjectType::cases()),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'title' => ['pt_BR' => $title, 'en' => $title],
            'summary' => ['pt_BR' => fake()->sentence(), 'en' => fake()->sentence()],
            'content' => ['pt_BR' => [], 'en' => []],
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subDay()]);
    }

    public function scheduled(): static
    {
        return $this->state(['published_at' => now()->addDay()]);
    }
}
