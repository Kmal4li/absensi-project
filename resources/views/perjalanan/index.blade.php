    @extends('layouts.app')

    @push('style')
    @powerGridStyles
    @endpush

    @section('buttons')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div>
            <a href="{{ route('perjalanan.create') }}" class="btn btn-sm btn-primary">
                <span data-feather="plus-circle" class="align-text-bottom me-1"></span>
                Tambah Data Perjalanan
            </a>
        </div>
    </div>
    @endsection

    @section('content')
    @include('partials.alerts')
    <livewire:perjalanan-table />
    @if ($perjalananUserTransaksi->isEmpty())
        <p class="text-muted">Belum ada laporan keuangan yang diunggah.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Transaksi</th>
                    <th>Jumlah Nominal</th>
                    <th>Deskripsi</th>
                    <th>Bukti Transaksi</th>
                    <th>Dibuat Pada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perjalananUserTransaksi as $laporan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $laporan->nama_transaksi }}</td>
                        <td>{{ number_format($laporan->jumlah_nominal, 2, ',', '.') }}</td>
                        <td>{{ $laporan->deskripsi }}</td>
                        <td>
                            @if ($laporan->bukti_transaksi)
                                <a href="{{ asset('storage/' . $laporan->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-primary">Lihat Bukti</a>
                            @else
                                <span class="text-muted">Tidak Ada</span>
                            @endif
                        </td>
                        <td>{{ $laporan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

    @push('script')
    <script src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
    @powerGridScripts
    @endpush