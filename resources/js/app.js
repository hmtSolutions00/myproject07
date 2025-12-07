import * as d3 from "d3";
import tippy from "tippy.js";
import "tippy.js/dist/tippy.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

/* =========================================================
   MASTER LOADER (untuk modal tambah data)
   Ambil dari /api/masters
========================================================= */
async function loadMasters() {
  try {
    const res = await fetch("/api/masters");
    const m = await res.json();

    const fillSelect = (el, items, labelKey = "label", extraFirst = null) => {
      if (!el) return;
      el.innerHTML = "";

      if (extraFirst) {
        const opt0 = document.createElement("option");
        opt0.value = extraFirst.value;
        opt0.textContent = extraFirst.text;
        el.appendChild(opt0);
      }

      (items || []).forEach((it) => {
        const opt = document.createElement("option");
        opt.value = it.id;
        opt.textContent =
          it[labelKey] ??
          it.nama ??
          it.nama_kementerian ??
          it.nama_partai ??
          it.label_jurusan ??
          "-";
        el.appendChild(opt);
      });
    };

    // SELECTS
    fillSelect(
      document.getElementById("master-kementerian"),
      m.kementerian,
      "nama_kementerian"
    );
    fillSelect(
      document.getElementById("master-provinsi-lahir"),
      m.provinsi,
      "nama"
    );
    fillSelect(document.getElementById("master-umur"), m.umur_kategori, "label");
    fillSelect(document.getElementById("master-partai"), m.partai, "nama_partai");
    fillSelect(
      document.getElementById("master-jabatan-rangkap"),
      m.jabatan_rangkap,
      "label"
    );
    fillSelect(document.getElementById("master-dpr"), m.dpr_mpr, "label");
    fillSelect(
      document.getElementById("master-militer"),
      m.militer_polisi,
      "label"
    );

    // Lokasi (provinsi)
    fillSelect(document.getElementById("master-lokasi-sma"), m.provinsi, "nama");
    fillSelect(document.getElementById("master-lokasi-s1"), m.provinsi, "nama");

    fillSelect(
      document.getElementById("master-lokasi-s2"),
      m.provinsi,
      "nama",
      { value: 0, text: "Tidak Ada / Default" }
    );
    fillSelect(
      document.getElementById("master-lokasi-s3"),
      m.provinsi,
      "nama",
      { value: 0, text: "Tidak Ada / Default" }
    );

    // Pendidikan
    fillSelect(
      document.getElementById("master-pendidikan-s1"),
      m.pendidikan_umum,
      "label_jurusan"
    );
    fillSelect(
      document.getElementById("master-pendidikan-s2s3"),
      m.pendidikan_s2s3,
      "label_jurusan",
      { value: 0, text: "Tidak Ada / Default" }
    );

    // Korupsi & Harta
    fillSelect(
      document.getElementById("master-korupsi"),
      m.korupsi_level,
      "label"
    );
    fillSelect(document.getElementById("master-harta"), m.harta_level, "label");

    // RADIO JK
    const jkWrap = document.getElementById("master-jk");
    if (jkWrap) {
      jkWrap.innerHTML = "";
      (m.jenis_kelamin || []).forEach((jk) => {
        const lab = document.createElement("label");
        lab.className = "badge-soft";
        lab.innerHTML = `
          <input type="radio" name="jenis_kelamin_id" value="${jk.id}" required>
          ${jk.label}
        `;
        jkWrap.appendChild(lab);
      });
    }
  } catch (err) {
    console.error("loadMasters error:", err);
  }
}
loadMasters();

// switch foto mode url / file
document.addEventListener("change", (e) => {
  if (!e.target.matches("input[name='foto_mode']")) return;

  const mode = e.target.value;
  const urlWrap = document.getElementById("fotoUrlWrap");
  const fileWrap = document.getElementById("fotoFileWrap");

  if (!urlWrap || !fileWrap) return;

  if (mode === "file") {
    urlWrap.classList.add("d-none");
    fileWrap.classList.remove("d-none");
  } else {
    fileWrap.classList.add("d-none");
    urlWrap.classList.remove("d-none");
  }
});


/* =========================================================
   UMAP + DETAIL + COMPARE ONEPAGE
========================================================= */
(async function () {
  const canvas = document.getElementById("umap-canvas");
  if (!canvas) return;

  // elemen dock & layout (dibuat null-safe)
  const dockLeft    = document.getElementById("dock-left");
  const dockCompare = document.getElementById("dock-compare");
  const layout      = document.getElementById("umap-layout");
  const hintLocked  = document.getElementById("hint-locked");
  const hintCompare = document.getElementById("hint-compare");

  const btnCompare      = document.getElementById("btnCompare");
  const btnExitCompare  = document.getElementById("btnExitCompare");
  const btnResetCompare = document.getElementById("btnResetCompare");
  const btnCloseDetail  = document.getElementById("btnCloseDetail");

  const width  = canvas.clientWidth;
  const height = canvas.clientHeight;

  // helper display aman
  const disp = (v) => (v === null || v === undefined || v === "") ? "-" : v;

  // helper precedence: detail menang, fallback ke base
  const pick = (detailVal, baseVal, fallback = "-") => {
    if (detailVal !== null && detailVal !== undefined && detailVal !== "")
      return detailVal;
    if (baseVal !== null && baseVal !== undefined && baseVal !== "")
      return baseVal;
    return fallback;
  };

  // umur display: detail (display_umur_tahun / umur_tahun) menang
  const dispUmur = (d) => {
    // kalau API kasih display_umur_tahun, pakai itu
    if (d.display_umur_tahun !== null && d.display_umur_tahun !== undefined && d.display_umur_tahun !== "")
      return d.display_umur_tahun;

    // fallback angka umur_tahun mentah
    if (d.umur_tahun !== null && d.umur_tahun !== undefined && d.umur_tahun !== "")
      return `${d.umur_tahun} Tahun`;

    // terakhir umur kategori
    return disp(d.umur);
  };

  // SVG root
  const svg = d3.select(canvas)
    .append("svg")
    .attr("width", width)
    .attr("height", height);

  const g = svg.append("g").attr("class", "zoom-layer");

  // Fetch data UMAP
  const res = await fetch("/api/menteri");
  const raw = await res.json();

  const data = (raw || []).filter(
    (d) => Number.isFinite(+d.umap_x) && Number.isFinite(+d.umap_y)
  );

  if (!data.length) {
    canvas.innerHTML =
      "<div class='text-center p-5 text-muted'>Data UMAP belum tersedia</div>";
    return;
  }

  // scale
  const x = d3.scaleLinear()
    .domain(d3.extent(data, (d) => +d.umap_x))
    .nice()
    .range([30, width - 30]);

  const y = d3.scaleLinear()
    .domain(d3.extent(data, (d) => +d.umap_y))
    .nice()
    .range([height - 30, 30]);

  // palette fallback (tanpa hitam)
  const palette = [
    "#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF",
    "#FF9F40","#00C49A","#F15BB5","#00BBF9","#9BDEAC",
  ];

  const sanitizeColor = (hex) => {
    if (!hex) return null;
    const c = String(hex).toLowerCase().trim();
    if (c === "#000" || c === "#000000" || c === "black") return null;
    return c;
  };

  const getColor = (d, i) =>
    sanitizeColor(d.color_code) || palette[i % palette.length];

  let lockedId = null;
  let tipInstance = null;

  // compare mode state
  let compareMode = false;
  let compareIds = []; // max 2

  // hover template (pakai display + dispUmur)
  const hoverTemplate = (d, color) => `
    <div style="width:320px; background:${color}; border-radius:12px; padding:8px;">
      <div class="hover-glass">
        <div style="display:flex; gap:8px; align-items:center; margin-bottom:6px;">
          <div style="flex:1;">
            <div style="font-weight:900; font-size:1rem;">${disp(d.nama)}</div>
            <div style="font-size:.84rem; color:#475569;">${disp(d.kementerian)}</div>
          </div>
          <img src="${d.foto_url || ""}"
               onerror="this.style.display='none'"
               style="width:44px;height:44px;border-radius:10px;object-fit:cover;background:#fff;">
        </div>

        <div class="tooltip-kv">
          <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>
          <div class="k">Umur</div><div class="v">${dispUmur(d)}</div>
          <div class="k">Partai</div><div class="v">${disp(d.partai)}</div>
          <div class="k">Bidang S1</div><div class="v">${disp(d.pendidikan_s1)}</div>
        </div>

        <div style="margin-top:6px; font-size:.75rem; color:#475569;">
          ${compareMode ? "Klik untuk memilih compare" : "Klik titik untuk kunci & lihat detail"}
        </div>
      </div>
    </div>
  `;

  function showHover(d, node, color) {
    if (lockedId && lockedId !== d.id) return;
    if (tipInstance) tipInstance.destroy();

    tipInstance = tippy(node, {
      content: hoverTemplate(d, color),
      allowHTML: true,
      interactive: false,
      trigger: "manual",
      theme: "umap-hover",
      placement: "right-start",
      appendTo: document.body,
      popperOptions: {
        modifiers: [
          { name: "offset", options: { offset: [8, 8] } },
          { name: "preventOverflow", options: { padding: 8 } },
          {
            name: "flip",
            options: { fallbackPlacements: ["left-start", "top", "bottom"] },
          },
        ],
      },
    });

    tipInstance.show();
  }

  // DETAIL FULL (pakai display_* dan precedence detail menang)
  function renderFullCard(d, color) {
    if (!dockLeft) return;

    // set accent buat css dock putih + border warna
    dockLeft.style.setProperty("--dock-accent", color);

    dockLeft.innerHTML = `
      <div class="detail-head">
        <img class="detail-photo" src="${d.foto_url || ""}" onerror="this.style.display='none'">
        <button id="btnCloseDetailInner" class="detail-close">×</button>
      </div>

      <div class="detail-title">${disp(d.nama)}</div>
      <div class="detail-sub">${disp(d.kementerian)}</div>

      <div class="detail-scroll">
        <div class="kv">
          <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>

          <div class="k">Tempat Lahir</div><div class="v">${pick(d.display_tempat_lahir, d.provinsi_lahir)}</div>
          <div class="k">Tanggal Lahir</div><div class="v">${disp(d.display_tanggal_lahir)}</div>
          <div class="k">Umur (Tahun)</div><div class="v">${dispUmur(d)}</div>

          <div class="k">Provinsi Lahir</div><div class="v">${disp(d.provinsi_lahir)}</div>
          <div class="k">Umur Kategori</div><div class="v">${disp(d.umur)}</div>

          <div class="k">Partai</div><div class="v">${disp(d.partai)}</div>
          <div class="k">Jabatan Rangkap</div><div class="v">${disp(d.jabatan_rangkap)}</div>

          <div class="k">Karir DPR/MPR</div><div class="v">${disp(d.dpr_mpr)}</div>
          <div class="k">Karir Mil/Pol</div><div class="v">${disp(d.militer_polisi)}</div>

          <div class="k">Lokasi SMA</div><div class="v">${disp(d.lokasi_sma)}</div>
          <div class="k">Lokasi S1</div><div class="v">${disp(d.lokasi_s1)}</div>
          <div class="k">Lokasi S2</div><div class="v">${disp(d.lokasi_s2)}</div>
          <div class="k">Lokasi S3</div><div class="v">${disp(d.lokasi_s3)}</div>

          <div class="k">Almamater SMA</div><div class="v">${disp(d.display_almamater_sma)}</div>
          <div class="k">Almamater S1</div><div class="v">${disp(d.display_almamater_s1)}</div>
          <div class="k">Almamater S2</div><div class="v">${disp(d.display_almamater_s2)}</div>
          <div class="k">Almamater S3</div><div class="v">${disp(d.display_almamater_s3)}</div>

          <div class="k">Bidang S1</div><div class="v">${disp(d.pendidikan_s1)}</div>
          <div class="k">Bidang S2/S3</div><div class="v">${disp(d.pendidikan_s2s3)}</div>

          <div class="k">Korupsi</div><div class="v">${disp(d.korupsi_level)}</div>
          <div class="k">Harta Level</div><div class="v">${disp(d.harta_level)}</div>

          <div class="k">Status Hukum</div><div class="v">${disp(d.display_status_hukum)}</div>
          <div class="k">Kekayaan (Rp)</div><div class="v">${disp(d.display_kekayaan_rp)}</div>
        </div>
      </div>
    `;

    dockLeft.classList.remove("dock-left--hidden");
    layout?.classList.remove("umap-layout--onecol");
    hintLocked && (hintLocked.style.display = "inline-flex");

    dockLeft.querySelector("#btnCloseDetailInner")
      ?.addEventListener("click", closeDetailDock);
  }

  function closeDetailDock() {
    lockedId = null;
    dockLeft?.classList.add("dock-left--hidden");
    layout?.classList.add("umap-layout--onecol");
    hintLocked && (hintLocked.style.display = "none");

    nodes
      .attr("fill", (n, i) => getColor(n, i))
      .attr("r", 5)
      .attr("opacity", 0.9);
  }

  // COMPARE DOCK (pakai display_* juga)
  function renderCompareDock() {
    const grid = document.getElementById("compareGrid");
    if (!grid) return;

    const selected = compareIds
      .map(id => data.find(d => d.id === id))
      .filter(Boolean);

    if (selected.length < 2) {
      dockCompare?.classList.add("dock-compare--hidden");
      return;
    }

    const leftD  = selected[0];
    const rightD = selected[1];
    const leftColor  = getColor(leftD,  data.indexOf(leftD));
    const rightColor = getColor(rightD, data.indexOf(rightD));

    const fields = [
      { label:"Jenis Kelamin",   a:leftD.jenis_kelamin,        b:rightD.jenis_kelamin },
      { label:"Tempat Lahir",    a:leftD.display_tempat_lahir, b:rightD.display_tempat_lahir },
      { label:"Tanggal Lahir",   a:leftD.display_tanggal_lahir,b:rightD.display_tanggal_lahir },
      { label:"Umur (Tahun)",    a:dispUmur(leftD),            b:dispUmur(rightD) },

      { label:"Provinsi Lahir",  a:leftD.provinsi_lahir,       b:rightD.provinsi_lahir },
      { label:"Umur Kategori",   a:leftD.umur,                 b:rightD.umur },

      { label:"Partai",          a:leftD.partai,               b:rightD.partai },
      { label:"Jabatan Rangkap", a:leftD.jabatan_rangkap,      b:rightD.jabatan_rangkap },

      { label:"Karir DPR/MPR",   a:leftD.dpr_mpr,              b:rightD.dpr_mpr },
      { label:"Karir Mil/Pol",   a:leftD.militer_polisi,       b:rightD.militer_polisi },

      { label:"Lokasi SMA",      a:leftD.lokasi_sma,           b:rightD.lokasi_sma },
      { label:"Lokasi S1",       a:leftD.lokasi_s1,            b:rightD.lokasi_s1 },
      { label:"Lokasi S2",       a:leftD.lokasi_s2,            b:rightD.lokasi_s2 },
      { label:"Lokasi S3",       a:leftD.lokasi_s3,            b:rightD.lokasi_s3 },

      { label:"Almamater SMA",   a:leftD.display_almamater_sma,b:rightD.display_almamater_sma },
      { label:"Almamater S1",    a:leftD.display_almamater_s1, b:rightD.display_almamater_s1 },
      { label:"Almamater S2",    a:leftD.display_almamater_s2, b:rightD.display_almamater_s2 },
      { label:"Almamater S3",    a:leftD.display_almamater_s3, b:rightD.display_almamater_s3 },

      { label:"Bidang S1",       a:leftD.pendidikan_s1,        b:rightD.pendidikan_s1 },
      { label:"Bidang S2/S3",    a:leftD.pendidikan_s2s3,      b:rightD.pendidikan_s2s3 },

      { label:"Korupsi",         a:leftD.korupsi_level,        b:rightD.korupsi_level },
      { label:"Harta Level",     a:leftD.harta_level,          b:rightD.harta_level },

      { label:"Status Hukum",    a:leftD.display_status_hukum, b:rightD.display_status_hukum },
      { label:"Kekayaan (Rp)",   a:leftD.display_kekayaan_rp,   b:rightD.display_kekayaan_rp },
    ];

    const rows = fields.map(f => `
      <div class="compare-cell">${disp(f.a)}</div>
      <div class="compare-label">${f.label}</div>
      <div class="compare-cell">${disp(f.b)}</div>
    `).join("");

    grid.innerHTML = `
      <div class="compare-wrap">
        <div class="compare-head">
          <div class="compare-side" style="background:${leftColor}">
            <div class="compare-side-inner">
              <img class="compare-photo" src="${leftD.foto_url || ""}" onerror="this.style.display='none'">
              <div>
                <div class="compare-name">${disp(leftD.nama)}</div>
                <div class="compare-sub">${disp(leftD.kementerian)}</div>
              </div>
            </div>
          </div>

          <div class="compare-mid"></div>

          <div class="compare-side" style="background:${rightColor}">
            <div class="compare-side-inner">
              <img class="compare-photo" src="${rightD.foto_url || ""}" onerror="this.style.display='none'">
              <div>
                <div class="compare-name">${disp(rightD.nama)}</div>
                <div class="compare-sub">${disp(rightD.kementerian)}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="compare-table">
          ${rows}
        </div>
      </div>
    `;

    dockCompare?.classList.remove("dock-compare--hidden");
  }

  function resetCompare() {
    compareIds = [];
    dockCompare?.classList.add("dock-compare--hidden");
    nodes
      .attr("fill", (n, i) => getColor(n, i))
      .attr("opacity", 0.9)
      .attr("r", 5);
  }

  function enterCompareMode() {
    compareMode = true;
    lockedId = null;

    dockLeft?.classList.add("dock-left--hidden");
    layout?.classList.add("umap-layout--onecol");
    hintLocked && (hintLocked.style.display = "none");
    hintCompare && (hintCompare.style.display = "inline-flex");

    resetCompare();

    if (btnCompare) {
      btnCompare.textContent = "Mode Compare Aktif";
      btnCompare.classList.add("btn-outline");
    }
  }

  function exitCompareMode() {
    compareMode = false;
    hintCompare && (hintCompare.style.display = "none");
    dockCompare?.classList.add("dock-compare--hidden");

    if (btnCompare) {
      btnCompare.textContent = "Bandingkan Menteri";
      btnCompare.classList.remove("btn-outline");
    }

    nodes
      .attr("fill", (n, i) => getColor(n, i))
      .attr("opacity", 0.9)
      .attr("r", 5);
  }

  // ===== dots =====
  const nodes = g.selectAll("circle")
    .data(data)
    .enter()
    .append("circle")
    .attr("cx", (d) => x(+d.umap_x))
    .attr("cy", (d) => y(+d.umap_y))
    .attr("r", 5)
    .attr("fill", (d, i) => getColor(d, i))
    .attr("opacity", 0.9)
    .style("cursor", "pointer");

  nodes
    .on("mouseenter", function (event, d) {
      const i = data.indexOf(d);
      const color = getColor(d, i);

      d3.select(this).attr("r", lockedId === d.id ? 8 : 7);
      showHover(d, this, color);
    })
    .on("mouseleave", function () {
      d3.select(this).attr("r", (n) => (lockedId === n.id ? 8 : 5));
      if (!lockedId && tipInstance) tipInstance.hide();
    })
    .on("click", function (event, d) {
      const i = data.indexOf(d);
      const color = getColor(d, i);

      // ===== compare mode click =====
      if (compareMode) {
        if (compareIds.includes(d.id)) {
          compareIds = compareIds.filter((x) => x !== d.id);
        } else {
          if (compareIds.length < 2) compareIds.push(d.id);
          else compareIds[1] = d.id;
        }

        nodes
          .attr("fill", (n, ii) => compareIds.includes(n.id) ? "#000000" : getColor(n, ii))
          .attr("r",    (n) => compareIds.includes(n.id) ? 8 : 5)
          .attr("opacity", (n) => compareIds.includes(n.id) ? 1 : 0.85);

        if (compareIds.length === 2) renderCompareDock();
        else dockCompare?.classList.add("dock-compare--hidden");

        if (tipInstance) tipInstance.destroy();
        return;
      }

      // ===== normal lock click =====
      lockedId = d.id;

      nodes
        .attr("fill", (n, ii) => n.id === lockedId ? "#000000" : getColor(n, ii))
        .attr("r",    (n) => n.id === lockedId ? 8 : 5);

      if (tipInstance) tipInstance.destroy();
      renderFullCard(d, color);
    });

  // ===== zoom & pan follow mouse =====
  const zoom = d3.zoom()
    .scaleExtent([0.7, 8])
    .on("zoom", (event) => {
      g.attr("transform", event.transform);
    });

  svg.call(zoom);
  svg.on("dblclick.zoom", null);
  svg.on("dblclick", () => {
    svg.transition().duration(250).call(zoom.transform, d3.zoomIdentity);
  });

  // ===== compare buttons =====
  btnCompare?.addEventListener("click", () => {
    if (!compareMode) enterCompareMode();
    else exitCompareMode();
  });

  btnExitCompare?.addEventListener("click", exitCompareMode);
  btnResetCompare?.addEventListener("click", resetCompare);

  // ===== close detail external button (kalau ada di blade) =====
  btnCloseDetail?.addEventListener("click", closeDetailDock);
})();
