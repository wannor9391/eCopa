<?php

namespace App\Http\Controllers;

use App\Models\Gred;
use App\Models\Jabatan;
use App\Models\Kampus;
use App\Models\KampusUsers;
use App\Models\Pembetulan;
use App\Models\Pencegahan;
use App\Models\Pertukaran;
use App\Models\TempTindakan;
use App\Models\Tindakan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PtjController extends Controller
{
    public function PtjDashboard(){

        $belumDiproses=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_permohonan','Belum Diproses')
        ->count();

        $Lulus=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_permohonan','Lulus')
        ->count();

        $Tolak=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_permohonan','Tolak')
        ->count();

        $dalamTindakan=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_tindakan','Dalam Tindakan')
        ->count();

        $Selesai=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_tindakan','Selesai')
        ->count();

        $tidakDilaksanakan=Tindakan::where('dipohon_oleh',auth()->id())
        ->where('status_tindakan','Tidak Dilaksanakan')
        ->count();

        $dashboard = collect([$belumDiproses, $Lulus, $Tolak, $dalamTindakan, $Selesai, $tidakDilaksanakan ]);


        return view('laman_utama',compact('dashboard'));
    }

    public function editMaklumatPeribadi($id){

        $permohonan_user=User::join('jabatan','jabatan.ptj_code','=','users.ptj_code')
        ->join('gred','gred.gred_id','=','users.gred_id')
        ->where('users.staff_id', $id)
        ->first();


        $gred = Gred::all();
        $jabatan = Jabatan::all();
        $kampus = Kampus::all();

        $countuser_Kampus = KampusUsers::where('staff_id',$id)->count();

        if($countuser_Kampus==1)
        {
            $userKampus=KampusUsers::where('staff_id',$id)->first();
        }
        else
        {
            $userKampus=KampusUsers::where('staff_id',$id)->get();
        }
        return view('edit_pengguna',compact("permohonan_user","gred","jabatan","userKampus","kampus","countuser_Kampus"));
    }

    public function updateMaklumatPeribadi(Request $request,$id){

        $pengguna=User::find($id);

        $pengguna->nama = $request->nama;

        $pengguna->email = $request->email;

        $pengguna->no_tel = $request->notel;

        $pengguna->gred_id = $request->gred;

        $pengguna->save();

        return redirect('/pengguna/maklumat_peribadi/edit/'.$id)->with('status', 'Maklumat berjaya disimpan.');
    }

    public function create(){
        return view('ptj.form_mohon_pencegahan');
    }

    public function storePertukaranPeranan(Request $request)
    {

        $pertukaran = new Pertukaran();

        $pertukaran->peranan_dipohon = $request->peranan;

        $pertukaran->staff_id = auth()->id();

        $pertukaran->tarikh_mohon = Carbon::now();

        $pertukaran->save();

        return redirect('/pengguna/maklumat_peribadi/edit/'.auth()->id())->with('status', 'Permohonan pertukaran peranan berjaya dihantar.');

    }


    public function storePertukaranPtj(Request $request)
    {

        $pertukaran = new Pertukaran();

        $pertukaran->ptj_code_dipohon = $request->ptj;

        $pertukaran->staff_id = auth()->id();

        $pertukaran->tarikh_mohon = Carbon::now();

        $pertukaran->save();

        return redirect('/pengguna/maklumat_peribadi/edit/'.auth()->id())->with('status', 'Permohonan pertukaran jabatan berjaya dihantar.');

    }

    public function storeSimpan(Request $request){

        $TempTindakan = new TempTindakan();

        $TempTindakan->tahap_risiko = $request->tahap_risiko;

        $TempTindakan->punca_isu = $request->punca_isu;

        $TempTindakan->keterangan = $request->keterangan;

        $TempTindakan->kawalan_sedia_ada = $request->kawalan;

        $TempTindakan->cadangan_tindakan = $request->cadangan_pencegahan;

        $TempTindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $TempTindakan->dipohon_oleh = auth()->id();

        $TempTindakan->save();

        // Generating Redirects...
        return redirect()->route('create')->with('status', 'Permohonan berjaya disimpan.');
    }

    public function storeHantar(Request $request){

        //insert dalam table Pencegahan

        $Pencegahan = new Pencegahan();

        $Pencegahan->tahap_risiko = $request->tahap_risiko;

        $Pencegahan->punca_isu = $request->punca_isu;

        $Pencegahan->keterangan = $request->keterangan;

        $Pencegahan->kawalan_sedia_ada = $request->kawalan;

        $Pencegahan->save();

        //get latest inserted id into Pencegahan

        $LastInsertedID=$Pencegahan->id_pencegahan;

        //insert dalam table Tindakan

        $Tindakan = new Tindakan();

        $Tindakan->status_permohonan = 'Belum Diproses';

        $Tindakan->cadangan_tindakan = $request->cadangan_pencegahan;

        $Tindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $Tindakan->dipohon_oleh = auth()->id();

        $Tindakan->id_pencegahan = $LastInsertedID;

        $Tindakan->save();

        // Generating Redirects...
        return redirect()->route('create')->with('status', 'Permohonan berjaya dihantar.');

    }

    public function senaraiPencegahan(){

        //ambek semua dalam pencegahan and yg match je dari tindakan

        $tindakan_pencegahan = Tindakan::where('dipohon_oleh', auth()->id())
        ->rightJoin('Pencegahan', 'Tindakan.id_pencegahan', '=', 'Pencegahan.id_pencegahan')
        ->get();

        $temp_tindakan = TempTindakan::where('dipohon_oleh', auth()->id())
        ->whereNotNull('tahap_risiko')->get();

        return view('senarai_pencegahan',compact('tindakan_pencegahan'),compact('temp_tindakan'));
    }

    public function editPencegahanSimpan($id){
        $TempTindakan = TempTindakan::find($id);
        return view('ptj.edit_pencegahan_simpan', ['TempTindakan' => $TempTindakan]);
    }

    public function UpdatePencegahanSimpan(Request $request,$id){

        //update data pencegahan yg disimpan(belum dihantar)

        $TempTindakan = TempTindakan::find($id);

        $TempTindakan->tahap_risiko = $request->tahap_risiko;

        $TempTindakan->punca_isu = $request->punca_isu;

        $TempTindakan->keterangan = $request->keterangan;

        $TempTindakan->kawalan_sedia_ada = $request->kawalan;

        $TempTindakan->cadangan_tindakan = $request->cadangan_pencegahan;

        $TempTindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $TempTindakan->save();

        // Generating Redirects...
        return redirect('/pencegahan/simpan/edit/'.$id)->with('status', 'Permohonan berjaya disimpan.');

    }

    public function UpdatePencegahanHantar(Request $request,$id){

        //insert dalam table Pencegahan

        $Pencegahan = new Pencegahan();

        $Pencegahan->tahap_risiko = $request->tahap_risiko;

        $Pencegahan->punca_isu = $request->punca_isu;

        $Pencegahan->keterangan = $request->keterangan;

        $Pencegahan->kawalan_sedia_ada = $request->kawalan;

        $Pencegahan->save();

        //get latest inserted id into Pencegahan

        $LastInsertedID=$Pencegahan->id_pencegahan;

        //insert dalam table Tindakan

        $Tindakan = new Tindakan();

        $Tindakan->status_permohonan = 'Belum Diproses';

        $Tindakan->cadangan_tindakan = $request->cadangan_pencegahan;

        $Tindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $Tindakan->dipohon_oleh = auth()->id();

        $Tindakan->id_pencegahan = $LastInsertedID;

        $Tindakan->save();

        //delete dalam table temp tindakan
        $TempTindakan = TempTindakan::find($id);
        $TempTindakan->destroy($id);

        // Generating Redirects...
        return redirect()->route('create')->with('status', 'Permohonan berjaya dihantar.');

    }

    public function UpdatePencegahanPadam($id){

        //delete dalam table temp tindakan
        $TempTindakan = TempTindakan::find($id);
        $TempTindakan->destroy($id);

        // Generating Redirects...
        return redirect()->route('senaraiPencegahan')->with('status', 'Permohonan berjaya dipadam.');
    }

    public function editPencegahanHantar($id){
        $Pencegahan = Pencegahan::find($id);

        $tindakan_pencegahan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->where('id_pencegahan', $Pencegahan->id_pencegahan)->first();

        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan]);
        }
        elseif(auth()->user()->peranan == 'Pengguna' or auth()->user()->peranan == 'Pegawai Kualiti' or auth()->user()->peranan == 'Admin')
        {
            $Countpelulus = User::where('staff_id', $tindakan_pencegahan['diluluskan_oleh'])->count();

            if($Countpelulus==0)
            {
                return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan])->with('pelulus','');
            }
            else
            {
                $pelulus = User::where('staff_id', $tindakan_pencegahan['diluluskan_oleh'])->first();
                return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan])->with('pelulus',$pelulus->nama);
            }
        }



        // if ( auth()->user()->peranan == 'Ketua Bahagian')
        // {
        //     $tindakan_pencegahan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
        //     ->where('id_pencegahan', $Pencegahan->id_pencegahan)->first();
        //     return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan]);
        // }
        // elseif(auth()->user()->peranan == 'Pengguna')
        // {
        //     $tindakan_pencegahan = Tindakan::where('id_pencegahan', $Pencegahan->id_pencegahan)->first();
        //     $Countpelulus = User::where('staff_id', $tindakan_pencegahan['diluluskan_oleh'])->count();

        //     if($Countpelulus==0)
        //     {
        //         return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan])->with('pelulus','');
        //     }
        //     else
        //     {
        //         $pelulus = User::where('staff_id', $tindakan_pencegahan['diluluskan_oleh'])->first();
        //         return view('ptj.edit_pencegahan_hantar', ['Pencegahan' => $Pencegahan],['tindakan_pencegahan' => $tindakan_pencegahan])->with('pelulus',$pelulus->nama);
        //     }
        // }



    }

    public function UpdatePencegahanHantarSelesai(Request $request,$id){

        //update data tindakan yg dihantar selepas tindakan selesai/tidak dilaksanakan

        $tindakan_pencegahan = Tindakan::where('id_pencegahan', $id)->first();

        $tindakan_pencegahan->status_tindakan = $request->status_tindakan;

        $tindakan_pencegahan->tarikh_siap = $request->tarikh_siap;

        $tindakan_pencegahan->tindakan_susulan = $request->tindakan_susulan;

        $tindakan_pencegahan->ulasan_keberkesanan = $request->ulasan_keberkesanan;

        $tindakan_pencegahan->dokumen_sokongan = $request->dokumen_sokongan;

        if ( auth()->user()->peranan == 'Pegawai Kualiti')
        {
            $tindakan_pencegahan->dikemaskini_oleh=auth()->id();
            $tindakan_pencegahan->save();
            // Generating Redirects...
            return redirect()->route('PKsenaraiPencegahan')->with('status', 'Maklumat berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Pengguna')
        {
            $tindakan_pencegahan->save();
            // Generating Redirects...
            return redirect()->route('senaraiPencegahan')->with('status', 'Maklumat berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            $tindakan_pencegahan->save();
            // Generating Redirects...
            return redirect()->route('AdminsenaraiPencegahan')->with('status', 'Maklumat berjaya dihantar.');
        }





    }

    // public function PencegahanHantarPapar($id){

    //     $tindakan_pencegahan = Pencegahan::where('Pencegahan.id_pencegahan', 7)
    //     ->leftJoin('Tindakan', 'Pencegahan.id_pencegahan', '=', 'Tindakan.id_pencegahan')->first();

    //     return view('papar_pencegahan',['tindakan_pencegahan' => $tindakan_pencegahan]);

    // }

    public function createPembetulan(){
        return view('ptj.form_mohon_pembetulan');
    }

    public function storeSimpanPembetulan(Request $request){

        $TempTindakan = new TempTindakan();

        $TempTindakan->cadangan_tindakan = $request->cadangan_pembetulan;

        $TempTindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $TempTindakan->perkara = $request->perkara;

        $TempTindakan->sumber_cadangan = $request->sumber_cadangan;

        $TempTindakan->keterangan_sumber_cadangan = $request->keterangan_sumber;

        $TempTindakan->dipohon_oleh = auth()->id();

        $TempTindakan->save();

        // Generating Redirects...
        return redirect()->route('createPembetulan')->with('status', 'Permohonan berjaya disimpan.');
    }

    public function storeHantarPembetulan(Request $request){

        //insert dalam table Pencegahan

        $Pembetulan = new Pembetulan();

        $Pembetulan->perkara = $request->perkara;

        $Pembetulan->sumber_cadangan = $request->sumber_cadangan;

        $Pembetulan->keterangan_sumber_cadangan = $request->keterangan_sumber;

        $Pembetulan->save();

        //get latest inserted id into Pencegahan

        $LastInsertedID=$Pembetulan->id_pembetulan;

        //insert dalam table Tindakan

        $Tindakan = new Tindakan();

        $Tindakan->status_permohonan = 'Belum Diproses';

        $Tindakan->cadangan_tindakan = $request->cadangan_pembetulan;

        $Tindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $Tindakan->dipohon_oleh = auth()->id();

        $Tindakan->id_pembetulan = $LastInsertedID;

        $Tindakan->save();

        // Generating Redirects...
        return redirect()->route('createPembetulan')->with('status', 'Permohonan berjaya dihantar.');

    }

    public function senaraiPembetulan(){

        //ambek semua dalam pembetulan and yg match je dari tindakan

        $tindakan_pembetulan = Tindakan::where('dipohon_oleh', auth()->id())
        ->rightJoin('Pembetulan', 'Tindakan.id_pembetulan', '=', 'Pembetulan.id_pembetulan')->get();

        $temp_tindakan = TempTindakan::where('dipohon_oleh', auth()->id())
        ->whereNull('tahap_risiko')->get();

        return view('senarai_pembetulan',compact('tindakan_pembetulan'),compact('temp_tindakan'));
    }

    public function editPembetulanSimpan($id){
        $TempTindakan = TempTindakan::find($id);
        return view('ptj.edit_pembetulan_simpan', ['TempTindakan' => $TempTindakan]);
    }

    public function UpdatePembetulanSimpan(Request $request,$id){

        //update data pembetulan yg disimpan(belum dihantar)

        $TempTindakan = TempTindakan::find($id);

        $TempTindakan->cadangan_tindakan = $request->cadangan_pembetulan;

        $TempTindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $TempTindakan->perkara = $request->perkara;

        $TempTindakan->sumber_cadangan = $request->sumber_cadangan;

        $TempTindakan->keterangan_sumber_cadangan = $request->keterangan_sumber;

        $TempTindakan->save();

        // Generating Redirects...
        return redirect('/pembetulan/simpan/edit/'.$id)->with('status', 'Permohonan berjaya disimpan.');

    }

    public function UpdatePembetulanHantar(Request $request,$id){

        //insert dalam table Pencegahan

        $Pembetulan = new Pembetulan();

        $Pembetulan->perkara = $request->perkara;

        $Pembetulan->sumber_cadangan = $request->sumber_cadangan;

        $Pembetulan->keterangan_sumber_cadangan = $request->keterangan_sumber;

        $Pembetulan->save();

        //get latest inserted id into Pencegahan

        $LastInsertedID=$Pembetulan->id_pembetulan;

        //insert dalam table Tindakan

        $Tindakan = new Tindakan();

        $Tindakan->status_permohonan = 'Belum Diproses';

        $Tindakan->cadangan_tindakan = $request->cadangan_pembetulan;

        $Tindakan->tarikh_jangkaan_siap = $request->tarikh_jangkaan;

        $Tindakan->dipohon_oleh = auth()->id();

        $Tindakan->id_pembetulan = $LastInsertedID;

        $Tindakan->save();

        //delete dalam table temp tindakan
        $TempTindakan = TempTindakan::find($id);
        $TempTindakan->destroy($id);

        // Generating Redirects...
        return redirect()->route('createPembetulan')->with('status', 'Permohonan berjaya dihantar.');
    }

    public function UpdatePembetulanPadam($id){

        //delete dalam table temp tindakan
        $TempTindakan = TempTindakan::find($id);
        $TempTindakan->destroy($id);

        // Generating Redirects...
        return redirect()->route('senaraiPembetulan')->with('status', 'Permohonan berjaya dipadam.');
    }


    public function editPembetulanHantar($id){
        $Pembetulan = Pembetulan::find($id);

        $tindakan_pembetulan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->where('id_pembetulan', $Pembetulan->id_pembetulan)->first();

        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return view('ptj.edit_pembetulan_hantar', ['Pembetulan' => $Pembetulan],['tindakan_pembetulan' => $tindakan_pembetulan]);
        }
        elseif(auth()->user()->peranan == 'Pengguna' or auth()->user()->peranan == 'Pegawai Kualiti' or auth()->user()->peranan == 'Admin')
        {
            $Countpelulus = User::where('staff_id', $tindakan_pembetulan['diluluskan_oleh'])->count();

            if($Countpelulus==0)
            {
                return view('ptj.edit_pembetulan_hantar', ['Pembetulan' => $Pembetulan],['tindakan_pembetulan' => $tindakan_pembetulan])->with('pelulus','');
            }
            else
            {
                $pelulus = User::where('staff_id', $tindakan_pembetulan['diluluskan_oleh'])->first();
                return view('ptj.edit_pembetulan_hantar', ['Pembetulan' => $Pembetulan],['tindakan_pembetulan' => $tindakan_pembetulan])->with('pelulus',$pelulus->nama);
            }
        }

    }

    public function UpdatePembetulanHantarSelesai(Request $request,$id){

        //update data tindakan yg dihantar selepas tindakan selesai/tidak dilaksanakan

        $tindakan_pembetulan = Tindakan::where('id_pembetulan', $id)->first();

        $tindakan_pembetulan->status_tindakan = $request->status_tindakan;

        $tindakan_pembetulan->tarikh_siap = $request->tarikh_siap;

        $tindakan_pembetulan->tindakan_susulan = $request->tindakan_susulan;

        $tindakan_pembetulan->ulasan_keberkesanan = $request->ulasan_keberkesanan;

        $tindakan_pembetulan->dokumen_sokongan = $request->dokumen_sokongan;

        $tindakan_pembetulan->save();

        if ( auth()->user()->peranan == 'Pegawai Kualiti')
        {
            $tindakan_pembetulan->dikemaskini_oleh=auth()->id();
            $tindakan_pembetulan->save();
            // Generating Redirects...
            return redirect()->route('PKsenaraiPembetulan')->with('status', 'Maklumat berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Pengguna')
        {
            $tindakan_pembetulan->save();
            // Generating Redirects...
            return redirect()->route('senaraiPembetulan')->with('status', 'Maklumat berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            $tindakan_pembetulan->save();
            // Generating Redirects...
            return redirect()->route('AdminsenaraiPembetulan')->with('status', 'Maklumat berjaya dihantar.');
        }

    }

}
