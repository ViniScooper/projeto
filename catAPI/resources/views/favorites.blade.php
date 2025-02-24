<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meus Gatos Favoritos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f6fa;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .cat-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .cat-card {
            position: relative;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .cat-card:hover {
            transform: translateY(-5px);
        }

        .cat-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 3px solid #3498db;
        }

        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 0, 0, 0.9);
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: #ff4d4d;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .home-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .home-btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐱 Meus Gatos Favoritos</h1>
        <a href="{{ route('cats') }}" class="home-btn">Home</a>
    </div>

    <div class="cat-container" id="favoritesContainer">
        @foreach ($favorites as $favorite)
            <div class="cat-card">
                <img src="{{ $favorite->cat_url }}" class="cat-image" alt="Gato favorito">
                <button class="remove-btn" onclick="removeFavorite('{{ $favorite->cat_api_id }}')">❌ Remover</button>
            </div>
        @endforeach
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        async function removeFavorite(catId) {
            try {
                const response = await fetch(`/favorite/${catId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload(); // Recarrega a página para atualizar a lista
                } else {
                    alert('Erro ao remover o favorito.');
                }
            } catch (error) {
                console.error('Erro ao remover o favorito:', error);
            }
        }
    </script>
</body>
</html>
