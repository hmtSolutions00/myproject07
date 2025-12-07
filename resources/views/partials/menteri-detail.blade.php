<div class="d-grid gap-2" style="font-size:.92rem">
  <div><strong>Kementerian:</strong> {{ $m->kementerian->nama_kementerian ?? '-' }}</div>
  <div><strong>Jenis kelamin:</strong> {{ $m->jenisKelamin->label ?? '-' }}</div>
  <div><strong>Provinsi lahir:</strong> {{ $m->provinsiLahir->label ?? '-' }}</div>
  <div><strong>Umur kategori:</strong> {{ $m->umurKategori->label ?? '-' }}</div>
  <div><strong>Partai:</strong> {{ $m->partai->label ?? '-' }}</div>
  <div><strong>Jabatan rangkap:</strong> {{ $m->jabatanRangkap->label ?? '-' }}</div>

  <hr class="my-1" style="border-color:rgba(255,255,255,.08)">

  <div><strong>Pernah menteri:</strong> {{ $m->pernah_menteri ? 'Ya' : 'Tidak' }}</div>
  <div><strong>DPR/MPR:</strong> {{ $m->dprMpr->label ?? '-' }}</div>
  <div><strong>Militer/Polisi:</strong> {{ $m->militerPolisi->label ?? '-' }}</div>

  <hr class="my-1" style="border-color:rgba(255,255,255,.08)">

  <div><strong>Lokasi SMA:</strong> {{ $m->lokasiSma->label ?? '-' }}</div>
  <div><strong>Lokasi S1:</strong> {{ $m->lokasiS1->label ?? '-' }}</div>
  <div><strong>Lokasi S2:</strong> {{ $m->lokasiS2->label ?? '-' }}</div>
  <div><strong>Lokasi S3:</strong> {{ $m->lokasiS3->label ?? '-' }}</div>

  <hr class="my-1" style="border-color:rgba(255,255,255,.08)">

  <div><strong>Jurusan S1:</strong> {{ $m->pendidikanS1->label ?? '-' }}</div>
  <div><strong>Jurusan S2/S3:</strong> {{ $m->pendidikanS2S3->label ?? '-' }}</div>

  <hr class="my-1" style="border-color:rgba(255,255,255,.08)">

  <div><strong>Korupsi:</strong> {{ $m->korupsiLevel->label ?? '-' }}</div>
  <div><strong>Harta:</strong> {{ $m->hartaLevel->label ?? '-' }}</div>
</div>
