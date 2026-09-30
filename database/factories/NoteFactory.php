<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'project_id' => null,
            'slug' => Str::slug($title),
            'title' => ['pt_BR' => $title, 'en' => $title],
            'excerpt' => ['pt_BR' => fake()->sentence(), 'en' => fake()->sentence()],
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
