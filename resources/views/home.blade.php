@extends('layouts.app')

@section('content')
<div class="page-home">

  {{-- HEADER --}}
  <header class="home-header">
    <div class="home-header__inner">
      <button id="btnCompare" class="btn-pill btn-primary" type="button">
        Bandingkan Menteri
      </button>

      <h1 class="home-title">Peta Kemiripan Menteri</h1>

      <button id="btnAddData"
              class="btn-pill btn-primary"
              type="button"
              data-bs-toggle="modal"
              data-bs-target="#modalAddData">
        Masukkan Dataku
      </button>
    </div>
  </header>

  {{-- MAIN --}}
  <main class="home-main">
    <section class="umap-layout umap-layout--onecol" id="umap-layout">

      {{-- LEFT DOCK: DETAIL (lock mode) --}}
      <aside class="dock-left dock-left--hidden" id="dock-left">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-bold">Detail Menteri</div>
          <button id="btnCloseDetail" class="btn-pill btn-outline" type="button" style="padding:.35rem .8rem;font-size:.8rem;">
            Tutup
          </button>
        </div>

        <div id="detailMount"></div>
      </aside>

      {{-- RIGHT DOCK: COMPARE (compare mode) --}}
      <aside class="dock-compare dock-compare--hidden" id="dock-compare">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-bold">Bandingkan Menteri</div>
          <div class="text-muted" style="font-size:.85rem;">Klik 2 titik untuk membandingkan</div>
        </div>

        <div id="compareGrid"></div>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <button id="btnResetCompare" class="btn-pill btn-reset-compare">
            Pilih Ulang
          </button>
          <button id="btnExitCompare" class="btn-pill btn-primary">
            Selesai Bandingkan
          </button>
        </div>
      </aside>

      {{-- MAP --}}
      <div class="map-card">
        <div class="map-hints" id="mapHints">
          <span>Hover titik → lihat ringkas</span>
          <span>Click titik → kunci & scroll detail</span>
          <span>Scroll mouse → zoom (ikuti mouse)</span>
          <span>Double click → reset zoom</span>
          <span class="hint-locked" id="hint-locked" style="display:none;">Titik terkunci</span>
          <span class="hint-compare" id="hint-compare" style="display:none;">Mode Compare: pilih 2 titik</span>
        </div>

        <div id="umap-canvas" class="umap-canvas"></div>
      </div>

    </section>
  </main>
</div>


{{-- ===================== MODAL TAMBAH DATA ===================== --}}
<div class="modal fade" id="modalAddData" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    {{-- jw-adddata cuma buat scope kecil jika perlu, tidak bentrok app.css --}}
    <div class="modal-content modal-adddata jw-adddata">
      <div class="modal-header modal-adddata__header">
        <h5 class="modal-title fw-bold">Masukkan Dataku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formAddData" method="POST" action="{{ route('menteri.store') }}"
            enctype="multipart/form-data">
        @csrf

        <div class="modal-body modal-adddata__body">

          {{-- ================== SECTION: BASIC ================== --}}
          <div class="form-section">
            <div class="form-section__title">Data Dasar</div>

            <div class="row g-3">
              {{-- Nama --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Nama</label>
                <input type="text" name="nama" class="form-control" required>
              </div>

              {{-- Foto --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Foto</label>

                <div class="foto-mode-toggle">
                  <label class="badge-soft">
                    <input type="radio" name="foto_mode" value="url" checked>
                    URL
                  </label>
                  <label class="badge-soft">
                    <input type="radio" name="foto_mode" value="file">
                    Upload File
                  </label>
                </div>

                <div id="fotoUrlWrap">
                  <input type="text" name="foto_path" class="form-control"
                         placeholder="https://....jpg">
                </div>

                <div id="fotoFileWrap" class="d-none">
                  <input type="file" name="foto_file" class="form-control"
                         accept="image/*">
                </div>
              </div>
            </div>
          </div>

          {{-- ================== SECTION: DETAIL PERSONAL ================== --}}
          <div class="form-section">
            <div class="form-section__title">Detail Personal</div>

            <div class="row g-3">
              {{-- Tempat lahir (detail) --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Tempat Lahir (Detail)</label>
                <input type="text" name="tempat_lahir" class="form-control"
                       placeholder="Contoh: Majalengka, Jawa Barat">
                <small class="helper-text">Boleh kosong. Jika kosong akan pakai provinsi lahir.</small>
              </div>

              {{-- Provinsi lahir (master) + SEARCH --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Provinsi Lahir (Master)</label>

                <input type="text"
                       id="search-provinsi-lahir"
                       class="form-control mb-2"
                       placeholder="Cari provinsi lahir...">

                <select id="master-provinsi-lahir" name="provinsi_lahir_id"
                        class="form-select" required></select>

                <small class="helper-text">Ketik untuk filter provinsi lahir.</small>
              </div>

              {{-- Tanggal lahir --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Tanggal Lahir (Detail)</label>
                <input type="date" name="tanggal_lahir" class="form-control">
              </div>

              {{-- Kategori umur --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Kategori Umur (Master)</label>
                <select id="master-umur" name="umur_kategori_id"
                        class="form-select" required></select>
              </div>

              {{-- Umur tahun --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Umur (Tahun)</label>
                <input type="number" name="umur_tahun" class="form-control"
                       min="0" placeholder="Contoh: 54">
                <small class="helper-text">Opsional, boleh kosong.</small>
              </div>

              {{-- JK --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Jenis Kelamin</label>
                <div id="master-jk" class="d-flex gap-3 flex-wrap mt-1"></div>
              </div>
            </div>
          </div>

          {{-- ================== SECTION: MASTER UTAMA ================== --}}
          <div class="form-section">
            <div class="form-section__title">Atribut Utama</div>

            <div class="row g-3">

              {{-- Kementerian (SEARCH SIMPLE) --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Kementerian</label>

                <input type="text"
                       id="search-kementerian"
                       class="form-control mb-2"
                       placeholder="Cari kementerian...">

                <select id="master-kementerian"
                        name="kementerian_id"
                        class="form-select"
                        required></select>

                <small class="helper-text">Ketik untuk filter list kementerian.</small>
              </div>

              {{-- Partai (SEARCH SIMPLE) --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Partai</label>

                <input type="text"
                       id="search-partai"
                       class="form-control mb-2"
                       placeholder="Cari partai...">

                <select id="master-partai"
                        name="partai_id"
                        class="form-select"
                        required></select>

                <small class="helper-text">Ketik untuk filter list partai.</small>
              </div>

              {{-- Jabatan Rangkap --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Jabatan Rangkap</label>
                <select id="master-jabatan-rangkap" name="jabatan_rangkap_id"
                        class="form-select" required></select>
              </div>

              {{-- Pernah Menteri --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Pernah Menjabat Menteri?</label>
                <div class="d-flex gap-3 mt-2">
                  <label class="badge-soft">
                    <input type="radio" name="pernah_menteri" value="1" required>
                    Pernah
                  </label>
                  <label class="badge-soft">
                    <input type="radio" name="pernah_menteri" value="0">
                    Tidak Pernah
                  </label>
                </div>
              </div>

              {{-- DPR/MPR --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">DPR / MPR</label>
                <select id="master-dpr" name="dpr_mpr_id"
                        class="form-select" required></select>
              </div>

              {{-- Militer/Polisi --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Latar Militer / Polisi</label>
                <select id="master-militer" name="militer_polisi_id"
                        class="form-select" required></select>
              </div>

            </div>
          </div>

          {{-- ================== SECTION: ALMAMATER & LOKASI ================== --}}
          <div class="form-section">
            <div class="form-section__title">Almamater & Lokasi</div>

            <div class="row g-3">
              {{-- SMA --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Almamater SMA (Detail)</label>
                <input type="text" name="almamater_sma" class="form-control"
                       placeholder="Contoh: SMAN 1 Majalengka">
              </div>

              {{-- Lokasi SMA (master) + SEARCH --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Lokasi SMA (Master)</label>

                <input type="text"
                       id="search-lokasi-sma"
                       class="form-control mb-2"
                       placeholder="Cari lokasi SMA...">

                <select id="master-lokasi-sma" name="lokasi_sma_id"
                        class="form-select" required></select>

                <small class="helper-text">Ketik untuk filter lokasi SMA.</small>
              </div>

              {{-- S1 --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Almamater S1 (Detail)</label>
                <input type="text" name="almamater_s1" class="form-control"
                       placeholder="Contoh: IPB / UI / UGM">
              </div>

              {{-- Lokasi S1 (master) + SEARCH --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Lokasi S1 (Master)</label>

                <input type="text"
                       id="search-lokasi-s1"
                       class="form-control mb-2"
                       placeholder="Cari lokasi S1...">

                <select id="master-lokasi-s1" name="lokasi_s1_id"
                        class="form-select" required></select>

                <small class="helper-text">Ketik untuk filter lokasi S1.</small>
              </div>

              {{-- S2 --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Almamater S2 (Detail)</label>
                <input type="text" name="almamater_s2" class="form-control"
                       placeholder="Opsional">
              </div>

              {{-- Lokasi S2 (master) + SEARCH --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Lokasi S2 (Master)</label>

                <input type="text"
                       id="search-lokasi-s2"
                       class="form-control mb-2"
                       placeholder="Cari lokasi S2...">

                <select id="master-lokasi-s2" name="lokasi_s2_id"
                        class="form-select"></select>

                <small class="helper-text">Ketik untuk filter lokasi S2.</small>
              </div>

              {{-- S3 --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Almamater S3 (Detail)</label>
                <input type="text" name="almamater_s3" class="form-control"
                       placeholder="Opsional">
              </div>

              {{-- Lokasi S3 (master) + SEARCH --}}
              <div class="col-md-6">
                <label class="form-label form-label--tight">Lokasi S3 (Master)</label>

                <input type="text"
                       id="search-lokasi-s3"
                       class="form-control mb-2"
                       placeholder="Cari lokasi S3...">

                <select id="master-lokasi-s3" name="lokasi_s3_id"
                        class="form-select"></select>

                <small class="helper-text">Ketik untuk filter lokasi S3.</small>
              </div>
            </div>
          </div>

          {{-- ================== SECTION: PENDIDIKAN & LEVEL ================== --}}
          <div class="form-section">
            <div class="form-section__title">Pendidikan & Level</div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label form-label--tight">Jurusan / Bidang S1</label>
                <select id="master-pendidikan-s1" name="pendidikan_s1_id"
                        class="form-select" required></select>
              </div>

              <div class="col-md-6">
                <label class="form-label form-label--tight">Jurusan S2 / S3</label>
                <select id="master-pendidikan-s2s3" name="pendidikan_s2s3_id"
                        class="form-select"></select>
              </div>

              <div class="col-md-6">
                <label class="form-label form-label--tight">Level Korupsi</label>
                <select id="master-korupsi" name="korupsi_level_id"
                        class="form-select" required></select>
              </div>

              <div class="col-md-6">
                <label class="form-label form-label--tight">Level Harta</label>
                <select id="master-harta" name="harta_level_id"
                        class="form-select" required></select>
              </div>

              <div class="col-md-6">
                <label class="form-label form-label--tight">Kekayaan (Rp) - Detail</label>
                <input type="number" name="kekayaan_rp" class="form-control"
                       min="0" step="1" placeholder="Contoh: 9022400000">
                <small class="helper-text">Opsional. Isi angka tanpa titik/koma.</small>
              </div>

              <div class="col-md-6">
                <label class="form-label form-label--tight">Status Hukum - Detail</label>
                <input type="text" name="status_hukum" class="form-control"
                       placeholder="Contoh: Terperiksa/Saksi">
              </div>

              <div class="col-12">
                <label class="form-label form-label--tight">Jabatan (opsional)</label>
                <textarea name="jabatan" class="form-control" rows="2"
                          placeholder="Contoh: Kepala Badan Gizi Nasional"></textarea>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer modal-adddata__footer">
          <button type="submit" class="btn-pill btn-primary">Simpan</button>
          <button type="button" class="btn-pill btn-outline" data-bs-dismiss="modal">
            Batal
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection


{{-- ================== EXTRA STYLES (optional kecil) ================== --}}
@push('styles')
<style>
  /* kecil aja, gak ganggu app.css */
  .jw-adddata .form-control.mb-2{
    border-radius:10px;
  }
</style>
@endpush


{{-- ================== SCRIPTS ================== --}}
@push('scripts')
<script>
/* =========================================================
   SEARCH SIMPLE UNTUK SELECT (NO LIB)
   -> dipakai Kementerian, Partai, & semua Provinsi
========================================================= */
function attachSelectSearch(inputEl, selectEl) {
  if (!inputEl || !selectEl) return;

  const allOptions = [];
  for (const opt of selectEl.options) {
    allOptions.push({ value: opt.value, text: opt.textContent });
  }

  const render = (keyword) => {
    const key = (keyword || "").toLowerCase().trim();
    selectEl.innerHTML = "";

    const filtered = !key
      ? allOptions
      : allOptions.filter(o => o.text.toLowerCase().includes(key));

    for (const o of filtered) {
      const opt = document.createElement("option");
      opt.value = o.value;
      opt.textContent = o.text;
      selectEl.appendChild(opt);
    }

    if (!filtered.length) {
      const opt = document.createElement("option");
      opt.value = "";
      opt.textContent = "Tidak ada hasil";
      opt.disabled = true;
      selectEl.appendChild(opt);
    }
  };

  render("");
  inputEl.addEventListener("input", (e) => render(e.target.value));
}

/* =========================================================
   INIT SEARCH SETELAH MASTERS KEISI
   (loadMasters ada di app.js)
========================================================= */
function initSearchAfterMasters() {
  const pairs = [
    // kementerian + partai
    ["search-kementerian", "master-kementerian"],
    ["search-partai", "master-partai"],

    // provinsi-based
    ["search-provinsi-lahir", "master-provinsi-lahir"],
    ["search-lokasi-sma", "master-lokasi-sma"],
    ["search-lokasi-s1", "master-lokasi-s1"],
    ["search-lokasi-s2", "master-lokasi-s2"],
    ["search-lokasi-s3", "master-lokasi-s3"],
  ];

  for (const [inputId, selectId] of pairs) {
    const inputEl  = document.getElementById(inputId);
    const selectEl = document.getElementById(selectId);

    if (!selectEl || !selectEl.options.length) return false;
    attachSelectSearch(inputEl, selectEl);
  }

  return true;
}

document.addEventListener("shown.bs.modal", (e) => {
  if (e.target.id !== "modalAddData") return;

  const timer = setInterval(() => {
    if (initSearchAfterMasters()) clearInterval(timer);
  }, 200);
});
</script>
<script>
  // kirim id menteri baru ke JS (null kalau gak ada)
  window.__NEW_MENTERI_ID__ = @json(session('new_menteri_id'));
</script>

@vite('resources/js/app.js')
@endpush
