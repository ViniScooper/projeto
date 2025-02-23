<div style="position: fixed; top: 20px; right: 20px; display: flex; gap: 10px;">
    <a href="{{ route('profile') }}" style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
        Meu Perfil
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Sair
        </button>
    </form>
</div>