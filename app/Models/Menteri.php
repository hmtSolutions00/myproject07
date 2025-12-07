<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menteri extends Model
{
    // SAMAKAN dengan nama tabel migration
    protected $table = 'menteris';

    protected $fillable = [
        'nama', 'foto_path',

        'kementerian_id',
        'jenis_kelamin_id',
        'provinsi_lahir_id',
        'umur_kategori_id',
        'partai_id',
        'jabatan_rangkap_id',

        'pernah_menteri',

        'dpr_mpr_id',
        'militer_polisi_id',

        'lokasi_sma_id',
        'lokasi_s1_id',
        'lokasi_s2_id',
        'lokasi_s3_id',

        'pendidikan_s1_id',
        'pendidikan_s2s3_id',

        'korupsi_level_id',
        'harta_level_id',

        'status',
        'submitted_by_ip'
    ];

    protected $casts = [
        'pernah_menteri' => 'integer',
    ];

    // relasi sama seperti punyamu (aman)
    public function kementerian(): BelongsTo { return $this->belongsTo(MasterKementerian::class, 'kementerian_id'); }
    public function jenisKelamin(): BelongsTo { return $this->belongsTo(MasterJenisKelamin::class, 'jenis_kelamin_id'); }
    public function provinsiLahir(): BelongsTo { return $this->belongsTo(MasterProvinsi::class, 'provinsi_lahir_id'); }
    public function umurKategori(): BelongsTo { return $this->belongsTo(MasterUmurKategori::class, 'umur_kategori_id'); }
    public function partai(): BelongsTo { return $this->belongsTo(MasterPartai::class, 'partai_id'); }
    public function jabatanRangkap(): BelongsTo { return $this->belongsTo(MasterJabatanRangkap::class, 'jabatan_rangkap_id'); }
    public function dprMpr(): BelongsTo { return $this->belongsTo(MasterDprMpr::class, 'dpr_mpr_id'); }
    public function militerPolisi(): BelongsTo { return $this->belongsTo(MasterMiliterPolisi::class, 'militer_polisi_id'); }
    public function lokasiSma(): BelongsTo { return $this->belongsTo(MasterProvinsi::class, 'lokasi_sma_id'); }
    public function lokasiS1(): BelongsTo { return $this->belongsTo(MasterProvinsi::class, 'lokasi_s1_id'); }
    public function lokasiS2(): BelongsTo { return $this->belongsTo(MasterProvinsi::class, 'lokasi_s2_id'); }
    public function lokasiS3(): BelongsTo { return $this->belongsTo(MasterProvinsi::class, 'lokasi_s3_id'); }
    public function pendidikanS1(): BelongsTo { return $this->belongsTo(MasterPendidikan::class, 'pendidikan_s1_id'); }
    public function pendidikanS2S3(): BelongsTo { return $this->belongsTo(MasterPendidikan::class, 'pendidikan_s2s3_id'); }
    public function korupsiLevel(): BelongsTo { return $this->belongsTo(MasterKorupsiLevel::class, 'korupsi_level_id'); }
    public function hartaLevel(): BelongsTo { return $this->belongsTo(MasterHartaLevel::class, 'harta_level_id'); }
    public function umapEmbedding(): HasOne
{
    return $this->hasOne(\App\Models\UmapEmbedding::class, 'menteri_id')
                ->where('is_active', true);
}
public function detail(): HasOne
{
    return $this->hasOne(\App\Models\DetailMenteri::class, 'menteri_id');
}

}
