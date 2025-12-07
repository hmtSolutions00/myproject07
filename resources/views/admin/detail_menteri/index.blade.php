@extends('admin.layout')

@section('page-title','Sheet: Detail Menteri')
@section('page-subtitle','Human-readable lengkap (gabungan menteris + detail_menteris)')

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

  <div class="table-responsive" style="overflow-x:auto;">
    <table class="table table-admin table-hover align-middle" style="min-width:1700px;">
      <thead>
        <tr>
          <th style="width:56px;">No</th>
          

          <th>Nama</th>
          <th>Kementerian</th>

          <th>Link Gambar</th>
          <th>Gambar</th>

          <th>Jenis Kelamin</th>
          <th>Tempat Lahir</th>
          <th>Tanggal Lahir</th>
          <th>Umur (Tahun)</th>

          <th>Partai</th>
          <th>Nama Jabatan Rangkap</th>

          <th>Karir Menteri?</th>
          <th>Karir DPR/MPR?</th>
          <th>Karir Militer/Polisi?</th>

          <th>Almamater SMA</th>
          <th>Almamater S1</th>
          <th>Almamater S2</th>
          <th>Almamater S3</th>

          <th>Bidang Pendidikan</th>
          <th>Status Hukum?</th>
          <th>Kekayaan (Rp)</th>
        </tr>
      </thead>

      <tbody id="tableBody">
      @foreach($menteris as $i => $m)
        @php
          $d = $m->detail; // detail_menteris (boleh null)
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="fw-bold text-light">{{ $m->nama ?? '-' }}</td>
          <td>{{ $m->kementerian->nama_kementerian ?? '-' }}</td>

          <td style="max-width:240px;word-break:break-all;">
            {{ $m->foto_path ?? '-' }}
          </td>

          <td>
            @if($m->foto_path)
              <img src="{{ $m->foto_path }}"
                   onerror="this.style.display='none'"
                   style="width:52px;height:52px;border-radius:10px;object-fit:cover;">
            @else
              -
            @endif
          </td>

          <td>{{ $m->jenisKelamin->label ?? '-' }}</td>
          <td>{{ $d->tempat_lahir ?? '-' }}</td>
          <td>
            {{ $d && $d->tanggal_lahir ? \Carbon\Carbon::parse($d->tanggal_lahir)->translatedFormat('l, d F Y') : '-' }}
          </td>
          <td>{{ $d->umur_tahun ?? '-' }}</td>

          <td>{{ $m->partai->nama_partai ?? '-' }}</td>
          <td>{{ $m->jabatanRangkap->label ?? '-' }}</td>

          <td>
            <span class="badge-soft">{{ $m->pernah_menteri ? 'YA' : 'TIDAK' }}</span>
          </td>

          <td>{{ $m->dprMpr->label ?? '-' }}</td>
          <td>{{ $m->militerPolisi->label ?? '-' }}</td>

          <td>{{ $d->almamater_sma ?? '-' }}</td>
          <td>{{ $d->almamater_s1 ?? '-' }}</td>
          <td>{{ $d->almamater_s2 ?? '-' }}</td>
          <td>{{ $d->almamater_s3 ?? '-' }}</td>

          <td>
            {{ $m->pendidikanS1->label_jurusan ?? $m->pendidikanS1->label ?? '-' }}
          </td>

          <td>{{ $d->status_hukum ?? '-' }}</td>

          <td>
            @if($d && is_numeric($d->kekayaan_rp))
              {{ number_format($d->kekayaan_rp, 0, ',', '.') }}
            @else
              -
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<script>
  document.getElementById('searchBox').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(tr=>{
      const name = tr.children[2].innerText.toLowerCase();
      tr.style.display = name.includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
