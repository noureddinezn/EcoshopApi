<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * ============================================================
 * ProfileController - User Profile Management
 * ============================================================
 *
 * Think of this as your PROFILE PAGE!
 * It helps you:
 *   👤 See your own user information
 *
 * ============================================================
 */
class ProfileController extends Controller
{
    /**
     * METHOD 1: Get my profile information
     * EASY EXPLANATION: "Show me my account details"
     *
     * URL: GET /api/profile
     * REQUIRES: You must be logged in
     */
    public function show(Request $request)
    {
        // 👤 GET THE LOGGED-IN USER
        // This is you! Your name, email, role, everything
        $me = $request->user();

        // ✅ SEND BACK YOUR INFO
        return response()->json([
            'message' => 'Profile retrieved successfully',
            'data' => $me,
        ], 200);
    }
}
