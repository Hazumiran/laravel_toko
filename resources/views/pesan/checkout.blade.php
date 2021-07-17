@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>Menu</a>
        </div>
        <div class="col-md-12 mt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-shopping-cart"></i>CheckOut</h3>
                    @if($pesanan != null && $pesanan_detail != null)
                    <p align="right">Tanggal Pesan : {{ $pesanan->tanggal}} </p>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            @forelse($pesanan_detail as $pesanan_detail)
                            <tr>
                                <td>{{$no++}} </td>
                                <td>
                                    <img src="{{ url('uploads') }}/{{ $pesanan_detail->barang->gambar }}" width="150">
                                </td>
                                <td>{{$pesanan_detail->barang->nama_barang }} </td>
                                <td>{{$pesanan_detail->jumlah }} </td>
                                <td>Rp. {{ number_format($pesanan_detail->barang->harga) }} </td>
                                <td>Rp. {{ number_format($pesanan_detail->jumlah_harga)}} </td>
                                <td>
                                    <form action="{{url('checkout')}}/{{ $pesanan_detail->id }} " method="POST">
                                        @csrf
                                        {{method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('apakah anda yakin ingin menghapus data ini ?');"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>
                                    Bwwaaa zonk !
                                </td>
                            </tr>
                            @endforelse
                            <tr>
                                <td colspan="4" align="right"><strong>Total Harga :</strong> </td>
                                <td><strong> Rp. {{ number_format($pesanan->jumlah_harga)}} </strong></td>
                                <td>
                                    <a href="{{ url('confirm') }}" class="btn btn-success" onclick="return confirm('apakah anda yakin checkout sekarang ?');"><i class="fa fa-shopping-cart"></i>CheckOut</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>


            </div>
        </div>


    </div>

</div>
@endsection