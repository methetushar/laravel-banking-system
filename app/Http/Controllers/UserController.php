<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string',
            'account_type' => 'required|in:Individual,Business',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $data = [
            'name' => $request->input('name'),
            'account_type' => $request->input('account_type'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'balance' => $request->input('balance'),
        ];
        // Create a new user
        $user = User::create($data);

        return response()->json(['user' => $user]);
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log in the user
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            // Generate a personal access token
            $token = Auth::user()->createToken('auth-token')->plainTextToken;
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function user(Request $request)
    {
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }
}
