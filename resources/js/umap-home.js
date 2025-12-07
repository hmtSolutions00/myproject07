import * as d3 from "d3";
import tippy from "tippy.js";
import { renderCompareHTML } from "./umap-compare";

(async function () {
  const canvas = document.getElementById("umap-canvas");
  if (!canvas) return;

  const dockLeft = document.getElementById("dockLeft");
  const comparePanel = document.getElementById("comparePanel");
  const compareBody = document.getElementById("compareBody");
  const compareBanner = document.getElementById("compareBanner");
  const compareStepText = document.getElementById("compareStepText");
  const modeHint = document.getElementById("modeHint");

  const btnCompareMode = document.getElementById("btnCompareMode");
  const btnCompareExit = document.getElementById("btnCompareExit");
  const btnCompareReset = document.getElementById("btnCompareReset");
  const btnCompareClose = document.getElementById("btnCompareClose");

  const btnInsertMode = document.getElementById("btnInsertMode");
  const insertPanel = document.getElementById("insertPanel");
  const btnInsertClose = document.getElementById("btnInsertClose");
  const btnInsertCancel = document.getElementById("btnInsertCancel");

  let mode = "default"; // default | compare | insert
  let lockedId = null;
  let compareSelected = []; // store data objects
  let tipInstance = null;

  const width = canvas.clientWidth;
  const height = canvas.clientHeight;

  // SVG root
  const svg = d3.select(canvas)
    .append("svg")
    .attr("width", width)
    .attr("height", height);

  const g = svg.append("g").attr("class", "zoom-layer");

  // Fetch data
  const res = await fetch("/api/menteri");
  const raw = await res.json();

  // ✅ hanya data yang punya embedding valid
  const data = raw.filter(d =>
    Number.isFinite(+d.umap_x) && Number.isFinite(+d.umap_y)
  );

  if (!data.length) {
    canvas.innerHTML = "<div class='p-5 text-center text-muted'>Data UMAP belum tersedia</div>";
    return;
  }

  // Scale coords
  const x = d3.scaleLinear()
    .domain(d3.extent(data, d => +d.umap_x)).nice()
    .range([30, width - 30]);

  const y = d3.scaleLinear()
    .domain(d3.extent(data, d => +d.umap_y)).nice()
    .range([height - 30, 30]);

  // fallback palette (avoid black)
  const palette = [
    "#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF",
    "#FF9F40","#00C49A","#F15BB5","#00BBF9","#9BDEAC"
  ];

  const sanitizeColor = (hex) => {
    if (!hex) return null;
    const c = String(hex).toLowerCase().trim();
    if (c === "#000" || c === "#000000" || c === "black") return null;
    return c;
  };

  const getColor = (d, i) => sanitizeColor(d.color_code) || palette[i % palette.length];

  // helper display fallback
  const disp = (v) => (v === null || v === undefined || v === "") ? "-" : v;

  // ✅ umur prioritas dari detail kalau ada
  const dispUmur = (d) => {
    // kalau API punya display_umur_tahun → pakai
    if (d.display_umur_tahun) return d.display_umur_tahun;
    // fallback angka mentah
    if (d.umur_tahun !== null && d.umur_tahun !== undefined && d.umur_tahun !== "") {
      return `${d.umur_tahun} Tahun`;
    }
    // terakhir fallback kategori umur
    return disp(d.umur);
  };

  // Tooltip template (ringkas)
  const hoverTemplate = (d, color) => `
    <div class="hover-glass" style="width:300px;">
      <div style="
        background:${color};
        padding:8px;
        border-radius:10px;
        position:relative;
        overflow:hidden;
      ">
        <div style="background:rgba(255,255,255,.7);border-radius:8px;padding:8px;">
          <div class="d-flex gap-2 align-items-center mb-2">
            <div class="flex-grow-1">
              <div style="font-weight:900;font-size:1rem;">${disp(d.nama)}</div>
              <div style="font-size:.85rem;color:#334155">${disp(d.kementerian)}</div>
            </div>
            <img src="${d.foto_url || ''}" onerror="this.style.display='none'"
              style="width:44px;height:44px;border-radius:10px;object-fit:cover;background:#fff;">
          </div>

          <div class="kv" style="font-size:.85rem;">
            <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>
            <div class="k">Umur</div><div class="v">${dispUmur(d)}</div>
            <div class="k">Partai</div><div class="v">${disp(d.partai)}</div>
            <div class="k">Bidang S1</div><div class="v">${disp(d.pendidikan_s1)}</div>
          </div>

          <div class="mt-2" style="font-size:.75rem;color:#475569;">
            ${mode === "compare"
              ? (compareSelected.length === 0
                  ? "Klik untuk pilih menteri pertama"
                  : compareSelected.length === 1
                    ? "Klik untuk pilih menteri kedua"
                    : "2 menteri sudah dipilih")
              : "Klik titik untuk kunci & scroll detail"}
          </div>
        </div>
      </div>
    </div>
  `;

  function showHover(d, node, color){
    if (mode === "default" && lockedId && lockedId !== d.id) return;

    if (tipInstance) tipInstance.destroy();

    tipInstance = tippy(node, {
      content: hoverTemplate(d, color),
      allowHTML: true,
      interactive: true,
      trigger: "manual",
      placement: "right",
      theme: "umap-hover",
    });
    tipInstance.show();
  }

  // ✅ DETAIL DOCK FULL
  function renderDock(d, color){
    dockLeft?.style.setProperty("--dock-accent", color);

    dockLeft.innerHTML = `
      <img class="detail-photo" src="${d.foto_url || ''}" onerror="this.style.display='none'">
      <div class="detail-title">${disp(d.nama)}</div>
      <div class="detail-sub">${disp(d.kementerian)}</div>

      <div class="detail-scroll">
        <div class="kv">
          <div class="k">Jenis Kelamin</div><div class="v">${disp(d.jenis_kelamin)}</div>
          <div class="k">Tempat Lahir</div><div class="v">${disp(d.display_tempat_lahir)}</div>
          <div class="k">Tanggal Lahir</div><div class="v">${disp(d.display_tanggal_lahir)}</div>
          <div class="k">Umur (Tahun)</div><div class="v">${dispUmur(d)}</div>

          <div class="k">Provinsi Lahir</div><div class="v">${disp(d.provinsi_lahir)}</div>
          <div class="k">Umur Kategori</div><div class="v">${disp(d.umur_tahun)}</div>

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

      <button class="btn-cta" id="btnDockClose">Tutup Detail</button>
    `;

    dockLeft.classList.remove("hidden");

    const btnDockClose = document.getElementById("btnDockClose");
    btnDockClose?.addEventListener("click", () => {
      dockLeft.classList.add("hidden");
      lockedId = null;
      refreshDots();
    });
  }

  function setMode(next){
    mode = next;

    compareBanner?.classList.add("hidden");
    comparePanel?.classList.add("hidden");
    insertPanel?.classList.add("hidden");
    dockLeft?.classList.add("hidden");
    lockedId = null;

    if (next !== "compare") compareSelected = [];

    if (next === "default"){
      modeHint.textContent = "Hover titik → lihat ringkas. Klik titik → kunci & scroll detail.";
      btnCompareMode?.classList.remove("btn-primary");
      btnCompareMode?.classList.add("btn-outline");
    }
    if (next === "compare"){
      modeHint.textContent = "Mode Bandingkan aktif. Pilih 2 menteri di peta.";
      compareBanner?.classList.remove("hidden");
      btnCompareMode?.classList.add("btn-primary");
      btnCompareMode?.classList.remove("btn-outline");
      compareStepText.textContent = "Mode Bandingkan: pilih menteri pertama.";
    }
    if (next === "insert"){
      modeHint.textContent = "Isi form insert di bawah. Setelah submit, kembali ke peta.";
      insertPanel?.classList.remove("hidden");
    }

    refreshDots();
  }

  // draw dots
  const nodes = g.selectAll("circle")
    .data(data)
    .enter()
    .append("circle")
    .attr("cx", d => x(+d.umap_x))
    .attr("cy", d => y(+d.umap_y))
    .attr("r", 5)
    .attr("fill", (d,i) => getColor(d,i))
    .attr("opacity", 0.9)
    .style("cursor","pointer");

  function refreshDots(){
    nodes
      .attr("fill", (n,i) => {
        if (mode === "compare" && compareSelected.find(s => s.id === n.id)) return "#000000";
        if (mode === "default" && lockedId === n.id) return "#000000";
        return getColor(n,i);
      })
      .attr("r", (n) => {
        if (mode === "compare" && compareSelected.find(s => s.id === n.id)) return 8;
        if (mode === "default" && lockedId === n.id) return 8;
        return 5;
      });
  }

  nodes
    .on("mouseenter", function (event, d) {
      const color = getColor(d, data.indexOf(d));
      d3.select(this).attr("r", () => {
        if (mode === "compare" && compareSelected.find(s => s.id === d.id)) return 9;
        if (mode === "default" && lockedId === d.id) return 9;
        return 7;
      });
      showHover(d, this, color);
    })
    .on("mouseleave", function () {
      refreshDots();
      if (tipInstance) tipInstance.hide();
    })
    .on("click", function (event, d) {
      const color = getColor(d, data.indexOf(d));

      if (mode === "default") {
        lockedId = d.id;
        if (tipInstance) tipInstance.destroy();
        renderDock(d, color);
        refreshDots();
        return;
      }

      if (mode === "compare") {
        if (compareSelected.find(s => s.id === d.id)) return;

        if (compareSelected.length < 2) {
          compareSelected.push(d);
          refreshDots();

          if (compareSelected.length === 1) {
            compareStepText.textContent = "Mode Bandingkan: pilih menteri kedua.";
          }

          if (compareSelected.length === 2) {
            compareStepText.textContent = "2 menteri dipilih. Lihat panel perbandingan.";
            comparePanel.classList.remove("hidden");
            compareBody.innerHTML = renderCompareHTML(compareSelected[0], compareSelected[1]);
          }
        }
      }
    });

  // zoom & pan follow mouse
  const zoom = d3.zoom()
    .scaleExtent([0.7, 8])
    .on("zoom", (event) => {
      g.attr("transform", event.transform);
    });

  svg.call(zoom);
  svg.on("dblclick.zoom", null);
  svg.on("dblclick", () => {
    svg.transition().duration(300).call(zoom.transform, d3.zoomIdentity);
  });

  // buttons
  btnCompareMode?.addEventListener("click", () => setMode(mode === "compare" ? "default" : "compare"));
  btnCompareExit?.addEventListener("click", () => setMode("default"));
  btnCompareClose?.addEventListener("click", () => comparePanel.classList.add("hidden"));
  btnCompareReset?.addEventListener("click", () => {
    compareSelected = [];
    comparePanel.classList.add("hidden");
    compareStepText.textContent = "Mode Bandingkan: pilih menteri pertama.";
    refreshDots();
  });

  btnInsertMode?.addEventListener("click", () => setMode(mode === "insert" ? "default" : "insert"));
  btnInsertClose?.addEventListener("click", () => setMode("default"));
  btnInsertCancel?.addEventListener("click", () => setMode("default"));

  setMode("default");
})();
