<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'User',
        ]);

        $token =  uuid_create();

        $user->update([
            'token' =>  $token,
        ]);

        return response()->json([
            "message" => "Register success",
            "accessToken" => $token
        ]);
    }

    public function login(Request $request)
    {
        $user = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Username or password incorrect'
            ], 401);
        }

        $token = uuid_create();

        auth()->user()->update([
            'token' =>  $token
        ]);

        return response()->json([
            'message' => "Login success",
            "role" =>  auth()->user()->role,
            "accessToken" =>  $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = User::where('token', $request->bearerToken())->first();

        $user->delete([
            'token' => null
        ]);

        return response()->json([
            "message" => "Logout success"
        ]);
    }

    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'recipes' => $user->recipes->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'ingredients' => $recipe->ingredients,
                    'method' => $recipe->method,
                    'tips' => $recipe->tips,
                    'energy' => $recipe->energy . ' kcal',
                    'carbohydrate' => $recipe->carbohydrate . 'g',
                    'protein' => $recipe->protein . 'g',
                    'thumbnail' => asset($recipe->thumbnail),
                    'created_at' => $recipe->created_at,
                    'author' => $recipe->username,
                    'rating_avgs' => round($recipe->ratings()->avg('rating'), 2),
                    "category" => [
                        'id' => $recipe->category->id,
                        'name' => $recipe->category->name,
                        'slug' => $recipe->category->slug
                    ]
                ];
            }),
        ]);
    }
}
