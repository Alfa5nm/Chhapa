<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Catalogue') · Chhapa</title>
    <link rel="stylesheet" href="{{ asset('chhapa.css') }}">
    <script src="{{ asset('catalogue-form.js') }}" defer></script>
</head>
<body>
<header class="masthead">
    <nav class="shell nav" aria-label="Main navigation">
        <a class="brand" href="{{ route('catalogue.index') }}">Chhapa<span>.</span></a>
        <div class="nav-links">
            <a href="{{ route('catalogue.index') }}">Browse catalogue</a>
            @auth
                @if (auth()->user()->managed_business_id)
                    <a href="{{ route('workspace.index') }}">Business workspace</a>
                @endif
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-button">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Sign in</a>
            @endauth
        </div>
    </nav>
</header>
<main class="shell main">
    @if (session('success'))
        <p class="notice" role="status">{{ session('success') }}</p>
    @endif
    @yield('content')
</main>
</body>
</html>
