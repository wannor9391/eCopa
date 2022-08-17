<?php

namespace App\Http\Controllers;

use App\Models\Gred;
use App\Models\Jabatan;
use App\Models\Kampus;
use App\Models\KampusUsers;
use App\Models\Pertukaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ModController extends Controller
{
    public function senaraiPermohonanPengguna(){

        $MOD_kampus=KampusUsers::where('staff_id',auth()->id())->first();

        $permohonan_user=User::join('kampus_users','kampus_users.staff_id','=','users.staff_id')
        ->join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->where('users.ptj_code',auth()->user()->ptj_code)
        ->where('kampus_users.kampus_id', $MOD_kampus->kampus_id)
        ->where('users.status', 0)
        ->where(function($query){
            $query->where('users.peranan', 'Pengguna')
                    ->orwhere('users.peranan', 'Ketua Bahagian')
                    ->orwhere('users.peranan', 'Pegawai Kualiti'); })
        ->get();



        return view('senarai_pengguna',compact('permohonan_user'));
    }

    public function senaraiPenggunaBerdaftar(){

        $MOD_kampus=KampusUsers::where('staff_id',auth()->id())->first();

        $permohonan_user=User::join('kampus_users','kampus_users.staff_id','=','users.staff_id')
        ->join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->where('users.ptj_code',auth()->user()->ptj_code)
        ->where('kampus_users.kampus_id', $MOD_kampus->kampus_id)
        ->where(function($query){
            $query->where('users.peranan', 'Pengguna')
                    ->orwhere('users.peranan', 'Ketua Bahagian')
                    ->orwhere('users.peranan', 'Pegawai Kualiti'); })
        ->where(function($query){
            $query->where('users.status', 2)
                  ->orwhere('users.status', 3); })
        ->get();
        return view('senarai_pengguna',compact('permohonan_user'));

    }


    public function updatePermohonanBaru($id){

        $pengguna = User::where('staff_id', $id)->first();

        if((Route::currentRouteNamed('updatePermohonanPenggunaBaruLulus')))
        {
            $pengguna->status = '2';
            $pengguna->save();
            if ( auth()->user()->peranan == 'Moderator')
            {
                return redirect('/MOD/pengguna/permohonan/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
            }
            elseif ( auth()->user()->peranan == 'Admin')
            {
                return redirect('/admin/pengguna/permohonan/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
            }
        }
        else if ((Route::currentRouteNamed('updatePermohonanPenggunaBaruTolak')))
        {
            $pengguna->status = '1';
            $pengguna->save();
            if ( auth()->user()->peranan == 'Moderator')
            {
                return redirect('/MOD/pengguna/permohonan/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
            }
            elseif ( auth()->user()->peranan == 'Admin')
            {
                return redirect('/admin/pengguna/permohonan/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
            }
        }
    }

    public function editPengguna($id){

        $permohonan_user=User::join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->join('gred','gred.gred_id','=','users.gred_id')
        ->where('users.staff_id', $id)
        ->first();

        $countuser_Kampus = KampusUsers::where('staff_id',$id)->count();

        if($countuser_Kampus==1)
        {
            $userKampus=KampusUsers::where('staff_id',$id)->first();
        }
        else
        {
            $userKampus=KampusUsers::where('staff_id',$id)->get();
        }


        $gred = Gred::all();
        $jabatan = Jabatan::all();
        $kampus = Kampus::all();

        return view('edit_pengguna',compact("permohonan_user","gred","jabatan","kampus","userKampus","countuser_Kampus"));
    }

    public function updatePenggunaBerdaftar(Request $request,$id){

        $pengguna=User::find($id);

        $pengguna->nama = $request->nama;

        $pengguna->email = $request->email;

        $pengguna->no_tel = $request->notel;

        $pengguna->gred_id = $request->gred;

        $pengguna->peranan = $request->peranan;

        $pengguna->ptj_code = $request->ptj;

        $pengguna->status = $request->status;

        $pengguna->save();

        $kampus=$request->kampus;

        KampusUsers::where('staff_id', $id)->delete();

        foreach ($kampus as $v_kampus)
        {
            $kampus_user = new KampusUsers();
            $kampus_user->staff_id =  $id;
            $kampus_user->kampus_id=$v_kampus;
            $kampus_user->save();
        }

        if ( auth()->user()->peranan == 'Moderator')
        {
            return redirect('/MOD/pengguna/berdaftar/edit/'.$id)->with('status', 'Maklumat berjaya disimpan.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/pengguna/berdaftar/edit/'.$id)->with('status', 'Maklumat berjaya disimpan.');
        }

    }

    public function senaraiPertukaranPeranan()
    {

        if ( auth()->user()->peranan == 'Moderator')
        {
            $MOD_kampus=KampusUsers::where('staff_id',auth()->id())->first();

            $pertukaran=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $MOD_kampus->kampus_id)
            ->whereNull('ptj_code_dipohon')
            ->whereNull('tarikh_lulus_tolak')
            ->select('pertukaran.id','pertukaran.staff_id','nama','peranan','peranan_dipohon')
            ->get();

        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            $pertukaran=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
            ->whereNull('ptj_code_dipohon')
            ->whereNull('tarikh_lulus_tolak')
            ->get();
        }

        return view('senarai_pertukaran',compact('pertukaran'));
    }

    public function editPertukaranPeranan($id){

        $permohonan_user=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
        ->join('gred','gred.gred_id','=','users.gred_id')
        ->join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->where('id',$id)
        ->first();

        // $userKampus=KampusUsers::where('staff_id',$permohonan_user->staff_id)
        // ->join('kampus','kampus.kampus_id','=','users.gred_id')
        // ->first();
        return view('edit_pertukaran',compact("permohonan_user"));
    }

    public function LulusPertukaranPeranan($id)
    {
        $pertukaran_peranan = Pertukaran::find($id);

        $pertukaran_peranan->tarikh_lulus_tolak=Carbon::now();

        $pertukaran_peranan->status='Y';

        $pertukaran_peranan->save();

        $pemohon = User::find($pertukaran_peranan->staff_id);

        $pemohon->peranan=$pertukaran_peranan->peranan_dipohon;

        $pemohon->save();

        if ( auth()->user()->peranan == 'Moderator')
        {
            return redirect('/MOD/pengguna/pertukaran/peranan/papar/'.$id)->with('status', 'Permohonan pertukaran peranan berjaya diluluskan.');
        }
        elseif( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/pengguna/pertukaran/peranan/papar/'.$id)->with('status', 'Permohonan pertukaran peranan berjaya diluluskan.');
        }


    }

    public function TolakPertukaranPeranan($id)
    {
        $pertukaran_peranan = Pertukaran::find($id);

        $pertukaran_peranan->tarikh_lulus_tolak=Carbon::now();

        $pertukaran_peranan->status='N';

        $pertukaran_peranan->save();

        if ( auth()->user()->peranan == 'Moderator')
        {
            return redirect('/MOD/pengguna/pertukaran/peranan/papar/'.$id)->with('status', 'Permohonan pertukaran peranan berjaya ditolak.');
        }
        elseif( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/pengguna/pertukaran/peranan/papar/'.$id)->with('status', 'Permohonan pertukaran peranan berjaya ditolak.');
        }


    }


    public function senaraiPertukaranPtj()
    {
        // $pertukaran=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
        // ->join('jabatan','jabatan.ptj_code','=','pertukaran.ptj_code_dipohon')
        // ->where('jabatan','jabatan.ptj_code','=','users.ptj_code_dipohon')
        // ->whereNull('peranan_dipohon')
        // ->whereNull('tarikh_lulus_tolak')
        // ->get();

        $pertukaran1=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
        ->join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->whereNull('peranan_dipohon')
        ->whereNull('tarikh_lulus_tolak')
        ->get();

        $pertukaran=Pertukaran::join('users','users.staff_id','=','pertukaran.staff_id')
        ->join('jabatan','jabatan.ptj_code','=','pertukaran.ptj_code_dipohon')
        ->whereNull('peranan_dipohon')
        ->whereNull('tarikh_lulus_tolak')
        ->union($pertukaran1)
        ->get();

        echo $pertukaran;


        // echo '/n';
        // echo $pertukaran2;
        //return view('senarai_pertukaran',compact('pertukaran'));
    }

}
