export function renderCompareHTML(a, b) {
  const disp = (v) => (v === null || v === undefined || v === "") ? "-" : v;

  const sanitizeColor = (hex) => {
    if (!hex) return null;
    const c = String(hex).toLowerCase().trim();
    if (c === "#000" || c === "#000000" || c === "black") return null;
    return c;
  };

  const leftColor  = sanitizeColor(a.color_code) || "#e5e7eb";
  const rightColor = sanitizeColor(b.color_code) || "#e5e7eb";

  // ✅ umur prioritas detail -> angka mentah -> kategori
  const dispUmur = (d) => {
    if (d.display_umur_tahun) return d.display_umur_tahun;
    if (d.umur_tahun !== null && d.umur_tahun !== undefined && d.umur_tahun !== "") {
      return `${d.umur_tahun} Tahun`;
    }
    return disp(d.umur);
  };

  // urutan field harus sama persis kiri-label-kanan
  const fields = [
    { k: "Jenis Kelamin",     a: a.jenis_kelamin,          b: b.jenis_kelamin },
    { k: "Tempat Lahir",      a: a.display_tempat_lahir,   b: b.display_tempat_lahir },
    { k: "Tanggal Lahir",     a: a.display_tanggal_lahir,  b: b.display_tanggal_lahir },
    { k: "Umur (Tahun)",      a: dispUmur(a),              b: dispUmur(b) },

    { k: "Provinsi Lahir",    a: a.provinsi_lahir,         b: b.provinsi_lahir },
    { k: "Umur Kategori",     a: a.umur,                   b: b.umur },

    { k: "Partai",            a: a.partai,                 b: b.partai },
    { k: "Jabatan Rangkap",   a: a.jabatan_rangkap,        b: b.jabatan_rangkap },

    { k: "Karir DPR/MPR",     a: a.dpr_mpr,                b: b.dpr_mpr },
    { k: "Karir Mil/Pol",     a: a.militer_polisi,         b: b.militer_polisi },

    { k: "Lokasi SMA",        a: a.lokasi_sma,             b: b.lokasi_sma },
    { k: "Lokasi S1",         a: a.lokasi_s1,              b: b.lokasi_s1 },
    { k: "Lokasi S2",         a: a.lokasi_s2,              b: b.lokasi_s2 },
    { k: "Lokasi S3",         a: a.lokasi_s3,              b: b.lokasi_s3 },

    { k: "Almamater SMA",     a: a.display_almamater_sma,  b: b.display_almamater_sma },
    { k: "Almamater S1",      a: a.display_almamater_s1,   b: b.display_almamater_s1 },
    { k: "Almamater S2",      a: a.display_almamater_s2,   b: b.display_almamater_s2 },
    { k: "Almamater S3",      a: a.display_almamater_s3,   b: b.display_almamater_s3 },

    { k: "Bidang S1",         a: a.pendidikan_s1,          b: b.pendidikan_s1 },
    { k: "Bidang S2/S3",      a: a.pendidikan_s2s3,        b: b.pendidikan_s2s3 },

    { k: "Korupsi",           a: a.korupsi_level,          b: b.korupsi_level },
    { k: "Harta Level",       a: a.harta_level,            b: b.harta_level },

    { k: "Status Hukum",      a: a.display_status_hukum,   b: b.display_status_hukum },
    { k: "Kekayaan (Rp)",     a: formatRupiah(a.display_kekayaan_rp), b: formatRupiah(b.display_kekayaan_rp) },
  ];

  const labelsHTML = fields.map(f => `<div class="k">${f.k}</div>`).join("");

  const valuesHTML = (side) =>
    fields.map(f => `<div class="v">${disp(side === "a" ? f.a : f.b)}</div>`).join("");

  const cardHTML = (d, color, sideKey) => `
    <div class="compare-card" style="background:${color}">
      <div class="compare-card-inner">
        <div class="compare-header">
          <img class="compare-photo" src="${d.foto_url || ""}" onerror="this.style.display='none'">
          <div>
            <div class="compare-name">${disp(d.nama)}</div>
            <div class="compare-sub">${disp(d.catatan)}</div>
          </div>
        </div>

        <div class="compare-values">
          ${valuesHTML(sideKey)}
        </div>
      </div>
    </div>
  `;

  return `
    <div class="compare-grid-3">
      ${cardHTML(a, leftColor, "a")}

      <div class="compare-labels">
        ${labelsHTML}
      </div>

      ${cardHTML(b, rightColor, "b")}
    </div>
  `;
}
