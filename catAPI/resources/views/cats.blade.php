<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Galeria de Gatos - CatAPI</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }

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

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .breed-select {
            padding: 10px;
            min-width: 250px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
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
            border-bottom: 3px solid var(--secondary-color);
        }

        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 215, 0, 0.9);
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .favorite-btn:hover {
            background: #ffdf4d;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .favorites-section {
            margin-top: 50px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .loading-spinner {
            display: none;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .breed-info {
            padding: 15px;
            text-align: center;
        }

        .breed-name {
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            font-size: 0.95em;
        }

        .breed-origin {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .breed-temperament {
            color: #444;
            font-size: 0.8em;
            margin-top: 5px;
            font-style: italic;
        }

        .breed-description {
            color: #666;
            font-size: 0.8em;
            margin-top: 8px;
            line-height: 1.4;
        }

        .breed-weight {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .breed-life-span {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .breed-wiki {
            margin-top: 10px;
        }

        .breed-wiki a {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .breed-wiki a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐱 Galeria de Gatos Aleatórios</h1>
    </div>

    <div style="position: fixed; top: 20px; right: 20px; display: flex; gap: 10px;">
        <a href="{{ route('profile') }}" style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
            Acessar Perfil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Sair
            </button>
        </form>
    </div>

    <div class="controls">
        <select id="breedSelect" class="breed-select" onchange="fetchCats()">
            <option value="">-- Selecione uma raça --</option>
        </select>
    </div>

    <div class="loading-spinner" id="loading"></div>
    <div class="cat-container" id="catContainer"></div>


        <!-- 
        <div class="favorites-section">
            <h2>Gatos Favoritos</h2>
            <div class="cat-container" id="favoritesContainer"></div>
        </div>

 -->

    <script>
        const API_BASE = 'https://api.thecatapi.com/v1';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const API_KEY = 'live_X8eWcADPa53NDOmhjdMCGLzaO5tfL2fJzKPKandbhQtlfUWVHOXtjkaay2esBgpP';

        // Estado global para controle de favoritos
        let favorites = new Set();

        async function init() {
            await loadBreeds();
            await loadFavorites();
            fetchCats();
        }

        async function loadBreeds() {
            try {
                const response = await fetch(`${API_BASE}/breeds`, {
                    headers: {
                        'x-api-key': API_KEY
                    }
                });
                const breeds = await response.json();
                const select = document.getElementById('breedSelect');
                
                breeds.forEach(breed => {
                    const option = document.createElement('option');
                    option.value = breed.id;
                    option.textContent = breed.name;
                    select.appendChild(option);
                });
            } catch (error) {
              //  showError('Erro ao carregar raças');
            }
        }

        async function fetchCats() {
            showLoading(true);
            try {
                const breedId = document.getElementById('breedSelect').value;
                const url = breedId 
                    ? `${API_BASE}/images/search?limit=12&breed_ids=${breedId}&include_breeds=1`
                    : `${API_BASE}/images/search?limit=12&has_breeds=1&include_breeds=1`;

                const response = await fetch(url, {
                    headers: {
                        'x-api-key': API_KEY
                    }
                });
                const cats = await response.json();
                console.log('Dados da API:', cats); // Para debug
                displayCats(cats);
            } catch (error) {
               // showError('Erro ao carregar gatos');
            } finally {
                showLoading(false);
            }
        }

        function displayCats(cats) {
            const container = document.getElementById('catContainer');
            container.innerHTML = cats.map(cat => {
                // Acessa os dados de raça corretamente
                const breedInfo = cat.breeds?.[0] || {};
                console.log('Informações da raça:', breedInfo); // Para debug

                // Extrai informações com fallback
                const breedName = breedInfo.name || 'Gato Doméstico';
                const origin = breedInfo.origin ? `Origem: ${breedInfo.origin}` : '';
                const temperament = breedInfo.temperament ? `Temperamento: ${breedInfo.temperament}` : '';
                const description = breedInfo.description ? `Descrição: ${breedInfo.description}` : '';
                const weight = breedInfo.weight ? `Peso: ${breedInfo.weight.metric} kg` : '';
                const lifeSpan = breedInfo.life_span ? `Expectativa de vida: ${breedInfo.life_span} anos` : '';
                const wikiUrl = breedInfo.wikipedia_url ? `<a href="${breedInfo.wikipedia_url}" target="_blank">Mais informações</a>` : '';

                return `
                <div class="cat-card">
                    <img src="${cat.url}" class="cat-image" alt="${breedName}">
                    <div class="breed-info">
                        <p class="breed-name">${breedName}</p>
                        ${origin ? `<p class="breed-origin">${origin}</p>` : ''}
                        ${temperament ? `<p class="breed-temperament">${temperament}</p>` : ''}
                        ${description ? `<p class="breed-description">${description}</p>` : ''}
                        ${weight ? `<p class="breed-weight">${weight}</p>` : ''}
                        ${lifeSpan ? `<p class="breed-life-span">${lifeSpan}</p>` : ''}
                        ${wikiUrl ? `<p class="breed-wiki">${wikiUrl}</p>` : ''}
                    </div>
                    <button class="favorite-btn" data-cat-id="${cat.id}" data-cat-url="${cat.url}" onclick="toggleFavorite('${cat.id}', '${cat.url}')">
                        ${favorites.has(cat.id) ? '❤️ Remover' : '⭐ Favoritar'}
                    </button>
                </div>
                `;
            }).join('');
        }

        async function toggleFavorite(catId, catUrl) {
            try {
                const response = await fetch('/favorite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cat_id: catId,
                        cat_url: catUrl,
                        _token: document.querySelector('meta[name="csrf-token"]').content
                    })
                });

                const result = await response.json();
                
                if (result.status === 'added') {
                    alert('Adicionado aos favoritos!');
                    loadFavorites(); // Atualiza a lista de favoritos
                } else if (result.status === 'removed') {
                    alert('Removido dos favoritos!');
                    loadFavorites(); // Atualiza a lista de favoritos
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        }

        async function loadFavorites() {
            try {
                const response = await fetch('/favorites');
                const favoritesData = await response.json();
                console.log('Dados dos favoritos:', favoritesData); // Log de depuração

                const container = document.getElementById('favoritesContainer');
                container.innerHTML = favoritesData.map(fav => `
                    <div class="cat-card">
                        <img src="${fav.cat_url}" class="cat-image" alt="Gato favorito">
                        <button class="favorite-btn" 
                                onclick="toggleFavorite('${fav.cat_api_id}', '${fav.cat_url}')"
                                data-cat-id="${fav.cat_api_id}">
                            ❌ Remover
                        </button>
                    </div>
                `).join('');
            } catch (error) {
              //  console.error('Erro ao carregar favoritos:', error); // Log de depuração
               // showError('Erro ao carregar favoritos');
            }
        }

        // Funções auxiliares
        function updateFavoriteButtons(catId) {
            document.querySelectorAll(`[data-cat-id="${catId}"]`).forEach(btn => {
                btn.innerHTML = favorites.has(catId) ? '❤️ Remover' : '⭐ Favoritar';
                btn.setAttribute('aria-label', favorites.has(catId) 
                    ? 'Remover dos favoritos' 
                    : 'Adicionar aos favoritos');
            });
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function showError(message) {
            alert(message);
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.padding = '15px';
            toast.style.background = '#333';
            toast.style.color = 'white';
            toast.style.borderRadius = '5px';
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', init);
        document.addEventListener('DOMContentLoaded', loadFavorites);
    </script>
</body>
</html>