// storage/umap/run-umap.js
import fs from "fs";
import { UMAP } from "umap-js";

const inputPath = process.argv[2];
const outputPath = process.argv[3];
const rows = JSON.parse(fs.readFileSync(inputPath, "utf8"));

// Langsung pakai features dari Laravel (sudah urut sesuai cobaUmap.js)
const features = rows.map(r => r.features);

// Config PERSIS seperti cobaUmap.js
const umap = new UMAP({
  nComponents: 2,
  nNeighbors: 10,
  minDist: 0.1,
  randomState: 42,  // Untuk hasil konsisten
});

// Method seperti cobaUmap.js
const nEpochs = umap.initializeFit(features);
for (let i = 0; i < nEpochs; i++) {
  umap.step();
}
const embedding = umap.getEmbedding();

// Output
const out = rows.map((r, i) => ({
  id: r.id,
  umap_x: embedding[i][0],
  umap_y: embedding[i][1],
}));

fs.writeFileSync(outputPath, JSON.stringify(out));
console.log("UMAP DONE. rows:", out.length);