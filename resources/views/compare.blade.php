@extends('layouts.app')
@section('title','Bandingkan Menteri')

@section('content')
<section class="glass p-4 mb-3" 
  x-data="{ compareMode:false }">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h2 class="h4 fw-bold mb-1">Bandingkan Menteri</h2>
      <p class="mb-0" style="color:var(--muted)">Aktifkan mode untuk pilih 2 menteri.</p>
      <small x-show="compareMode" class="badge-soft mt-2 d-inline-block">
        Mode Bandingkan Aktif
      </small>
    </div>
    <button class="btn-genz" 
      @click="compareMode = !compareMode; window.setCompareMode(compareMode)">
      <span x-text="compareMode ? 'Matikan Bandingkan' : 'Bandingkan Menteri'"></span>
    </button>
  </div>
</section>

<section class="glass p-3 position-relative mb-3">
  <div id="umap-canvas"></div>
</section>

<section id="compare-cards" class="d-none">
  <div class="compare-wrap">
    <div id="card-left" class="glass p-3 info-card"></div>
    <div id="card-right" class="glass p-3 info-card"></div>
  </div>
  <div class="text-center mt-3">
    <button class="btn btn-outline-light rounded-pill px-4"
      onclick="window.resetCompare()">
      Banding Ulang
    </button>
    <div class="compare-sync-note">Scroll salah satu kartu → keduanya bergerak bareng</div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/umap-compare.js')
@endpush

