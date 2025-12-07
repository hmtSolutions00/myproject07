@extends('layouts.app')
@section('title','Tambah Data Menteri')

@section('content')
<section class="row justify-content-center">
  <div class="col-lg-8">
    <div class="glass p-4">
      <h2 class="h4 fw-bold mb-1">Tambah Data Menteri</h2>
      <p style="color:var(--muted)">Isi data seakurat mungkin. Data kamu bisa masuk antrian review.</p>

      <form method="POST" action="{{ route('menteri.store') }}" enctype="multipart/form-data" class="mt-3">
        @csrf

        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Nama</label>
            <input name="nama" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Foto (URL atau Upload)</label>
            <input name="foto_url" class="form-control mb-2" placeholder="https://...">
            <input name="foto_file" type="file" class="form-control">
          </div>

          {{-- contoh master select --}}
          <div class="col-md-6">
            <label class="form-label">Kementerian</label>
            <select name="kementerian_id" class="form-select form-select-lg">
              @foreach($kementerians as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kementerian }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Jenis Kelamin</label>
            <div class="d-flex gap-3 pt-2">
              @foreach($jenisKelimins as $jk)
                <label class="form-check">
                  <input class="form-check-input" type="radio" name="jenis_kelamin_id" value="{{ $jk->id }}" required>
                  <span class="form-check-label">{{ $jk->label }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- sisanya kamu lanjutkan sesuai master yang sudah ada --}}
        </div>

        <button class="btn-genz mt-4 w-100">Kirim Data</button>
      </form>
    </div>
  </div>
</section>
@endsection
