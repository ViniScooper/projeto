<!DOCTYPE html>
<html>
<head>
    <title>Meus Favoritos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .cat-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .cat-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .cat-card:hover {
            transform: translateY(-5px);
        }
        
        .cat-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 12px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .remove-btn:hover {
            background: #cc0000;
        }
        
        .loading {
            display: none;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; margin: 20px 0;">Meus Gatos Favoritos</h1>
    
    <div class="loading" id="loading"></div>
    <div class="cat-container" id="favoritesContainer"></div>

    <script>
        const API_BASE = '/api/favorites';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        async function loadFavorites() {
            showLoading(true);
            try {
                const response = await fetch(API_BASE);
                const data = await response.json();
                
                if (!data.success) throw new Error(data.message);
                
                const container = document.getElementById('favoritesContainer');
                // Atualize a geração do HTML para escapar os valores com JSON.stringify()
                container.innerHTML = data.data.map(fav => `
                    <div class="cat-card">
                        <img src="${fav.cat_url}" class="cat-image" alt="Gato favorito">
                        <button class="remove-btn" 
                                onclick="removeFavorite('${fav.cat_api_id}')"
                                data-cat-id="${fav.cat_api_id}">
                            ❌ Remover
                        </button>
                    </div>
                `).join('');
                
            } catch (error) {
                alert(error.message);
            } finally {
                showLoading(false);
            }
        }

        async function removeFavorite(catId) {
            try {
                const response = await fetch(`/favorite/${catId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const result = await response.json();
                
                if (result.status === 'removed') {
                    showToast(result.message);
                    // Remove todos os cards com o mesmo cat_api_id
                    document.querySelectorAll(`[data-cat-id="${catId}"]`).forEach(el => {
                        el.closest('.cat-card').remove();
                    });
                } else {
                    showToast('⚠️ ' + result.message, true);
                }
                
            } catch (error) {
                console.error('Erro na remoção:', error);
                showToast('⚠️ Falha ao remover. Tente novamente.', true);
            }
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = `toast ${isError ? 'error' : ''}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Carrega os favoritos ao iniciar
        document.addEventListener('DOMContentLoaded', loadFavorites);
    </script>
</body>
</html>
