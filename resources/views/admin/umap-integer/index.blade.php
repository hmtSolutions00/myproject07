@extends('admin.layout')

@section('page-title','Sheet: Untuk UMAP Integer')
@section('page-subtitle','Vektor integer final yang dipakai UMAP engine')

@section('admin-content')
<div class="admin-card">

  {{-- Top Actions --}}
  <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <div class="fw-bold text-light">
      Total baris: <span class="badge-soft">{{ count($rows) }}</span>
    </div>

    <div class="d-flex gap-2">
      {{-- Recompute --}}
      <form method="POST" action="{{ route('admin.umap.integer.recompute') }}">
        @csrf
        <button type="submit"
                class="btn btn-info fw-bold rounded-pill px-4 text-dark"
                onclick="return confirm('Recompute akan menghitung ulang embedding UMAP. Lanjutkan?')">
          Recompute UMAP
        </button>
      </form>

      {{-- Clear --}}
      <form method="POST" action="{{ route('admin.umap.integer.clear') }}">
        @csrf
        <button type="submit"
                class="btn btn-outline-danger fw-bold rounded-pill px-4"
                onclick="return confirm('INI AKAN MENGHAPUS SEMUA umap_embeddings. Yakin?')">
          Clear UMAP
        </button>
      </form>
    </div>
  </div>

  {{-- alerts --}}
  @if(session('success'))
    <div class="alert alert-success rounded-3 mb-3">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger rounded-3 mb-3">
      {{ session('error') }}
    </div>
  @endif

  <div class="table-responsive">
    <table class="table table-admin table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Nama</th>
          <th>JK</th><th>Prov</th><th>Umur</th><th>Partai</th><th>Jab Rangkap</th>
          <th>Pernah</th>
          <th>DPR</th><th>Mil/Pol</th>
          <th>SMA</th><th>S1</th><th>S2</th><th>S3</th>
          <th>Jur S1</th><th>Jur S2/S3</th>
          <th>Korupsi</th><th>Harta</th>
        </tr>
      </thead>
      <tbody>
      @foreach($rows as $r)
        <tr>
          <td>{{ $r['id'] }}</td>
          <td class="fw-bold text-light">{{ $r['nama'] }}</td>
          <td>{{ $r['jenis_kelamin'] }}</td>
          <td>{{ $r['provinsi_lahir'] }}</td>
          <td>{{ $r['umur'] }}</td>
          <td>{{ $r['partai'] }}</td>
          <td>{{ $r['jabatan_rangkap'] }}</td>
          <td><span class="badge-soft">{{ $r['pernah_menteri'] }}</span></td>
          <td>{{ $r['dpr_mpr'] }}</td>
          <td>{{ $r['militer_polisi'] }}</td>
          <td>{{ $r['lokasi_sma'] }}</td>
          <td>{{ $r['lokasi_s1'] }}</td>
          <td>{{ $r['lokasi_s2'] }}</td>
          <td>{{ $r['lokasi_s3'] }}</td>
          <td>{{ $r['pendidikan_s1'] }}</td>
          <td>{{ $r['pendidikan_s2s3'] }}</td>
          <td>{{ $r['korupsi'] }}</td>
          <td>{{ $r['harta'] }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
