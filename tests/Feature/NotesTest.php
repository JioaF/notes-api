<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Note;
use Database\Seeders\NoteSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function testNoteCreateSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/notes', [
            'title' => 'Test note',
            'content' => 'Test content'
        ],[
            'Authorization' => 'test'
        ])->assertStatus(201)->assertJson([
            'data' => [
                'title' => 'Test note',
                'content' => 'Test content'
            ]
        ]);
    }

    public function testNoteCreateFail(): void{
        $this->seed(UserSeeder::class);

        $this->post('/api/notes', [
            'title' => '',
            'content' => ''
        ],[
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' =>[
                'title' => [
                    'The title field is required.'
                ],
                'content' =>[
                    'The content field is required.'
                ]
            ]
        ]);
    }

    public function testCreateUnauthorize(){
        $this->seed(UserSeeder::class);

        $this->post('/api/notes', [
            'title' => 'Test note',
            'content' => 'Test content'
        ],[
            'Authorization' => 'wrong'
        ])->assertStatus(401)->assertJson([
            'errors' =>[
                'message' =>[
                    'Unauthorized'
                ]
            ]
        ]);
    }

    public function testGetNoteSuccess(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->get('/api/notes/'.$note->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' =>[
                'title' => 'Test note',
                'content' => 'Test content'
            ]
        ]);
    }

    public function testGetNoteNotFound(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->get('/api/notes/'.($note->id+3), [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'errors' =>[
                'message' => [
                    'Not found'
                ]
            ]
        ]);

    }

    public function testGetOtherUserNote(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->get('/api/notes/'.($note->id+1), [
            'Authorization' => 'test2'
        ])->assertStatus(404)->assertJson([
            'errors' =>[
                'message' => [
                    'Not found'
                ]
            ]
        ]);
    }

    public function testUpdateNoteSuccess(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->put('/api/notes/'.$note->id, [
            'title' => 'Meeting Note',
            'content' => 'Discuss project timeline'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' =>[
                'title' => 'Meeting Note',
                'content' => 'Discuss project timeline'
            ]
        ]);
    }

    public function testUpdateValidationError(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->put('/api/notes/'.$note->id, [
            'title' => '',
            'content' => ''
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' =>[
                'title' => [
                    'The title field is required.'
                ],
                'content' => [
                    'The content field is required.'
                ]
            ]
        ]);
    }

    public function testDeleteNoteSuccess(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->delete('/api/notes/'.$note->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => true
        ]);
    }

    public function testDeleteNoteNotFound(){
        $this->seed([NoteSeeder::class]);

        $note = Note::query()->limit(1)->first();

        $this->delete('/api/notes/'.($note->id + 3), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'Not found'
                ]
            ]
        ]);
    }

    public function testGetAllUserNoteSuccess(){
        $this->seed([NoteSeeder::class]);

        $note = Note::get();

        $this->get('/api/notes', [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'notes' => []
        ]);
    }
}
