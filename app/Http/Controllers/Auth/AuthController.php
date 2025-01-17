<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new UserResource(Auth::user());
    }
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $user = User::where('email', $data['email'])->first();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if(!Auth::attempt($credentials)) {

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials'
            ]);
           
        }

        session()->regenerate();
        $token = $user->createToken('auth_token')->plainTextToken;
        $parts = explode('|', $token);
        $token = $parts[1];

        return response()->json([
            'success' => true,
            'message' => '로그인완료',
            'token' => $token,
            'user' => Auth::user()
        ], 200);

    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'username' => 'required|string|max:16|unique:users',
            'phone' => 'required|numeric|digits_between:10,11',
            'name' => 'required|max:255',
            'birth' => 'required|numeric|digits:8',
            'birth_type' => 'numeric|nullable',
            'address' => 'nullable|max:255',
            'detail_address' => 'nullable|max:255',
            'zip_code' => 'nullable|numeric|digits:5',
            'email' => 'required|string|email|max:255|unique:users',
            'homepage' => 'nullable|max:255',
            'officers' => 'numeric|nullable',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '가입 완료',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
