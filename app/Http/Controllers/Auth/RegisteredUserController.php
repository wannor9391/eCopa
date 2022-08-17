<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Gred;
use App\Models\Jabatan;
use App\Models\Kampus;
use App\Models\KampusUsers;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $gred = Gred::all();
        $jabatan = Jabatan::all();
        $kampus = Kampus::all();
        return view('auth.register',compact('gred','jabatan','kampus'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // $gred=$request->gred;
        // echo $gred;
         //kiri nama input
        $request->validate([
            'staffid' => ['required','numeric', 'digits_between:6,7','unique:users,staff_id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'notel' => ['required', 'string', 'unique:users,no_tel'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        //kiri nama column,kanan nama input
        $user = User::create([
            'staff_id' => $request->staffid,
            'nama' => $request->name,
            'email' => $request->email,
            'no_tel' => $request->notel,
            'gred_id' => $request->gred,
            'peranan' => $request->peranan,
            'ptj_code' => $request->ptj,
            'password' => Hash::make($request->password),
            'status' => 0,
        ]);

        event(new Registered($user));

        $kampus=$request->kampus;

        foreach ($kampus as $v_kampus)
        {
            $kampus_user = new KampusUsers();
            $kampus_user->staff_id = $request->staffid;
            $kampus_user->kampus_id=$v_kampus;
            $kampus_user->save();
        }


        return redirect()->route('register')->with('status', 'Permohonan pendaftaran akaun berjaya dihantar.');
        // Auth::login($user);
        // return redirect(RouteServiceProvider::HOME);
    }
}
