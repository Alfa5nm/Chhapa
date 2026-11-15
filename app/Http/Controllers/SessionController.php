<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController
{
    public function create(): View
    {
        return view('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:72'],
        ]);

        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages(['email' => 'The email or password was incorrect.']);
        }

        $request->session()->regenerate();

        return $request->user()->managed_business_id
            ? redirect()->intended(route('workspace.index'))
            : to_route('catalogue.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('catalogue.index');
    }
}
