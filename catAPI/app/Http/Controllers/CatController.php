<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class CatController extends Controller
{
    public function showFavorites()
    {
        return view('favorites');
    }

    public function getFavorites()
    {
        try {
            $favorites = Auth::user()->favorites()
                ->select('id', 'cat_api_id', 'cat_url', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $favorites
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar favoritos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar favoritos'
            ], 500);
        }
    }

    public function favorite(Request $request)
    {
        try {
            $request->validate([
                'cat_id' => 'required|string',
                'cat_url' => 'required|url'
            ]);

            Favorite::create([
                'user_id' => Auth::id(),
                'cat_api_id' => $request->cat_id,
                'cat_url' => $request->cat_url
            ]);

            return response()->json([
                'status' => 'added',
                'message' => '⭐ Favorito adicionado com sucesso!',
                'cat_id' => $request->cat_id
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro no favorito: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFavorite($catId)
    {
        try {
            $favorite = Favorite::where('user_id', Auth::id())
                ->where('cat_api_id', $catId)
                ->firstOrFail();

            $favorite->delete();
            
            return response()->json([
                'status' => 'removed',
                'message' => '✅ Favorito removido!',
                'cat_id' => $catId
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao remover favorito: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Favorito não encontrado ou já removido'
            ], 404);
        }
    }
}