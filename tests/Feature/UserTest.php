<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testRegisterUserSuccess(){
        $this->post('/api/users/register', [
            'email' => 'perlufye@gmail.com',
            'username' => 'perlufye',
            'password' => 'asdfjklgh',
        ])->assertStatus(201)->assertJson([
                "data" => [
                    'email' => 'perlufye@gmail.com',
                    'username' => 'perlufye'
                ]
            ]
        );
    }

    public function testRegisterUserFailed(){
        $this->post('/api/users/register', [
            'email' => '',
            'username' => '',
            'password' => '',
        ])->assertStatus(400)->assertJson([
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ],
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]
        );
    }

    public function testRegisterUserEmailAlreadyRegistered(){
        $this->testRegisterUserSuccess();
        $this->post('/api/users/register', [
            'email' => 'perlufye@gmail.com',
            'username' => 'perlufye',
            'password' => 'asdfjklgh',
        ])->assertStatus(400)->assertJson([
                'errors' => [
                    'email' =>[
                        'The email has already been taken.'
                    ]
                ]
            ]
        );
    }

    public function testUserLoginSuccess(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'perlufye@gmail.com',
            'password' => 'asdfjklgh',
        ])->assertStatus(200)->assertJson([
            'data'=>[
                'email' => 'perlufye@gmail.com',
                'username' => 'perlufye'
            ]
        ]);
        $user = User::where('email', 'perlufye@gmail.com')->first();
        self::assertNotNull($user->token);
    }

    public function testUserLoginFail(){
        $this->post('/api/users/login', [
            'email' => 'perlufye@gmail.com',
            'password' => 'asdfjklfs',
        ])->assertStatus(401)->assertJson([
            'errors'=>[
                'message' =>[
                    'username or password wrong.'
                ]
            ]
        ]);
    }

    public function testUserGetSuccess(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users', [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'email' => 'perlufye@gmail.com',
                'username' => 'perlufye'
            ]
        ]);
    }

    public function testUserGetUnautorized(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users')->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Unauthorized'
                ]
            ]
        ]);
    }

    public function testUserInvalidToken(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users', [
            'Authorization' => 'wrong'
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Unauthorized'
                ]
            ]
        ]);
    }

    public function testUserUpdateUsernameSuccess(){
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('email', 'perlufye@gmail.com')->first();
        $this->patch('/api/users', [
            'username' => 'paerlufye'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'email' => 'perlufye@gmail.com',
                'username' => 'paerlufye',
            ]
        ]);

        $newUser = User::where('email', 'perlufye@gmail.com')->first();
        self::assertNotEquals($newUser, $oldUser);
    }

    public function testUserUpdatePasswordSuccess(){
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('email', 'perlufye@gmail.com')->first();
        $this->patch('/api/users', [
            'password' => 'quieresion'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'email' => 'perlufye@gmail.com',
                'username' => 'perlufye',
            ]
        ]);

        $newUser = User::where('email', 'perlufye@gmail.com')->first();
        self::assertNotEquals($newUser, $oldUser);
    }

    public function testUserUpdateUsernameFail(){
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users', [
            'username' => 'per'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'username' => [
                    'The username field must be at least 5 characters.'
                ]
            ]
        ]);
    }

    public function testUserUpdatePasswordFail(){
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users', [
            'password' => 'quieres'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'password' => [
                    'The password field must be at least 8 characters.'
                ]
            ]
        ]);
    }

    public function testUserLogoutSuccess(){
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => true
        ]);
    }

    public function testUserLogoutFail(){
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'wrong'
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Unauthorized'
                ]
            ]
        ]);
    }
}
