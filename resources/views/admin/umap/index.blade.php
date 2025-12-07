@extends('admin.layout')

@section('page-title','Sheet: Untuk UMAP (Label)')
@section('page-subtitle','Audit fitur UMAP dalam bentuk label asli')

@section('admin-content')
<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>JK</th>
          <th>Prov Lahir</th>
          <th>Umur</th>
          <th>Partai</th>
          <th>Jabatan Rangkap</th>
          <th>Pernah Menteri</th>
          <th>DPR/MPR</th>
          <th>Mil/Pol</th>
          <th>SMA</th>
          <th>S1</th>
          <th>S2</th>
          <th>S3</th>
          <th>Jurusan S1</th>
          <th>Jurusan S2/S3</th>
          <th>Korupsi</th>
          <th>Harta</th>
        </tr>
      </thead>
      <tbody>
      @foreach($menteris as $m)
        <tr>
          <td>{{ $m->id }}</td>
          <td class="fw-bold text-light">{{ $m->nama }}</td>
          <td>{{ $m->jenisKelamin->label ?? '-' }}</td>
          <td>{{ $m->provinsiLahir->nama_provinsi ?? '-' }}</td>
          <td>{{ $m->umurKategori->label ?? '-' }}</td>
          <td>{{ $m->partai->label ?? '-' }}</td>
          <td>{{ $m->jabatanRangkap->label ?? '-' }}</td>
          <td><span class="badge-soft">{{ $m->pernah_menteri?'Pernah':'Tidak' }}</span></td>
          <td>{{ $m->dprMpr->label ?? '-' }}</td>
          <td>{{ $m->militerPolisi->label ?? '-' }}</td>
          <td>{{ $m->lokasiSma->nama_provinsi ?? '-' }}</td>
          <td>{{ $m->lokasiS1->nama_provinsi ?? '-' }}</td>
          <td>{{ optional($m->lokasiS2)->nama_provinsi ?? '-' }}</td>
          <td>{{ optional($m->lokasiS3)->nama_provinsi ?? '-' }}</td>
          <td>{{ $m->pendidikanS1->label ?? '-' }}</td>
          <td>{{ optional($m->pendidikanS2S3)->label ?? '-' }}</td>
          <td>{{ $m->korupsiLevel->label ?? '-' }}</td>
          <td>{{ $m->hartaLevel->label ?? '-' }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
