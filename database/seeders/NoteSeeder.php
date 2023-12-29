<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $user = User::where('email', 'perlufye@gmail.com')->first();
        Note::create([
            'title' => 'Test note',
            'content' => 'Test content',
            'user_id' => $user->id
        ]);
        Note::create([
            'title' => 'Test note2',
            'content' => 'Test content2',
            'user_id' => $user->id
        ]);
        Note::create([
            'title' => 'Test note3',
            'content' => 'Test content3',
            'user_id' => $user->id
        ]);
    }
}
