<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class CatController extends Controller
{
    public function favorite(Request $request)
    {
        $request->validate([
            'cat_id' => 'required|string',
            'cat_url' => 'required|string',
        ]);

        $favorite = Favorite::where('user_id', Auth::id())->where('cat_api_id', $request->cat_id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            $favorite = new Favorite();
            $favorite->user_id = Auth::id();
            $favorite->cat_api_id = $request->cat_id;
            $favorite->cat_url = $request->cat_url;
            $favorite->save();
            return response()->json(['status' => 'added']);
        }
    }

    public function deleteFavorite($catId)
    {
        $favorite = Favorite::where('user_id', Auth::id())->where('cat_api_id', $catId)->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 404);
    }

    public function showFavorites()
    {
        $favorites = Favorite::where('user_id', Auth::id())->get();
        return view('favorites', compact('favorites'));
    }

    public function getFavorites()
    {
        $favorites = Favorite::where('user_id', Auth::id())->get();
        return response()->json(['data' => $favorites]);
    }
}