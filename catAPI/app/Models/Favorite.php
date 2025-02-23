<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favoritos'; // Garanta o nome correto da tabela
    
    protected $fillable = [
        'user_id',
        'cat_api_id', 
        'cat_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Chave estrangeira explícita
    }
}