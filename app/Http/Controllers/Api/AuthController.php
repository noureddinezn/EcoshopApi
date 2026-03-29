<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * ============================================================
 * AuthController - User Authentication
 * ============================================================
 *
 * Think of this as a SECURITY GUARD at the door!
 * It helps users:
 *   👤 Create a new account (register)
 *   🔓 Login with email and password
 *   🚪 Logout when they're done
 *
 * ============================================================
 */
class AuthController extends Controller
{
    /**
     * METHOD 1: Create a new user account
     * EASY EXPLANATION: "I want to join the store"
     *
     * URL: POST /api/register
     * SEND: { "name": "John", "email": "john@example.com", "password": "secret", "password_confirmation": "secret" }
     */
    public function register(Request $request)
    {
        // ✔️ CHECK: Is the data correct?
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],                    // Name is required
            'email' => ['required', 'email', 'unique:users,email'],         // Email must be unique
            'password' => ['required', 'confirmed', Password::defaults()],  // Password with rules
        ]);

        // 👤 CREATE NEW USER IN DATABASE
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // 🔐 GENERATE TOKEN (special key to stay logged in)
        $token = $user->createToken('auth_token')->plainTextToken;

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,  // Use this to make requests!
            ],
        ], 201);
    }

    /**
     * METHOD 2: Login to existing account
     * EASY EXPLANATION: "I have an account, let me in!"
     *
     * URL: POST /api/login
     * SEND: { "email": "john@example.com", "password": "secret" }
     */
    public function login(Request $request)
    {
        // ✔️ CHECK: Email and password provided?
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 🔍 FIND USER BY EMAIL
        $user = User::where('email', $validated['email'])->first();

        // 🔐 CHECK: Is password correct? (compare with hash)
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);  // 401 = Unauthorized
        }

        // 🔐 GENERATE TOKEN (you'll use this to stay logged in)
        $token = $user->createToken('auth_token')->plainTextToken;

        // ✅ SEND BACK SUCCESS WITH TOKEN
        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,  // Save this! Use it for next requests
            ],
        ], 200);
    }

    /**
     * METHOD 3: Logout (remove your token)
     * EASY EXPLANATION: "I'm leaving the store, remove my key"
     *
     * URL: POST /api/logout
     * REQUIRES: You must be logged in
     */
    public function logout(Request $request)
    {
        // 🚪 DELETE THE CURRENT TOKEN
        // This makes the token useless
        $request->user()->currentAccessToken()->delete();

        // ✅ SEND BACK SUCCESS
        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
