<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse{
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource{
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            throw new HttpResponseException(response([
                    'errors' => [
                        'message' =>[
                            'username or password wrong.'
                        ]
                    ]
                ], 401));
        }
        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function get(): UserResource{
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource{
        $data = $request->validated();

        $user = Auth::user();
        if(isset($data['email'])){
            $user->email = $data['email'];
        }

        if(isset($data['username'])){
            $user->username = $data['username'];
        }

        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse{
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'message'  => 'logged out'
        ])->setStatusCode(200);
    }

}
