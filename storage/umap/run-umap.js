// storage/umap/run-umap.js
import fs from "fs";
import { UMAP } from "umap-js";

// input dari Laravel berupa JSON berisi fitur numerik
const inputPath = process.argv[2];
const outputPath = process.argv[3];

const rows = JSON.parse(fs.readFileSync(inputPath, "utf8"));

// rows: [{id:1, features:[...]}]
const features = rows.map(r => r.features);

const umap = new UMAP({
  nComponents: 2,
  nNeighbors: 10,
  minDist: 0.1,
  randomState: 42, // biar stabil
});

const embedding = umap.fit(features);

// output: [{id, umap_x, umap_y}]
const out = rows.map((r, i) => ({
  id: r.id,
  umap_x: embedding[i][0],
  umap_y: embedding[i][1],
}));

fs.writeFileSync(outputPath, JSON.stringify(out));
console.log("UMAP DONE. rows:", out.length);
