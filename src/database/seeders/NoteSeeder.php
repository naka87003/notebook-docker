<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::create([
            'title' => 'サウナ',
            'content' => "8:15 集合\r\n8:30 白玉温泉\r\n11:00 肉五郎（焼肉ランチ）\r\n14:00 解散",
            'starts_at' => '2024-08-11 08:15:00',
            'ends_at' => '2024-08-11 14:00:00',
            'public' => true,
            'has_image' => false,
            'user_id' => 1,
            'category_id' => 3,
            'status_id' => 1,
            'tag_id' => null,
            'created_at' => '2024-08-06 09:02:00',
            'updated_at' => '2024-08-06 09:02:00'
        ]);
        Note::create([
            'title' => '',
            'content' => '"Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type invalid as well."',
            'starts_at' => null,
            'ends_at' => null,
            'public' => true,
            'has_image' => false,
            'user_id' => 1,
            'category_id' => 1,
            'status_id' => 1,
            'tag_id' => 2,
            'created_at' => '2024-08-07 19:33:00',
            'updated_at' => '2024-08-07 19:33:00'
        ]);
        Note::create([
            'title' => '買い物',
            'content' => "豆腐\r\nパン\r\nヨーグルト",
            'starts_at' => null,
            'ends_at' => null,
            'public' => false,
            'has_image' => false,
            'user_id' => 1,
            'category_id' => 2,
            'status_id' => 1,
            'tag_id' => null,
            'created_at' => '2024-08-09 20:47:00',
            'updated_at' => '2024-08-09 20:47:00'
        ]);
        Note::create([
            'title' => 'toast',
            'content' => "不可算名詞\r\nHow many slices of toast do you have for breakfast?",
            'starts_at' => null,
            'ends_at' => null,
            'public' => false,
            'has_image' => false,
            'user_id' => 1,
            'category_id' => 1,
            'status_id' => 1,
            'tag_id' => 1,
            'created_at' => '2024-08-10 08:10:00',
            'updated_at' => '2024-08-10 08:10:00'
        ]);
    }
}
