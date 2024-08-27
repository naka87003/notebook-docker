<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Note;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->call([
            CategorySeeder::class,
            StatusSeeder::class,
            TagSeeder::class,
            NoteSeeder::class
        ]);

        User::factory(50)->create();
        Tag::factory(1000)->create();
        Note::factory(1000)->create();
        Note::factory(500)->event()->create();
        Like::factory(2000)->create();
        Comment::factory(2500)->create();
        Reply::factory(2000)->create();
        Follow::factory(200)->create();
    }
}
