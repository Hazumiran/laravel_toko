<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use RealRashid\SweetAlert\Facades\Alert;


class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
        $barang = Barang::where('id', $id)->first();

        return view('pesan.index', compact('barang'));
    }
    public function pesan(Request $request, $id)
    {
        $barang = Barang::where('id', $id)->first();
        $tanggal = Carbon::now();

        //validasi stok
        if ($request->jumlah_pesan > $barang->stok) {
            return redirect('pesan/' . $id);
        }

        //validasi pemesasanan
        $cek_pemesanan = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();
        if (empty($cek_pemesanan)) {

            $pesanan = new Pesanan;
            $pesanan->user_id = Auth::user()->id;
            $pesanan->tanggal = $tanggal;
            $pesanan->status = 0;
            $pesanan->kode = mt_rand(100, 999);
            $pesanan->jumlah_harga = 0;
            $pesanan->save();
        }


        //details
        $pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();
        //cek details
        $cek_details = PesananDetail::where('barang_id', $barang->id)->where('pesanan_id', $pesanan_baru->id)->first();
        if (empty($cek_details)) {

            $pesanan_detail = new PesananDetail;
            $pesanan_detail->barang_id = $barang->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->jumlah = $request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $barang->harga * $request->jumlah_pesan;
            $pesanan_detail->save();
        } else {
            $pesanan_detail = PesananDetail::where('barang_id', $barang->id)->where('pesanan_id', $pesanan_baru->id)->first();
            $pesanan_detail->jumlah = $pesanan_detail->jumlah + $request->jumlah_pesan;

            $harga_pesanan_detail =  $barang->harga * $request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $pesanan_detail->jumlah_harga + $harga_pesanan_detail;
            $pesanan_detail->update();
        }

        //jumlah total
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga + $barang->harga * $request->jumlah_pesan;
        $pesanan->update();

        Alert::success('Sukses', 'MasukKeranjang');
        return redirect('checkout');
    }
    public function checkout()
    {
        // $pesanan = Pesanan::where('user_id', Auth::user()->id)->first();
        // $show = false;
        // if (!empty($pesanan)) {
        //     if ($pesanan->status == 0) {
        //         $show = true;
        //         $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan->id)->where('status', 0)->get();
        //         dd($pesanan_detail);
        //     } else {
        //         $pesanan_detail = null;
        //     }
        // }

        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();
        if (!empty($pesanan)) {
            $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan->id)->get();
        } elseif ($pesanan == null) {
            $pesanan_detail = null;
        }

        return view('pesan.checkout', compact('pesanan', 'pesanan_detail'));
    }

    public function checkout_delete($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->first();

        $pesanan =  Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga - $pesanan_detail->jumlah_harga;
        $pesanan->update();

        $pesanan_detail->delete();

        Alert::error('Pesanan dihapus', 'Hapus');
        return redirect('checkout');
    }
    public function confirm()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if (empty($user->alamat)) {
            Alert::error('Oops', 'Identitas haru diisi lengkap !');
            return redirect('profile');
        }
        if (empty($user->no_hp)) {
            Alert::error('Oops', 'Identitas haru diisi lengkap !');
            return redirect('profile');
        }

        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();

        $pesanan_id = $pesanan->id;

        $pesanan->status = 1;
        $pesanan->update();

        $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan_id)->get();
        foreach ($pesanan_detail as $pesanan_detail) {
            $barang = Barang::where('id', $pesanan_detail->barang_id)->first();
            $barang->stok = $barang->stok - $pesanan_detail->jumlah;
            $barang->update();
        }

        Alert::success('Pesanan Checkout', 'Silahkan sekarang bayar');

        return redirect('history/' . $pesanan_id);
    }
}
