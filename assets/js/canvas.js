// 1. キャンバスの初期設定
const stage = new Konva.Stage({
    container: 'canvas-container', // HTMLのdivのID
    width: 800,
    height: 500
});

const layer = new Konva.Layer();
stage.add(layer);

// 2. ドラッグ可能な赤い四角形（ダミーパーツ）を作る
const dummyPart = new Konva.Rect({
    x: 50,
    y: 50,
    width: 100,
    height: 100,
    fill: 'red',
    draggable: true // これだけでドラッグ可能になります
});

// 3. レイヤーに追加して描画する
layer.add(dummyPart);
layer.draw();