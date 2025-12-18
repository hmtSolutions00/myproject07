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

      const toOption = (it) => ({
        value: it.id,
        text:
          it[labelKey] ??
          it.nama ??
          it.nama_kementerian ??
          it.nama_partai ??
          it.label_jurusan ??
          "-",
      });

      const options = (items || []).map(toOption);

      // ✅ kalau sudah di-enhance TomSelect, update lewat API TomSelect
      if (el.tomselect) {
        const ts = el.tomselect;

        ts.clearOptions();

        if (extraFirst) {
          ts.addOption({ value: extraFirst.value, text: extraFirst.text });
        }

        ts.addOptions(options);
        ts.refreshOptions(false);

        // kalau sebelumnya kosong, set ke first option biar gak blank
        if (!ts.getValue() && (extraFirst || options.length)) {
          ts.setValue(extraFirst ? extraFirst.value : options[0].value, true);
        }

        return;
      }

      // ✅ fallback native select biasa
      el.innerHTML = "";

      if (extraFirst) {
        const opt0 = document.createElement("option");
        opt0.value = extraFirst.value;
        opt0.textContent = extraFirst.text;
        el.appendChild(opt0);
      }

      options.forEach((op) => {
        const opt = document.createElement("option");
        opt.value = op.value;
        opt.textContent = op.text;
        el.appendChild(opt);
      });
    };

    // ✅ helper: clone options dari select A ke select B (native / tomselect aman)
    const cloneSelectOptions = (fromEl, toEl, extraFirst = null) => {
      if (!fromEl || !toEl) return;

      // kalau dua-duanya tomselect
      if (fromEl.tomselect && toEl.tomselect) {
        const fromTs = fromEl.tomselect;
        const toTs = toEl.tomselect;

        toTs.clearOptions();

        if (extraFirst) {
          toTs.addOption({ value: extraFirst.value, text: extraFirst.text });
        }

        const opts = Object.values(fromTs.options).map((o) => ({
          value: o.value,
          text: o.text,
        }));

        toTs.addOptions(opts);
        toTs.refreshOptions(false);

        if (!toTs.getValue() && (extraFirst || opts.length)) {
          toTs.setValue(extraFirst ? extraFirst.value : opts[0].value, true);
        }
        return;
      }

      // native fallback
      toEl.innerHTML = "";

      if (extraFirst) {
        const opt0 = document.createElement("option");
        opt0.value = extraFirst.value;
        opt0.textContent = extraFirst.text;
        toEl.appendChild(opt0);
      }

      for (const opt of fromEl.options) {
        const c = opt.cloneNode(true);
        toEl.appendChild(c);
      }

      // kalau belum ada selected value, set default
      if (!toEl.value && toEl.options.length) {
        toEl.value = extraFirst ? String(extraFirst.value) : toEl.options[0].value;
      }
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

    // ===========================
    // ✅ Pendidikan (FIX UTAMA)
    // ===========================
    const elS1 = document.getElementById("master-pendidikan-s1");
    const elS2S3 = document.getElementById("master-pendidikan-s2s3");

    // 1) isi S1 dari pendidikan_umum
    fillSelect(elS1, m.pendidikan_umum, "label_jurusan");

    // 2) isi S2/S3 dengan clone dari S1 (karena opsi sama)
    //    (tetap ada "Tidak Ada / Default" di paling atas)
    cloneSelectOptions(elS1, elS2S3, { value: 0, text: "Tidak Ada / Default" });

    // debug optional
    console.log("masters:", m);
    console.log("pendidikan_umum length:", (m.pendidikan_umum || []).length);
    console.log("S2S3 options length:", elS2S3?.options?.length || 0);

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

let __mastersLoaded = false;

document.addEventListener("shown.bs.modal", async (e) => {
  if (e.target.id !== "modalAddData") return;

  if (!__mastersLoaded) {
    await loadMasters();
    __mastersLoaded = true;
  }
});

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

  // ✅ format angka rupiah dengan titik pemisah ribuan
  const formatRupiah = (value) => {
    if (value === null || value === undefined || value === "") return "-";

    const numStr = String(value).replace(/\D/g, "");
    if (!numStr || numStr === "0") return "-";

    return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

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
    if (d.display_umur_tahun !== null && d.display_umur_tahun !== undefined && d.display_umur_tahun !== "")
      return d.display_umur_tahun;

    if (d.umur_tahun !== null && d.umur_tahun !== undefined && d.umur_tahun !== "")
      return `${d.umur_tahun} Tahun`;

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

  // ✅ AUTO LOCK: ambil id menteri baru dari blade (session)
  const newId = window.__NEW_MENTERI_ID__ || null;
  const autoLockTarget = newId
    ? data.find(d => String(d.id) === String(newId))
    : null;

  // hover template
  const hoverTemplate = (d, color) => `
    <div style="width:320px; background:${color}; border-radius:12px; padding:8px;">
      <div class="hover-glass">
        <div style="display:flex; gap:8px; align-items:center; margin-bottom:6px;">
          <div style="flex:1;">
            <div style="font-weight:900; font-size:1rem;">${disp(d.nama)}</div>
            <div style="font-size:.84rem; color:#475569;">${disp(d.catatan)}</div>
          </div>
          <img src="${d.foto_url || ""}"
               onerror="this.style.display='none'"
               style="width:44px;height:44px;border-radius:10px;object-fit:cover;background:#fff;">
        </div>

        <div class="tooltip-kv">
          <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>
          <div class="k">Umur</div><div class="v">${dispUmur(d)}</div>
          <div class="k">Partai</div><div class="v">${disp(d.partai)}</div>
          <div class="k">Bidang Pendidikan</div><div class="v">${disp(d.pendidikan_s1)}</div>
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

  function renderFullCard(d, color) {
    if (!dockLeft) return;

    dockLeft.style.setProperty("--dock-accent", color);

    dockLeft.innerHTML = `
      <div class="detail-head">
        <img class="detail-photo" src="${d.foto_url || ""}" onerror="this.style.display='none'">
        <button id="btnCloseDetailInner" class="detail-close">×</button>
      </div>

      <div class="detail-title">${disp(d.nama)}</div>
      <div class="detail-sub">${disp(d.catatan)}</div>

      <div class="detail-scroll">
        <div class="kv">
          <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>

          <div class="k">Tempat Lahir</div><div class="v">${pick(d.display_tempat_lahir, d.provinsi_lahir)}</div>
          <div class="k">Tanggal Lahir</div><div class="v">${disp(d.display_tanggal_lahir)}</div>
          <div class="k">Umur (Tahun)</div><div class="v">${dispUmur(d)}</div>

          <div class="k">Provinsi Lahir</div><div class="v">${disp(d.provinsi_lahir)}</div>

          <div class="k">Partai</div><div class="v">${disp(d.partai)}</div>
          <div class="k">Jabatan Rangkap</div><div class="v">${disp(d.jabatan_rangkap)}</div>

          <div class="k">Karir DPR/MPR</div><div class="v">${disp(d.dpr_mpr)}</div>
          <div class="k">Karir Mil/Pol</div><div class="v">${disp(d.militer_polisi)}</div>
          <div class="k">Almamater SMA</div><div class="v">${disp(d.display_almamater_sma)}</div>
          <div class="k">Almamater S1</div><div class="v">${disp(d.display_almamater_s1)}</div>
          <div class="k">Almamater S2</div><div class="v">${disp(d.display_almamater_s2)}</div>
          <div class="k">Almamater S3</div><div class="v">${disp(d.display_almamater_s3)}</div>

          <div class="k">Bidang Pendidikan</div><div class="v">${disp(d.pendidikan_s1)}</div>

          <div class="k">Status Hukum</div><div class="v">${disp(d.display_status_hukum)}</div>
          <div class="k">Kekayaan (Rp)</div><div class="v">${formatRupiah(d.display_kekayaan_rp)}</div>
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
      { label:"Partai",          a:leftD.partai,               b:rightD.partai },
      { label:"Jabatan Rangkap", a:leftD.jabatan_rangkap,      b:rightD.jabatan_rangkap },
      { label:"Karir DPR/MPR",   a:leftD.dpr_mpr,              b:rightD.dpr_mpr },
      { label:"Karir Mil/Pol",   a:leftD.militer_polisi,       b:rightD.militer_polisi },
      { label:"Almamater SMA",   a:leftD.display_almamater_sma,b:rightD.display_almamater_sma },
      { label:"Almamater S1",    a:leftD.display_almamater_s1, b:rightD.display_almamater_s1 },
      { label:"Almamater S2",    a:leftD.display_almamater_s2, b:rightD.display_almamater_s2 },
      { label:"Almamater S3",    a:leftD.display_almamater_s3, b:rightD.display_almamater_s3 },
      { label:"Bidang Pendidikan", a:leftD.pendidikan_s1, b:rightD.pendidikan_s1 },
      { label:"Status Hukum",    a:leftD.display_status_hukum, b:rightD.display_status_hukum },
      { label:"Kekayaan (Rp)",   a:formatRupiah(leftD.display_kekayaan_rp), b:formatRupiah(rightD.display_kekayaan_rp) },
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
                <div class="compare-sub">${disp(leftD.catatan)}</div>
              </div>
            </div>
          </div>

          <div class="compare-mid"></div>

          <div class="compare-side" style="background:${rightColor}">
            <div class="compare-side-inner">
              <img class="compare-photo" src="${rightD.foto_url || ""}" onerror="this.style.display='none'">
              <div>
                <div class="compare-name">${disp(rightD.nama)}</div>
                <div class="compare-sub">${disp(rightD.catatan)}</div>
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
      btnCompare.classList.remove("btn-primary");
      btnCompare.classList.add("btn-compare-active");
    }
  }

  function exitCompareMode() {
    compareMode = false;
    hintCompare && (hintCompare.style.display = "none");
    dockCompare?.classList.add("dock-compare--hidden");

    if (btnCompare) {
      btnCompare.textContent = "Bandingkan Menteri";
      btnCompare.classList.remove("btn-compare-active");
      btnCompare.classList.add("btn-primary");
    }

    nodes
      .attr("fill", (n, i) => getColor(n, i))
      .attr("opacity", 0.9)
      .attr("r", 5);
  }

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

      lockedId = d.id;

      nodes
        .attr("fill", (n, ii) => n.id === lockedId ? "#000000" : getColor(n, ii))
        .attr("r",    (n) => n.id === lockedId ? 8 : 5);

      if (tipInstance) tipInstance.destroy();
      renderFullCard(d, color);
    });

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

  btnCompare?.addEventListener("click", () => {
    if (!compareMode) enterCompareMode();
    else exitCompareMode();
  });

  btnExitCompare?.addEventListener("click", exitCompareMode);
  btnResetCompare?.addEventListener("click", resetCompare);

  btnCloseDetail?.addEventListener("click", closeDetailDock);

  if (autoLockTarget) {
    const i = data.indexOf(autoLockTarget);
    const color = getColor(autoLockTarget, i);

    lockedId = autoLockTarget.id;

    nodes
      .attr("fill", (n, ii) => n.id === lockedId ? "#000000" : getColor(n, ii))
      .attr("r", (n) => (n.id === lockedId ? 8 : 5));

    renderFullCard(autoLockTarget, color);

    const tx = x(+autoLockTarget.umap_x);
    const ty = y(+autoLockTarget.umap_y);
    svg.transition()
      .duration(350)
      .call(
        zoom.transform,
        d3.zoomIdentity
          .translate(width / 2 - tx, height / 2 - ty)
          .scale(1.6)
      );
  }
})();
