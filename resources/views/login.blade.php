@extends('layout')
@section('title', 'Sign in')
@section('content')
<section class="panel narrow">
    <h1>Sign in</h1>
    <form class="stack" method="post" action="{{ route('login') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required></label>
        @error('email') <p class="error" role="alert">{{ $message }}</p> @enderror
        <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
        @error('password') <p class="error" role="alert">{{ $message }}</p> @enderror
        <button class="button">Sign in</button>
    </form>
</section>
@endsection
