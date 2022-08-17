<?php

namespace App\Http\Controllers;

use App\Models\Gred;
use App\Models\Jabatan;
use App\Models\Kampus;
use App\Models\KampusUsers;
use App\Models\Pertukaran;
use App\Models\Tindakan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class AdminController extends Controller
{
    public function AdminsenaraiPencegahan(){

        $tindakan_pencegahan=Tindakan::join('Pencegahan', 'Tindakan.id_pencegahan', '=', 'Pencegahan.id_pencegahan')
       ->get();

        return view('senarai_pencegahan',compact('tindakan_pencegahan'));
    }

    public function AdminsenaraiPembetulan(){

        $tindakan_pembetulan=Tindakan::join('Pembetulan', 'Tindakan.id_pembetulan', '=', 'Pembetulan.id_pembetulan')
        ->get();

        return view('senarai_pembetulan',compact('tindakan_pembetulan'));
    }

    public function MODAdminDashboard(){

        $pengguna_baru=User::where('users.status', 0)->count();

        $pengguna_berdaftar=User::where('users.status', 2)->orWhere('users.status', 3)->count();

        $pengguna_aktif=User::where('users.status', 2)->count();

        $pengguna_tak_aktif=User::where('users.status', 3)->count();

        $pertukaran=Pertukaran::whereNull('status')->count();

        $pertukaran_peranan=Pertukaran::whereNull('status')->whereNull('ptj_code_dipohon')->count();

        $pertukaran_jabatan=Pertukaran::whereNull('status')->whereNull('peranan_dipohon')->count();

        $pencegahan=Tindakan::whereNull('id_pembetulan')->where('status_permohonan','Belum Diproses')->count();

        $pembetulan=Tindakan::whereNull('id_pencegahan')->where('status_permohonan','Belum Diproses')->count();

        if ( auth()->user()->peranan == 'Moderator')
        {
            $dashboard = collect([$pengguna_baru, $pengguna_berdaftar, $pengguna_aktif, $pengguna_tak_aktif, $pertukaran_peranan, $pertukaran_jabatan ]);
        }
        elseif( auth()->user()->peranan == 'Admin')
        {
            $dashboard = collect([$pengguna_baru, $pengguna_berdaftar, $pengguna_aktif, $pertukaran, $pencegahan, $pembetulan ]);
        }

        return view('laman_utama',compact('dashboard'));


        //return view('laman_utama');
    }

    public function senaraiPermohonanPengguna(){

        $permohonan_user=User::join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->where('users.status', 0)
        ->get();
        return view('senarai_pengguna',compact('permohonan_user'));
    }

    public function senaraiPenggunaBerdaftar(){

        $permohonan_user=User::join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->where('users.peranan', '!=', 'Admin')
        ->where(function($query){
            $query->where('users.status', 2)
                  ->orwhere('users.status', 3); })
        ->get();
        return view('senarai_pengguna',compact('permohonan_user'));
    }

}
