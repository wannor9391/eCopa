<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->validate([

            'email' => ['required', 'email'],

            'password' => ['required'],
        ]);

        $attempt = Auth::attempt([

            'email' => $request->get('email'),

            'password' => $request->get('password'),

            'status' => 2
        ]);

        if ($attempt) {

            $request->session()->regenerate();
            if (auth()->user()->peranan == 'Pengguna')
            {
                return redirect()->route('PtjDashboard');
            }
            elseif (auth()->user()->peranan == 'Ketua Bahagian')
            {
                return redirect()->route('KBDashboard');
            }
            elseif(auth()->user()->peranan == 'Pegawai Kualiti')
            {
                return redirect()->route('PKDashboard');
            }
            elseif(auth()->user()->peranan == 'Moderator')
            {
                return redirect()->route('MODDashboard');
            }
            elseif(auth()->user()->peranan == 'Admin')
            {
                return redirect()->route('AdminDashboard');
            }
            //return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors([

            'email' => 'The provided credentials do not match our records.',

        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
