<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create([
            'name' => 'English',
            'hex_color' => '#ffd700',
            'user_id' => 1
        ]);
        Tag::create([
            'name' => 'Vue.js',
            'hex_color' => '#42B883',
            'user_id' => 1
        ]);
        Tag::create([
            'name' => 'JavaScript',
            'hex_color' => '#fff300',
            'user_id' => 1
        ]);
        Tag::create([
            'name' => 'PHP',
            'hex_color' => '#7A86B8',
            'user_id' => 1
        ]);
        Tag::create([
            'name' => 'Work',
            'user_id' => 1
        ]);
    }
}
