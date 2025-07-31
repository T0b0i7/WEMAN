<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="" />
    <title>404 Animation</title>
  </head>
  <style>
    *,
*::before,
*::after {
  margin: 0;
  padding: 0;
  border: 0;
  box-sizing: border-box;
}

*:focus {
  outline: none;
}

body {
  height: 100vh;
  height: 100dvh;
  overflow: hidden;
}

canvas {
  touch-action: none;
}
  </style>
  <body>
    <script>
      const imageSrc =
        "data:../assets/images/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAAAAD/4QMwaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA5LjEtYzAwMiA3OS5hNmE2Mzk2OGEsIDIwMjQvMDMvMDYtMTE6NTI6MDUgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCAyNS4xMSAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjI5NzRDODY3MEUzMTFFRjkzQzQ5OUVFNjc0OTI0NDQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjI5NzRDODc3MEUzMTFFRjkzQzQ5OUVFNjc0OTI0NDQiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2Mjk3NEM4NDcwRTMxMUVGOTNDNDk5RUU2NzQ5MjQ0NCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo2Mjk3NEM4NTcwRTMxMUVGOTNDNDk5RUU2NzQ5MjQ0NCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pv/uAA5BZG9iZQBkwAAAAAH/2wCEABsaGikdKUEmJkFCLy8v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";
    </script>

    <script>
      "use strict";
console.clear();

let w = 0;
let h = 0;

let renderIteration = 0;
let animationFrame = null;
let isTouchDevice = false;

const dpr = Math.max(1, window.devicePixelRatio);
const canvas = document.createElement("canvas");
const context = canvas.getContext("2d", {
  alpha: false,
  willReadFrequently: true,
});

let imageSource = null;
const canvasSource = document.createElement("canvas");
const contextSource = canvasSource.getContext("2d", {
  alpha: false,
  willReadFrequently: true,
});

const border = { left: 0, top: 0, right: w, bottom: h };
const center = { x: 0, y: 0 };

let imageData = null;
let data = null;

let imageDataSource = null;
let dataSource = null;

const sectorTiles = 20;
let sectorSizeX = 0;
let sectorSizeY = 0;
let sectors = new Map();
let sectorsX = 0;
let sectorsY = 0;

let boidHolder = [];
let boidCount = 2048;
let boidIndex = 0;
const boidRadius = 6;
const boidDiameter = boidRadius * 2;
const boidDiameterSquared = boidDiameter * boidDiameter;
let boidSpeed = 1;
const boidSpeedMin = 0;
const boidSpeedMax = 2.5;
const boidRadialSpeed = Math.PI / 60;
const boidVision = 50;
const boidVisionSquared = boidVision * boidVision;

const boidRadialSpeedMin = Math.PI / 10;
const boidRadialSpeedMax = Math.PI / 60;
const boidRepulsionDistance = 100;
const boidRepulsionDistanceSquared =
  boidRepulsionDistance * boidRepulsionDistance;

let boidCounterMax = 0;
let boidOffsetMax = 0;

let pointerInitialPos = {
  x: -boidRepulsionDistance,
  y: -boidRepulsionDistance,
};
let pointer = { x: 0, y: 0 };
let pointerPos = { x: 0, y: 0 };
let pointerDownButton = -1;
let pointerActive = false;

function init() {
  isTouchDevice =
    "ontouchstart" in window ||
    navigator.maxTouchPoints > 0 ||
    navigator.msMaxTouchPoints > 0;


  if (isTouchDevice === true) {
    boidCount *= 0.75;

    canvas.addEventListener("touchmove", cursorMoveHandler, false);
    canvas.addEventListener("touchend", cursorLeaveHandler, false);
    canvas.addEventListener("touchcancel ", cursorLeaveHandler, false);
  } else {
    canvas.addEventListener("pointermove", cursorMoveHandler, false);
    canvas.addEventListener("pointerdown", cursorDownHandler, false);
    canvas.addEventListener("pointerup", cursorUpHandler, false);
    canvas.addEventListener("pointerleave", cursorLeaveHandler, false);
  }


  document.body.appendChild(canvas);

  document.body.appendChild(canvasSource);


  imageSource = new Image();
  imageSource.src = imageSrc;
  imageSource.onload = () => {
    window.addEventListener("resize", onResize, false);

    restart();
  };
}

function onResize(event) {
  restart();
}

function restart() {
  const innerWidth =
    window.innerWidth ||
    document.documentElement.clientWidth ||
    document.body.clientWidth;
  const innerHeight =
    window.innerHeight ||
    document.documentElement.clientHeight ||
    document.body.clientHeight;


  w = innerWidth;
  h = innerHeight;

  canvas.width = w * dpr;
  canvas.height = h * dpr;

  canvasSource.width = w * dpr;
  canvasSource.height = h * dpr;


  const imageAspectRatio = imageSource.width / imageSource.height;
  const canvasAspectRatio = w / h;

  let drawWidth = 0;
  let drawHeight = 0;

  if (canvasAspectRatio > imageAspectRatio) {
    drawWidth = w;
    drawHeight = w / imageAspectRatio;
  } else {
    drawHeight = h;
    drawWidth = h * imageAspectRatio;
  }

  const offsetX = (w - drawWidth) * 0.5;
  const offsetY = (h - drawHeight) * 0.5;

  contextSource.drawImage(imageSource, offsetX, offsetY, drawWidth, drawHeight);


  imageData = context.getImageData(0, 0, w, h);
  data = imageData.data;

  imageDataSource = contextSource.getImageData(0, 0, w, h);
  dataSource = imageDataSource.data;


  sectorSizeX = Math.floor(w / sectorTiles);
  sectorSizeY = Math.floor(h / sectorTiles);
  sectors = new Map();
  sectorsX = Math.ceil(w / sectorSizeX);
  sectorsY = Math.ceil(h / sectorSizeY);


  border.right = w - 1;
  border.bottom = h - 1;

  center.x = w / 2;
  center.y = h / 2;

  pointerPos.x = pointerInitialPos.x;
  pointerPos.y = pointerInitialPos.y;
  pointer.x = pointerInitialPos.x;
  pointer.y = pointerInitialPos.y;

  boidCounterMax = Math.round(Math.min(w, h) / 48);

  removeBoids();
  createBoids();

  if (animationFrame != null) {
    cancelAnimFrame(animationFrame);
  }

  render();
}

function removeBoids() {
  boidIndex = 0;
  boidHolder = [];
}

function createBoids() {
  for (let i = 0; i < boidCount; i++) {
    const boid = createBoid(0, 0, 0, i);

    boidHolder.push(boid);
  }
}

function createBoid(
  x,
  y,
  heading = Math.random() * 2 * Math.PI - Math.PI,
  id = 0
) {
  const boid = {};

  resetBoid(boid, x, y, heading);

  boid.id = id;

  return boid;
}

function activateBoids() {
  for (let i = 0; i < 16; i++) {
    activateBoid();
  }
}

function activateBoid() {
  const angle = Math.random() * Math.PI * 2;

  const distance = Math.random() * (Math.min(w, h) * 0.5);

  const x = center.x + Math.cos(angle) * distance;
  const y = center.y + Math.sin(angle) * distance;

  const color = getPixel(x | 0, y | 0);

  if ((color.r < 55 && color.g < 55 && color.b < 55) || distance <= 100) {
    const boidRandomIndex = Math.floor(Math.random() * boidCount);
    const boid = boidHolder[boidRandomIndex];

    if (boid.life <= 0) {
      const heading = Math.atan2(center.y - y, center.x - x) + Math.PI;

      resetBoid(boid, x, y, heading);

      boid.life = Math.floor(Math.random() * 1024) + 128;
      boid.lifetime = boid.life;
      boid.lifetimeActivateNeighborSearch =
        boid.lifetime - boid.lifetime * 0.15;
    }
  }
}

function resetBoid(
  boid,
  x,
  y,
  heading = Math.random() * 2 * Math.PI - Math.PI
) {
  boid.x = x;
  boid.y = y;
  boid.ox = x;
  boid.oy = y;

  boid.key = "";
  boid.oldKey = "";
  boid.heading = heading;

  const counterMaxRandomOffset = Math.random() > 0.5 ? Math.random() * 10 : 0;

  boid.counterMax = boidCounterMax + counterMaxRandomOffset;
  boid.counter = Math.floor(Math.random() * boid.counterMax);

  boid.radialSpeed = 0;
  boid.speed = 0;
  boid.color = {
    r: 0,
    g: 0,
    b: 0,
  };
  boid.newColor = {
    r: 0,
    g: 0,
    b: 0,
  };
  boid.oldColor = {
    r: 0,
    g: 0,
    b: 0,
  };

  boid.life = 0;
  boid.lifetime = 0;
  boid.lifetimeActivateNeighborSearch = 0;
}
function getSectorKey(x, y) {
  const sectorX = Math.floor(x / sectorSizeX);
  const sectorY = Math.floor(y / sectorSizeY);

  return `${sectorX},${sectorY}`;
}

function addBoidToSector(boid) {
  const key = boid.key;

  if (sectors.has(key) === false) {
    sectors.set(key, new Set());
  }

  sectors.get(key).add(boid);
}

function removeBoidFromSector(boid) {
  const key = boid.oldKey;

  if (sectors.has(key) === true) {
    const sector = sectors.get(key);

    sector.delete(boid);
  }
}

function getNeighbors(boid) {
  const neighbors = [];
  const sectorKey = boid.key;
  const [sectorX, sectorY] = sectorKey.split(",").map(Number);

  for (let dx = -1; dx <= 1; dx++) {
    for (let dy = -1; dy <= 1; dy++) {
      const neighborX = (sectorX + dx + sectorsX) % sectorsX;
      const neighborY = (sectorY + dy + sectorsY) % sectorsY;
      const neighborKey = `${neighborX},${neighborY}`;

      if (sectors.has(neighborKey)) {
        const sectorBoids = sectors.get(neighborKey);

        sectorBoids.forEach((otherBoid) => {
          if (
            boid.id !== otherBoid.id &&
            getDistanceSquared(boid, otherBoid, w, h) < boidVisionSquared
          ) {
            neighbors.push(otherBoid);
          }
        });
      }
    }
  }

  return neighbors;
}

function getDistanceSquared(boid1, boid2, width, height) {
  const x0 = Math.min(boid1.x, boid2.x);
  const x1 = Math.max(boid1.x, boid2.x);
  const y0 = Math.min(boid1.y, boid2.y);
  const y1 = Math.max(boid1.y, boid2.y);

  const dx = Math.min(x1 - x0, x0 + width - x1);
  const dy = Math.min(y1 - y0, y0 + height - y1);

  return dx * dx + dy * dy;
}

function lerp(a, b, t) {
  return a + (b - a) * t;
}

function wrap(value, min, max) {
  const range = max - min;

  if (value >= min && value < max) {
    return value;
  }

  return ((((value - min) % range) + range) % range) + min;
}

function clamp(value, limit) {
  return Math.min(limit, Math.max(-limit, value));
}

function meanAngle(angle1, angle2) {
  const cos = Math.cos(angle1);
  const sin = Math.sin(angle1);

  const sumX = cos * 3 + Math.cos(angle2);
  const sumY = sin * 3 + Math.sin(angle2);

  return Math.atan2(sumY * 0.25, sumX * 0.25);
}

function clearImageData(fadeAmount = 1) {
  for (let i = 0, l = data.length; i < l; i += 4) {
    data[i] = Math.max(0, data[i] - fadeAmount);
    data[i + 1] = Math.max(0, data[i + 1] - fadeAmount);
    data[i + 2] = Math.max(0, data[i + 2] - fadeAmount);
    data[i + 3] = 255;
  }
}

function setPixel(x, y, r, g, b, a) {
  const i = (x + y * imageData.width) * 4;
  data[i] = r; data[i + 1] = g;
  data[i + 2] = b;
  data[i + 3] = a;
}

function getPixel(x, y) {
  const i = (x + y * imageDataSource.width) * 4;
  const r = dataSource[i];
  const g = dataSource[i + 1];
  const b = dataSource[i + 2];
  return { r, g, b };
}

function drawLine(x1, y1, x2, y2, r, g, b, a) {
  const dx = Math.abs(x2 - x1);
  const dy = Math.abs(y2 - y1);

  const sx = x1 < x2 ? 1 : -1;
  const sy = y1 < y2 ? 1 : -1;

  let err = dx - dy;

  let lx = x1;
  let ly = y1;

  while (true) {
    if (
      lx > border.left &&
      lx < border.right &&
      ly > border.top &&
      ly < border.bottom
    ) {
      setPixel(lx, ly, r, g, b, a);
    }

    if (lx === x2 && ly === y2) {
      break;
    }

    const e2 = 2 * err;

    if (e2 > -dx) {
      err -= dy;
      lx += sx;
    }

    if (e2 < dy) {
      err += dx;
      ly += sy;
    }
  }
}

function draw() {
  pointer.x += (pointerPos.x - pointer.x) / 10;
  pointer.y += (pointerPos.y - pointer.y) / 10;

  for (let i = 0, l = boidHolder.length; i < l; i++) {
    const boid = boidHolder[i];

    if (
      boid.x > border.right ||
      boid.x < border.left ||
      boid.y > border.bottom ||
      boid.y < border.top
    ) {
      boid.life = 0;
    }

    boid.life--;

    if (boid.life <= 0) {
      continue;
    }

    boid.oldKey = getSectorKey(boid.x, boid.y);
    boid.ox = boid.x;
    boid.oy = boid.y;

    boid.counter++;

    if (boid.counter > boid.counterMax) {
      boid.counter = 0;

      boid.oldColor.r = boid.color.r;
      boid.oldColor.g = boid.color.g;
      boid.oldColor.b = boid.color.b;

      boid.newColor = getPixel(boid.x | 0, boid.y | 0);
    }

    const t = boid.counter / boid.counterMax;

    boid.color.r = Math.round(lerp(boid.oldColor.r, boid.newColor.r, t));
    boid.color.g = Math.round(lerp(boid.oldColor.g, boid.newColor.g, t));
    boid.color.b = Math.round(lerp(boid.oldColor.b, boid.newColor.b, t));

    let alert = false;

    const dx = pointer.x - boid.x;
    const dy = pointer.y - boid.y;

    if (pointerActive === true) {
      const distSquared = dx * dx + dy * dy;

      alert = distSquared <= boidRepulsionDistanceSquared;
    }

    boid.speed += alert ? 0.5 : -0.025;
    boid.speed = Math.max(boidSpeedMin, Math.min(boid.speed, boidSpeedMax));

    boid.radialSpeed += alert ? 0.5 : -0.025;
    boid.radialSpeed = Math.min(
      boidRadialSpeedMin,
      Math.max(boid.radialSpeed, boidRadialSpeedMax)
    );

    const heading = () => {
      let delta = wrap(target - boid.heading, -Math.PI, Math.PI);

      delta = clamp(delta, boid.radialSpeed);

      boid.heading = wrap(boid.heading + delta, -Math.PI, Math.PI);
    };

    let target = 0;

    let neighbors = [];
    let neighborsCount = 0;

    if (boid.life < boid.lifetimeActivateNeighborSearch) {
      neighbors = getNeighbors(boid);
      neighborsCount = neighbors.length;
    }

    if (neighborsCount > 0) {
      let meanhx = 0;
      let meanhy = 0;

      let meanx = 0;
      let meany = 0;

      let mindist = boidDiameterSquared;
      let min = null;

      for (let j = 0, m = neighborsCount; j < m; j++) {
        const b = neighbors[j];

        meanhx += Math.cos(b.heading);
        meanhy += Math.sin(b.heading);

        meanx += b.x;
        meany += b.y;

        const dist = getDistanceSquared(boid, b, w, h);

        if (dist < mindist) {
          mindist = dist;
          min = b;
        }
      }

      if (alert) {
        target = Math.atan2(dy, dx) + Math.PI;
      } else {
        if (min) {
          target = Math.atan2(boid.y - min.y, boid.x - min.x);
        } else {
          meanhx /= neighborsCount;
          meanhy /= neighborsCount;

          meanx /= neighborsCount;
          meany /= neighborsCount;

          const meanh = Math.atan2(meanhy, meanhx);
          const center = Math.atan2(meany - boid.y, meanx - boid.x);

          target = meanAngle(meanh, center);
        }
      }

      heading();
    } else {
      if (alert) {
        target = Math.atan2(dy, dx) + Math.PI;

        heading();
      } else {
        const randomNoise = (Math.random() - 0.5) * 0.22;

        boid.heading += randomNoise;
        boid.heading = wrap(boid.heading, -Math.PI, Math.PI);
      }
    }

    boid.x += Math.cos(boid.heading) * (boidSpeed + boid.speed);
    boid.y += Math.sin(boid.heading) * (boidSpeed + boid.speed);

    boid.key = getSectorKey(boid.x, boid.y);

    if (boid.oldKey !== boid.key) {
      removeBoidFromSector(boid);
      addBoidToSector(boid);
    }

    if (boidSpeed + boid.speed <= 1) {
      setPixel(
        boid.x | 0,
        boid.y | 0,
        boid.color.r,
        boid.color.g,
        boid.color.b,
        255
      );
    } else {
      const dx = Math.abs(boid.x - boid.ox);
      const dy = Math.abs(boid.y - boid.oy);

      if (dx < center.x && dy < center.y) {
        drawLine(
          boid.x | 0,
          boid.y | 0,
          boid.ox | 0,
          boid.oy | 0,
          boid.color.r,
          boid.color.g,
          boid.color.b,
          255
        );
      }
    }
  }

  if (pointerDownButton === 0) {
    for (let i = 0; i < data.length; i += 4) {
      const r = data[i];
      const g = data[i + 1];
      const b = data[i + 2];

      const grayscale = 0.3 * r + 0.59 * g + 0.11 * b;

      data[i] = grayscale;
      data[i + 1] = grayscale;
      data[i + 2] = grayscale;
    }
  }
}

function render(timestamp) {
  activateBoids();

  if (renderIteration % 5 === 0 && pointerDownButton < 1) {
    clearImageData();
  }

  if (pointerDownButton === 1) {
    clearImageData(5);
  }

  draw();

  context.putImageData(imageData, 0, 0);

  renderIteration++;

  animationFrame = requestAnimFrame(render);
}

window.requestAnimFrame = (() => {
  return (
    window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.msRequestAnimationFrame
  );
})();

window.cancelAnimFrame = (() => {
  return window.cancelAnimationFrame || window.mozCancelAnimationFrame;
})();

function cursorDownHandler(event) {
  pointerDownButton = event.button;
}

function cursorUpHandler(event) {
  pointerDownButton = -1;
}

function cursorLeaveHandler(event) {
  pointerPos = { x: pointerInitialPos.x, y: pointerInitialPos.y };
  pointerDownButton = -1;
  pointerActive = false;
}

function cursorMoveHandler(event) {
  pointerPos = getCursorPosition(canvas, event);
  pointerActive = true;
}

function getCursorPosition(element, event) {
  const rect = element.getBoundingClientRect();
  const position = { x: 0, y: 0 };

  if (event.type === "mousemove" || event.type === "pointermove") {
    position.x = event.pageX - rect.left;
    position.y = event.pageY - rect.top;
  } else if (event.type === "touchmove") {
    position.x = event.touches[0].pageX - rect.left;
    position.y = event.touches[0].pageY - rect.top;
  }

  return position;
}

document.addEventListener("DOMContentLoaded", () => {
  init();
});


document.addEventListener('contextmenu', function (e) {
    e.preventDefault(); // Désactive le clic droit
});

document.addEventListener('keydown', function (e) {
    // Désactive Ctrl+U, Ctrl+Shift+I, F12, etc.
    if (e.ctrlKey && e.key === 'u' || e.ctrlKey && e.key === 'U' || e.key === 'F12' || e.ctrlKey && e.shiftKey && e.key === 'I') {
        e.preventDefault();
        alert("L'accès au code source est désactivé.");
    }
});

    </script>
  </body>
</html>
