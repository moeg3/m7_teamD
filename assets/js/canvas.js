const containerElement = document.getElementById('canvas-container');

// 1. キャンバスの初期設定
const stage = new Konva.Stage({
    container: 'canvas-container',
    width: containerElement.offsetWidth || 600, 
    height: containerElement.offsetHeight || 500
});
const layer = new Konva.Layer();
stage.add(layer);

const PX_PER_MM = 0.5; // 変更必要 標準は4
const tr = new Konva.Transformer({
    enabledAnchors: [], 
    rotationSnaps: [0, 45, 90, 135, 180, 225, 270, 315]
});
layer.add(tr);

stage.on('click tap', function (e) {
    if (e.target === stage) tr.nodes([]);
});

// 2. ドラッグ開始：ブラウザのポケットに直接データをねじ込む
document.querySelectorAll('.drag-item').forEach(item => {
    item.addEventListener('dragstart', (e) => {
        const dataToTransfer = JSON.stringify({
            src: e.currentTarget.dataset.imagePath,
            sizeMm: parseFloat(e.currentTarget.dataset.sizeMm) || 10,
            partId: e.currentTarget.dataset.partId
        });

        console.log('ドラッグデータ:', dataToTransfer);

        e.dataTransfer.setData('application/json', dataToTransfer);
        e.dataTransfer.effectAllowed = 'copy';
    });
});

// 3. ドロップ時の処理
containerElement.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
});

containerElement.addEventListener('drop', (e) => {
    e.preventDefault();

    const dataString = e.dataTransfer.getData('application/json');

    if (!dataString) {
        console.error('ドラッグデータを取得できません');
        return;
    }

    const draggedData = JSON.parse(dataString);

    console.log('ドロップデータ:', draggedData);

    const rect = containerElement.getBoundingClientRect();
    const dropX = e.clientX - rect.left;
    const dropY = e.clientY - rect.top;
    const sizePx = draggedData.sizeMm * PX_PER_MM;

    const image = new Image();

    image.onload = () => {
        console.log('画像読み込み成功:', draggedData.src);

        const imageNode = new Konva.Image({
            image: image,
            x: dropX,
            y: dropY,
            width: sizePx,
            height: sizePx,
            offsetX: sizePx / 2,
            offsetY: sizePx / 2,
            draggable: true,
            partId: draggedData.partId
        });

        imageNode.on('click tap', () => {
            tr.nodes([imageNode]);
        });

        layer.add(imageNode);
        layer.draw();
    };

    image.onerror = () => {
        console.error('画像読み込み失敗:', draggedData.src);
        alert('画像を読み込めませんでした: ' + draggedData.src);
    };

    image.src = draggedData.src;
});

// 4. 保存ボタンを押したときの処理
document.getElementById('save-btn').addEventListener('click', () => {
    // キャンバス上の画像（パーツ）だけをすべて取得
    const partsOnCanvas = layer.getChildren(node => node.className === 'Image');
    const itemsData = [];

    // それぞれのパーツの情報を配列にまとめる
    partsOnCanvas.forEach(node => {
        itemsData.push({
            partId: node.getAttr('partId'), // 設定しておいたパーツID
            x: Math.round(node.x()),        // 座標（小数点を四捨五入）
            y: Math.round(node.y()),
            rotation: Math.round(node.rotation()) // 回転角度
        });
    });

    if (itemsData.length === 0) {
        alert("キャンバスにパーツがありません。");
        return;
    }

    // fetchを使って、画面を切り替えずに裏側でPHPへデータを送信する
    fetch('workspace.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(itemsData) // データをJSON形式に変換して送る
    })
    .then(response => {
        console.log('HTTPステータス:', response.status);

        if (!response.ok) {
            throw new Error(`HTTPエラー: ${response.status}`);
        }

        return response.text();
    })
    .then(result => {
        console.log('保存結果:', result);
        alert("保存が完了しました！\n" + result);
        window.location.href = './mypage.php';
    })
    .catch(error => {
        console.error('保存エラー:', error);
        alert("保存通信に失敗しました。");
        console.error(error);
    });
});