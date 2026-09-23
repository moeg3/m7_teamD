// 1. キャンバスの初期設定
const stage = new Konva.Stage({
    container: 'canvas-container',
    width: 600,
    height: 500
});
const layer = new Konva.Layer();
stage.add(layer);

// ★追加・変更1：基準となる「1mmあたりのピクセル数」を定義する
// （後でカードケースや画面サイズに合わせてこの数値を動的に変更します。今は仮に「1mm = 4ピクセル」とします）
const PX_PER_MM = 4; 

// ★変更2：Transformerの設定を変更し、サイズ変更ハンドルを消す
const tr = new Konva.Transformer({
    enabledAnchors: [], // 四隅の四角（リサイズ用のアンカー）を空にして非表示にする
    rotationSnaps: [0, 45, 90, 135, 180, 225, 270, 315] // おまけ：45度ずつピタッと止まるようにすると使いやすいです
});
layer.add(tr);

stage.on('click tap', function (e) {
    if (e.target === stage) {
        tr.nodes([]);
    }
});

let draggedColor = null;

// ★追加・変更3：パーツが持つ「実際のミリ数」をHTMLから取得できるようにする
let draggedSizeMm = null; 

document.querySelectorAll('.drag-item').forEach(item => {
    item.addEventListener('dragstart', (e) => {
        draggedColor = e.target.getAttribute('data-color');
        // HTML側に追加する data-size-mm 属性からサイズ（mm）を取得する。無ければ仮に10mmとする
        draggedSizeMm = parseFloat(e.target.getAttribute('data-size-mm')) || 10; 
    });
});

const container = document.getElementById('canvas-container');
container.addEventListener('dragover', (e) => { e.preventDefault(); });

container.addEventListener('drop', (e) => {
    e.preventDefault();
    stage.setPointersPositions(e);
    const pointerPosition = stage.getPointerPosition();

    // ★追加・変更4：「ミリ数 × 1mmのピクセル数」で、画面上の実際の表示サイズ（ピクセル）を計算する
    const displaySizePx = draggedSizeMm * PX_PER_MM;

    const newShape = new Konva.Rect({
        x: pointerPosition.x - (displaySizePx / 2),
        y: pointerPosition.y - (displaySizePx / 2),
        width: displaySizePx,
        height: displaySizePx,
        fill: draggedColor,
        draggable: true,
        // 回転の中心を図形の真ん中に設定する（これをしないと左上を軸に回ってしまいます）
        offsetX: displaySizePx / 2,
        offsetY: displaySizePx / 2
    });

    newShape.on('click tap', function () {
        tr.nodes([this]);
    });

    layer.add(newShape);
});