@extends('admin.layout')

@section('page-title','Sheet: Untuk Aplikasi')
@section('page-subtitle','Data human-readable untuk tampilan publik & manajemen')

@section('admin-content')
<div class="admin-card">

  <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <div class="fw-bold text-light">
      Total: <span class="badge-soft">{{ $menteris->count() }}</span>
    </div>

    <input id="searchBox" type="text"
           class="form-control form-control-sm"
           style="max-width:260px"
           placeholder="Cari nama menteri...">
  </div>

  {{-- wrapper biar horizontal scroll nyaman --}}
  <div class="table-responsive" style="overflow-x:auto;">
    <table class="table table-admin table-hover align-middle w-100" style="min-width:1700px">
      <thead>
        <tr>
          <th style="width:80px">Aksi</th>
          <th style="width:60px">No</th>
          <th style="width:80px">ID</th>

          <th style="min-width:240px">Nama</th>
          <th style="min-width:220px">Kementerian</th>
          <th style="min-width:120px">Jenis Kelamin</th>
          <th style="min-width:160px">Provinsi Lahir</th>
          <th style="min-width:100px">Kategori Umur</th>
          <th style="min-width:220px">Partai</th>
          <th style="min-width:160px">Jabatan Rangkap</th>
          <th style="min-width:140px">Pernah Menteri</th>

          <th style="min-width:150px">DPR / MPR</th>
          <th style="min-width:170px">Militer / Polisi</th>

          <th style="min-width:150px">Lokasi SMA</th>
          <th style="min-width:150px">Lokasi S1</th>
          <th style="min-width:150px">Lokasi S2</th>
          <th style="min-width:150px">Lokasi S3</th>

          <th style="min-width:220px">Jurusan S1</th>
          <th style="min-width:220px">Jurusan S2/S3</th>

          <th style="min-width:150px">Korupsi</th>
          <th style="min-width:150px">Harta</th>

          <th style="min-width:120px">UMAP X</th>
          <th style="min-width:120px">UMAP Y</th>

          <th style="min-width:120px">Status</th>
        </tr>
      </thead>

      <tbody id="tableBody">
      @foreach($menteris as $i => $m)
        <tr>
          <td>
            <a href="{{ route('admin.aplikasi.edit',$m->id) }}"
               class="btn btn-sm btn-info rounded-pill fw-bold px-3">
              Edit
            </a>
          </td>

          <td class="text-muted fw-bold">{{ $i+1 }}</td>
          <td>{{ $m->id }}</td>

          <td>
            <div class="d-flex gap-2 align-items-center">
              <img class="photo-mini" src="{{ $m->foto_path }}" onerror="this.style.display='none'">
              <div>
                <div class="fw-bold text-light">{{ $m->nama }}</div>
                <div style="font-size:.8rem;color:#94a3b8;">
                  {{ optional($m->umapEmbedding)->batch_tag ?? '-' }}
                </div>
              </div>
            </div>
          </td>

          {{-- FIXED: pakai field yang benar dari master --}}
          <td>{{ optional($m->kementerian)->nama_kementerian ?? '-' }}</td>
          <td>{{ optional($m->jenisKelamin)->label ?? '-' }}</td>
          <td>{{ optional($m->provinsiLahir)->nama ?? '-' }}</td>
          <td>{{ optional($m->umurKategori)->label ?? '-' }}</td>
          <td>{{ optional($m->partai)->nama_partai ?? '-' }}</td>
          <td>{{ optional($m->jabatanRangkap)->label ?? '-' }}</td>

          <td>
            <span class="badge-soft">
              {{ $m->pernah_menteri ? 'Pernah' : 'Tidak' }}
            </span>
          </td>

          <td>{{ optional($m->dprMpr)->label ?? '-' }}</td>
          <td>{{ optional($m->militerPolisi)->label ?? '-' }}</td>

          <td>{{ optional($m->lokasiSma)->nama ?? '-' }}</td>
          <td>{{ optional($m->lokasiS1)->nama ?? '-' }}</td>
          <td>{{ optional($m->lokasiS2)->nama ?? 'Tidak Ada / Default' }}</td>
          <td>{{ optional($m->lokasiS3)->nama ?? 'Tidak Ada / Default' }}</td>

          <td>{{ optional($m->pendidikanS1)->label_jurusan ?? '-' }}</td>
          <td>{{ optional($m->pendidikanS2S3)->label_jurusan ?? 'Tidak Ada / Default' }}</td>

          <td>{{ optional($m->korupsiLevel)->label ?? '-' }}</td>
          <td>{{ optional($m->hartaLevel)->label ?? '-' }}</td>

          <td>{{ optional($m->umapEmbedding)->umap_x ?? '-' }}</td>
          <td>{{ optional($m->umapEmbedding)->umap_y ?? '-' }}</td>

          <td>
            <span class="badge-soft text-uppercase">{{ $m->status }}</span>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<script>
  // simple search front-end (tanpa reload)
  document.getElementById('searchBox').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(tr=>{
      const name = tr.children[3].innerText.toLowerCase(); // kolom Nama (index 3)
      tr.style.display = name.includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
