<?php

namespace App\Http\Controllers;

use App\Models\KampusUsers;
use App\Models\Pencegahan;
use App\Models\Pertukaran;
use App\Models\Tindakan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
// use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
// use \PDF;
use Illuminate\Http\Request;

class KBController extends Controller
{
    public function KBsenaraiPencegahan(){

        $countKB_Kampus = KampusUsers::where('staff_id',auth()->id())->count();

        if($countKB_Kampus==1)
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->first();

            $tindakan_pencegahan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('pencegahan','pencegahan.id_pencegahan','=','Tindakan.id_pencegahan')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->get();
        }
        else
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->get();

            if($countKB_Kampus==2)
            {
                $tindakan_pencegahan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('pencegahan','pencegahan.id_pencegahan','=','Tindakan.id_pencegahan')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->get();
            }
            elseif($countKB_Kampus==3)
            {
                $tindakan_pencegahan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('pencegahan','pencegahan.id_pencegahan','=','Tindakan.id_pencegahan')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->get();
            }
        }
        return view('senarai_pencegahan',compact('tindakan_pencegahan'));
    }

    public function KBDashboard(){

        $countKB_Kampus = KampusUsers::where('staff_id',auth()->id())->count();

        if($countKB_Kampus==1)
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->first();

            $belumDiproses=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_permohonan','Belum Diproses')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();

            $Lulus=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_permohonan','Lulus')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();

            $Tolak=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_permohonan','Tolak')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();

            $dalamTindakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_tindakan','Dalam Tindakan')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();

            $Selesai=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_tindakan','Selesai')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();

            $tidakDilaksanakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('tindakan.status_tindakan','Tidak Dilaksanakan')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->count();
        }
        else
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->get();

            if($countKB_Kampus==2)
            {
                $belumDiproses=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Belum Diproses')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();

                $Lulus=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Lulus')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();

                $Tolak=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Tolak')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();

                $dalamTindakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Dalam Tindakan')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();

                $Selesai=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Selesai')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();

                $tidakDilaksanakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Tidak Dilaksanakan')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->count();
            }
            elseif($countKB_Kampus==3)
            {
                $belumDiproses=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Belum Diproses')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();

                $Lulus=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Lulus')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();

                $Tolak=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_permohonan','Tolak')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();

                $dalamTindakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Dalam Tindakan')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();

                $Selesai=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Selesai')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();

                $tidakDilaksanakan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('tindakan.status_tindakan','Tidak Dilaksanakan')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->count();
            }
        }

        $dashboard = collect([$belumDiproses, $Lulus, $Tolak, $dalamTindakan, $Selesai, $tidakDilaksanakan ]);

        return view('laman_utama',compact('dashboard'));
    }

    public function updateLulus(Request $request,$id){


        $tindakan_pencegahan = Tindakan::where('id_pencegahan', $id)->first();

        $tindakan_pencegahan->status_permohonan = 'Lulus';

        $tindakan_pencegahan->tarikh_lulus = Carbon::now();

        $tindakan_pencegahan->arahan_tindakan = $request->arahan_tindakan;

        $tindakan_pencegahan->status_tindakan = 'Dalam Tindakan';

        $tindakan_pencegahan->diluluskan_oleh = auth()->id();

        $tindakan_pencegahan->save();


        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pencegahan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pencegahan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
        }

    }

    public function updateTolak(Request $request,$id){

        $tindakan_pencegahan = Tindakan::where('id_pencegahan', $id)->first();

        $tindakan_pencegahan->status_permohonan = 'Tolak';

        $tindakan_pencegahan->tarikh_lulus = Carbon::now();

        $tindakan_pencegahan->arahan_tindakan = $request->arahan_tindakan;

        $tindakan_pencegahan->diluluskan_oleh = auth()->id();

        $tindakan_pencegahan->save();


        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pencegahan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pencegahan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
        }

    }

    public function updateUlasan(Request $request,$id){

        $tindakan_pencegahan = Tindakan::where('id_pencegahan', $id)->first();

        $tindakan_pencegahan->ulasan_ketua_bahagian = $request->ulasan_ketua_bahagian;

        $tindakan_pencegahan->save();

        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pencegahan/hantar/edit/'.$id)->with('status', 'Ulasan berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pencegahan/hantar/edit/'.$id)->with('status', 'Ulasan berjaya dihantar.');
        }

    }

    public function KBsenaraiPembetulan(){

        $countKB_Kampus = KampusUsers::where('staff_id',auth()->id())->count();

        if($countKB_Kampus==1)
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->first();

            $tindakan_pembetulan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('pembetulan','pembetulan.id_pembetulan','=','Tindakan.id_pembetulan')
            ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
            ->where('users.ptj_code',auth()->user()->ptj_code)
            ->where('kampus_users.kampus_id', $KB_kampus->kampus_id)
            ->get();
        }
        else
        {
            $KB_kampus=KampusUsers::where('staff_id',auth()->id())->get();

            if($countKB_Kampus==2)
            {
                $tindakan_pembetulan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('pembetulan','pembetulan.id_pembetulan','=','Tindakan.id_pembetulan')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id);
                })->get();
            }
            elseif($countKB_Kampus==3)
            {
                $tindakan_pembetulan=Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
                ->join('pembetulan','pembetulan.id_pembetulan','=','Tindakan.id_pembetulan')
                ->join('kampus_users','kampus_users.staff_id','=','users.staff_id')
                ->where('users.ptj_code',auth()->user()->ptj_code)
                ->where(function($query) use ($KB_kampus){
                    $query->where('kampus_users.kampus_id', $KB_kampus[0]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[1]->kampus_id)
                          ->orWhere('kampus_users.kampus_id', $KB_kampus[2]->kampus_id);
                })->get();
            }
        }
        return view('senarai_pembetulan',compact('tindakan_pembetulan'));
    }


    public function updateLulusPembetulan(Request $request,$id){

        $tindakan_pembetulan = Tindakan::where('id_pembetulan', $id)->first();

        $tindakan_pembetulan->status_permohonan = 'Lulus';

        $tindakan_pembetulan->tarikh_lulus = Carbon::now();

        $tindakan_pembetulan->arahan_tindakan = $request->arahan_tindakan;

        $tindakan_pembetulan->status_tindakan = 'Dalam Tindakan';

        $tindakan_pembetulan->diluluskan_oleh = auth()->id();

        $tindakan_pembetulan->save();

        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pembetulan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pembetulan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya diluluskan.');
        }

    }

    public function updateTolakPembetulan(Request $request,$id){

        $tindakan_pembetulan = Tindakan::where('id_pembetulan', $id)->first();

        $tindakan_pembetulan->status_permohonan = 'Tolak';

        $tindakan_pembetulan->tarikh_lulus = Carbon::now();

        $tindakan_pembetulan->arahan_tindakan = $request->arahan_tindakan;

        $tindakan_pembetulan->diluluskan_oleh = auth()->id();

        $tindakan_pembetulan->save();


        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pembetulan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pembetulan/hantar/edit/'.$id)->with('status', 'Permohonan berjaya ditolak.');
        }

    }

    public function updateUlasanPembetulan(Request $request,$id){

        $tindakan_pembetulan = Tindakan::where('id_pembetulan', $id)->first();

        $tindakan_pembetulan->ulasan_ketua_bahagian = $request->ulasan_ketua_bahagian;

        $tindakan_pembetulan->save();

        if ( auth()->user()->peranan == 'Ketua Bahagian')
        {
            return redirect('/KB/pembetulan/hantar/edit/'.$id)->with('status', 'Ulasan berjaya dihantar.');
        }
        elseif ( auth()->user()->peranan == 'Admin')
        {
            return redirect('/admin/kb/pembetulan/hantar/edit/'.$id)->with('status', 'Ulasan berjaya dihantar.');
        }

    }

    public function reportPDF(){

        return view('input_laporan');
    }

    public function generatepdfPembetulan(Request $request){


        $tarikh_mula = $request->tarikh_mula;
        $tarikh_akhir = $request->tarikh_akhir;
        $jenis_laporan=$request->jenis_laporan;

        $startDate = Carbon::createFromFormat('Y-m-d', $tarikh_mula);
        $endDate = Carbon::createFromFormat('Y-m-d', $tarikh_akhir);

        if($jenis_laporan=='Log Tindakan Pembetulan')
        {
            $data = Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('pembetulan','pembetulan.id_pembetulan','=','Tindakan.id_pembetulan')
            ->where('tarikh_lulus', '>=', $startDate)
            ->where('tarikh_lulus', '<=', $endDate)
            ->whereNotNull('Tindakan.id_pembetulan')
            ->where('status_permohonan', 'Lulus')
            ->get();

            if (!$data->isEmpty())
            {
                $pdf = FacadePdf::loadView('laporan_pembetulan', [ 'data' => $data]);
                $pdf->setPaper('A4', 'landscape');
                $namapdf="Laporan_Pembetulan($tarikh_mula)-($tarikh_akhir).pdf";
                return $pdf->download($namapdf);
            }
            else
            {
                return redirect('/PK/laporan')->with('status', 'Tiada rekod ditemui.');
            }
        }
        elseif($jenis_laporan=='Log Tindakan Pencegahan')
        {
            $data = Tindakan::join('users','users.staff_id','=','Tindakan.dipohon_oleh')
            ->join('pencegahan','pencegahan.id_pencegahan','=','Tindakan.id_pencegahan')
            ->where('tarikh_lulus', '>=', $startDate)
            ->where('tarikh_lulus', '<=', $endDate)
            ->whereNotNull('Tindakan.id_pencegahan')
            ->where('status_permohonan', 'Lulus')
            ->get();

            if (!$data->isEmpty())
            {
                $pdf = FacadePdf::loadView('laporan_pencegahan', [ 'data' => $data]);
                $pdf->setPaper('A4', 'landscape');
                $namapdf="Laporan_Pencegahan($tarikh_mula)-($tarikh_akhir).pdf";
                return $pdf->download($namapdf);
            }
            else
            {
                return redirect('/PK/laporan')->with('status', 'Tiada rekod ditemui.');
            }


        }

    }

}
