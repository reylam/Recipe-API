<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'energy' => 'required|numeric',
            'protein' => 'required|numeric',
            'ingredients' => 'required|numeric',
            'tips' => 'required',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnail->move(public_path() . "/images/$request->slug", "image.png");
        }

        Recipe::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'ingredients' => $request->ingredients,
            'method' => $request->method,
            'tips' => $request->tips,
            'energy' => $request->energy,
            'carbohydrate' => $request->carbohydrate,
            'protein' => $request->protein,
            'thumbnail' => "images/$request->slug/image.png",
            'user_id' => auth()->user()->id,
        ]);


        return response()->json([
            'message' => 'Recipe created succesfull'
        ]);
    }


    public function destroy($slug)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        if (auth()->user()->id !== $recipe->user_id) {
            return response()->json([
                "message" =>  "Forbidden access"
            ], 403);
        }

        if (!$recipe) {
            return response()->json([
                'message' => 'Recipe not found'
            ], 404);
        }

        $recipe->delete();

        return response()->json([
            'message' =>  "Recipe deleted successful"
        ]);
    }

    public function rating($slug, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5'
        ]);


        if ($validator->fails()) {
            return response()->json([
                "message" => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $recipe = Recipe::where('slug', $slug)->first();

        if ($recipe->user_id !== auth()->user()->id) {
            return response()->json([
                "message" =>  "You cannot rate your own recipe"
            ], 403);
        }

        foreach ($recipe->ratings as $rating) {
            if (auth()->user()->id === $rating->user_id) {
                return response()->json([
                    'message' => 'You have rated'
                ], 403);
            }
        }

        Rating::create([
            'recipe_id' => $recipe->id,
            'rating' => $request->rating,
            'user_id' => auth()->user()->id
        ]);
        return response()->json([
            'message' => 'Rating success'
        ]);
    }

    public function comment($slug, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                "message" => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $recipe = Recipe::where('slug', $slug)->first();

        Comment::create([
            'recipe_id' => $recipe->id,
            'user_id' => auth()->user()->id,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Comment success'
        ]);
    }
}
