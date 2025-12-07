@extends('admin.layout')

@section('page-title','Edit Menteri')
@section('page-subtitle',$menteri->nama)

@section('admin-content')
<div class="row g-3">

  <div class="col-lg-4">
    <div class="admin-card">
      <div class="fw-bold mb-2">Preview Foto</div>
      <img id="photoPreview"
           src="{{ $menteri->foto_path }}"
           onerror="this.style.display='none'"
           style="width:100%;height:260px;object-fit:cover;border-radius:14px;border:1px solid rgba(255,255,255,.08);background:#0b1226;">
      <div class="text-muted mt-2" style="font-size:.85rem;color:#94a3b8;">
        Ganti URL foto di form kanan untuk update preview.
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="admin-card">

      @if ($errors->any())
        <div class="alert alert-danger rounded-3 fw-bold">
          Ada error, cek inputmu ya.
          <ul class="mt-2 mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST"
            action="{{ route('admin.aplikasi.update',$menteri->id) }}"
            class="form-admin">
        @csrf
        @method('PUT')

        <div class="row g-3">

          {{-- Nama --}}
          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control"
                   value="{{ old('nama',$menteri->nama) }}" required>
          </div>

          {{-- Foto --}}
          <div class="col-md-6">
            <label class="form-label">Foto URL</label>
            <input id="fotoInput" type="text" name="foto_path" class="form-control"
                   value="{{ old('foto_path',$menteri->foto_path) }}">
          </div>

          <div class="col-md-12"><hr style="border-color:rgba(255,255,255,.06)"></div>

          {{-- Kementerian --}}
          <div class="col-md-6">
            <label class="form-label">Kementerian</label>
            <select name="kementerian_id" class="form-select" required>
              @foreach($kementerians as $k)
                <option value="{{ $k->id }}"
                  @selected(old('kementerian_id',$menteri->kementerian_id)==$k->id)>
                  {{ $k->nama_kementerian }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Jenis Kelamin --}}
          <div class="col-md-6">
            <label class="form-label">Jenis Kelamin</label>
            <div class="d-flex gap-3 mt-1 flex-wrap">
              @foreach($jenisKalamins as $jk)
                <label class="badge-soft">
                  <input type="radio" name="jenis_kelamin_id" value="{{ $jk->id }}"
                    @checked(old('jenis_kelamin_id',$menteri->jenis_kelamin_id)==$jk->id)>
                  {{ $jk->label }}
                </label>
              @endforeach
            </div>
          </div>

          {{-- Provinsi Lahir --}}
          <div class="col-md-6">
            <label class="form-label">Provinsi Lahir</label>
            <select name="provinsi_lahir_id" class="form-select" required>
              @foreach($provinsis as $p)
                <option value="{{ $p->id }}"
                  @selected(old('provinsi_lahir_id',$menteri->provinsi_lahir_id)==$p->id)>
                  {{ $p->nama }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Kategori Umur --}}
          <div class="col-md-6">
            <label class="form-label">Kategori Umur</label>
            <select name="umur_kategori_id" class="form-select" required>
              @foreach($umurKategoris as $u)
                <option value="{{ $u->id }}"
                  @selected(old('umur_kategori_id',$menteri->umur_kategori_id)==$u->id)>
                  {{ $u->label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Partai --}}
          <div class="col-md-6">
            <label class="form-label">Partai</label>
            <select name="partai_id" class="form-select" required>
              @foreach($partais as $pt)
                <option value="{{ $pt->id }}"
                  @selected(old('partai_id',$menteri->partai_id)==$pt->id)>
                  {{ $pt->nama_partai }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Jabatan Rangkap --}}
          <div class="col-md-6">
            <label class="form-label">Jabatan Rangkap</label>
            <select name="jabatan_rangkap_id" class="form-select" required>
              @foreach($jabatanRangkaps as $jr)
                <option value="{{ $jr->id }}"
                  @selected(old('jabatan_rangkap_id',$menteri->jabatan_rangkap_id)==$jr->id)>
                  {{ $jr->label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Pernah Menteri --}}
          <div class="col-md-6">
            <label class="form-label">Pernah Menjabat Menteri?</label>
            <div class="d-flex gap-3 mt-1">
              <label class="badge-soft">
                <input type="radio" name="pernah_menteri" value="1"
                  @checked(old('pernah_menteri',$menteri->pernah_menteri)==1)>
                Pernah
              </label>
              <label class="badge-soft">
                <input type="radio" name="pernah_menteri" value="0"
                  @checked(old('pernah_menteri',$menteri->pernah_menteri)==0)>
                Tidak Pernah
              </label>
            </div>
          </div>

          {{-- Status --}}
          <div class="col-md-6">
            <label class="form-label">Status Publik</label>
            <select name="status" class="form-select" required>
              @foreach(['approved'=>'Approved','pending'=>'Pending','rejected'=>'Rejected'] as $key=>$label)
                <option value="{{ $key }}"
                  @selected(old('status',$menteri->status)==$key)>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- DPR/MPR --}}
          <div class="col-md-6">
            <label class="form-label">DPR / MPR</label>
            <select name="dpr_mpr_id" class="form-select" required>
              @foreach($dprMprs as $d)
                <option value="{{ $d->id }}"
                  @selected(old('dpr_mpr_id',$menteri->dpr_mpr_id)==$d->id)>
                  {{ $d->label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Militer/Polisi --}}
          <div class="col-md-6">
            <label class="form-label">Latar Militer / Polisi</label>
            <select name="militer_polisi_id" class="form-select" required>
              @foreach($militerPolisis as $mp)
                <option value="{{ $mp->id }}"
                  @selected(old('militer_polisi_id',$menteri->militer_polisi_id)==$mp->id)>
                  {{ $mp->label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Lokasi SMA --}}
          <div class="col-md-6">
            <label class="form-label">Lokasi SMA</label>
            <select name="lokasi_sma_id" class="form-select" required>
              @foreach($provinsis as $p)
                <option value="{{ $p->id }}"
                  @selected(old('lokasi_sma_id',$menteri->lokasi_sma_id)==$p->id)>
                  {{ $p->nama }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Lokasi S1 --}}
          <div class="col-md-6">
            <label class="form-label">Lokasi S1</label>
            <select name="lokasi_s1_id" class="form-select" required>
              @foreach($provinsis as $p)
                <option value="{{ $p->id }}"
                  @selected(old('lokasi_s1_id',$menteri->lokasi_s1_id)==$p->id)>
                  {{ $p->nama }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Lokasi S2 --}}
          <div class="col-md-6">
            <label class="form-label">Lokasi S2</label>
            <select name="lokasi_s2_id" class="form-select">
              <option value="{{ $prov0_id }}"
                @selected(old('lokasi_s2_id',$menteri->lokasi_s2_id)==$prov0_id)>
                Tidak Ada / Default
              </option>
              @foreach($provinsis as $p)
                @if($p->id != $prov0_id)
                  <option value="{{ $p->id }}"
                    @selected(old('lokasi_s2_id',$menteri->lokasi_s2_id)==$p->id)>
                    {{ $p->nama }}
                  </option>
                @endif
              @endforeach
            </select>
          </div>

          {{-- Lokasi S3 --}}
          <div class="col-md-6">
            <label class="form-label">Lokasi S3</label>
            <select name="lokasi_s3_id" class="form-select">
              <option value="{{ $prov0_id }}"
                @selected(old('lokasi_s3_id',$menteri->lokasi_s3_id)==$prov0_id)>
                Tidak Ada / Default
              </option>
              @foreach($provinsis as $p)
                @if($p->id != $prov0_id)
                  <option value="{{ $p->id }}"
                    @selected(old('lokasi_s3_id',$menteri->lokasi_s3_id)==$p->id)>
                    {{ $p->nama }}
                  </option>
                @endif
              @endforeach
            </select>
          </div>

          {{-- Pendidikan S1 --}}
          <div class="col-md-6">
            <label class="form-label">Jurusan / Bidang S1</label>
            <select name="pendidikan_s1_id" class="form-select" required>
              @foreach($pendidikanUmum as $pu)
                <option value="{{ $pu->id }}"
                  @selected(old('pendidikan_s1_id',$menteri->pendidikan_s1_id)==$pu->id)>
                  {{ $pu->label_jurusan }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Pendidikan S2/S3 (FIX) --}}
          <div class="col-md-6">
            <label class="form-label">Jurusan S2 / S3</label>
            <select name="pendidikan_s2s3_id" class="form-select">
              <option value="{{ $pend0_id }}"
                @selected(old('pendidikan_s2s3_id',$menteri->pendidikan_s2s3_id)==$pend0_id)>
                Tidak Ada / Default
              </option>
              @foreach($pendidikanS2S3 as $ps)
                @if($ps->id != $pend0_id)
                  <option value="{{ $ps->id }}"
                    @selected(old('pendidikan_s2s3_id',$menteri->pendidikan_s2s3_id)==$ps->id)>
                    {{ $ps->label_jurusan }}
                  </option>
                @endif
              @endforeach
            </select>
          </div>

          {{-- Korupsi --}}
          <div class="col-md-6">
            <label class="form-label">Level Korupsi</label>
            <select name="korupsi_level_id" class="form-select" required>
              @foreach($korupsis as $k)
                <option value="{{ $k->id }}"
                  @selected(old('korupsi_level_id',$menteri->korupsi_level_id)==$k->id)>
                  {{ $k->label }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Harta --}}
          <div class="col-md-6">
            <label class="form-label">Level Harta</label>
            <select name="harta_level_id" class="form-select" required>
              @foreach($hartas as $h)
                <option value="{{ $h->id }}"
                  @selected(old('harta_level_id',$menteri->harta_level_id)==$h->id)>
                  {{ $h->label }}
                </option>
              @endforeach
            </select>
          </div>

        </div>

        <div class="sticky-actions mt-3 d-flex gap-2">
          <button class="btn btn-info fw-bold rounded-pill px-4 text-dark" type="submit">
            Simpan Perubahan
          </button>
          <a href="{{ route('admin.aplikasi.index') }}"
             class="btn btn-outline-light fw-bold rounded-pill px-4">
            Batal
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  const fotoInput = document.getElementById('fotoInput');
  const photoPreview = document.getElementById('photoPreview');
  fotoInput?.addEventListener('input', () => {
    photoPreview.src = fotoInput.value;
    photoPreview.style.display = fotoInput.value ? 'block' : 'none';
  });
</script>
@endsection
