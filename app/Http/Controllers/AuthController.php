<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // new user register
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create default preferences
        $user->preferences()->create([
            'preferred_sources' => [],
            'preferred_categories' => [],
            'preferred_authors' => [],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $response  =  [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ];
        return $this->successResponse($response, 201, "Registration successful");
    }

    //user login
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse(422, "The provided credentials are incorrect.");
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $response  =  [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ];

        return $this->successResponse($response, 201, 'Login successful');
    }

    //user logout
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->showMessage('Login successful', 200);
    }

    //get uuser by token
    public function user(Request $request): JsonResponse
    {
        $user = [
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ];
        return $this->successResponse($user, 200);
    }
}
