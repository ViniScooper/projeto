<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }

        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .profile-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        input:focus {
            border-color: var(--secondary-color);
            outline: none;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .favorites-section {
            margin-top: 3rem;
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .cat-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .cat-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .error {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .success-message {
            color: #27ae60;
            background: #e8f6ef;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="profile-container">
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-section">
            <div>
                <h2>Informações do Perfil</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf) }}" required>
                        @error('cpf')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Nova Senha (deixe em branco para manter a atual)</label>
                        <input type="password" id="password" name="password">
                        @error('password')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirme a Nova Senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation">
                    </div>

                    <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                </form>
            </div>

            <div class="favorites-section">
                <h2>Gatos Favoritos</h2>
                <a href="{{ route('favorites') }}" class="btn btn-primary">Ver Favoritos</a>
                <div class="cat-grid" id="favoritesContainer">
                    <!-- Favorites will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Carregar favoritos ao abrir a página
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/favorites')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('favoritesContainer');
                    container.innerHTML = data.data.map(fav => `
                        <div class="cat-card">
                            <img src="${fav.cat_url}" class="cat-img" alt="Gato favorito">
                            <button class="btn btn-danger" 
                                    onclick="deleteFavorite('${fav.cat_api_id}')"
                                    style="position: absolute; top: 10px; right: 10px;">
                                ❌ Remover
                            </button>
                        </div>
                    `).join('');
                });
        });

        function deleteFavorite(catId) {
            fetch(`/favorites/${catId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    location.reload(); // Recarrega a página para atualizar a lista
                }
            });
        }
    </script>
</body>
</html>