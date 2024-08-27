<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
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

        $userId = User::all()->random()->id;
        return [
            'title' => fake()->randomElement([fake()->realText(rand(10, 50)), null]),
            'content' => fake()->paragraph(rand(1, 10)),
            'public' => true,
            'user_id' => $userId,
            'category_id' => 1,
            'status_id' => 1,
            'tag_id' => fake()->randomElement([Tag::where('user_id', $userId)->inRandomOrder()->first()->id, null, null]),
            'created_at' => fake()->dateTimeBetween('-3 year'),
            'updated_at' => fake()->dateTimeBetween('-3 year')
        ];
    }


    public function event(): static
    {
        return $this->state(function (array $attributes)  {
            $userId = User::all()->random()->id;
            $start = Carbon::parse(fake()->dateTimeBetween('now', '+4 month'));
            $end = $start->addHours(rand(0, 10));
            
            return [
                'title' => fake()->realText(rand(10, 30)),
                'content' => fake()->randomElement([fake()->paragraph(rand(1, 10)), null, null]),
                'user_id' => $userId,
                'category_id' => 3,
                'status_id' => 1,
                'starts_at' => $start,
                'ends_at' => $end,
                'tag_id' => fake()->randomElement([Tag::where('user_id', $userId)->inRandomOrder()->first()->id, null]),
            ];
        });
    }
}
