const containerElement = document.getElementById('canvas-container');

// 1. キャンバスの初期設定
const stage = new Konva.Stage({
    container: 'canvas-container',
    width: containerElement.offsetWidth || 600, 
    height: containerElement.offsetHeight || 500
});
const layer = new Konva.Layer();
stage.add(layer);

function addPartToCanvas(partData, x, y, rotation = 0) {
    const image = new Image();

    image.onload = () => {
        const widthPx = Number(partData.width_mm) * PX_PER_MM;
        const heightPx = Number(partData.height_mm) * PX_PER_MM;

        const imageNode = new Konva.Image({
            image: image,
            x: Number(x),
            y: Number(y),
            width: widthPx,
            height: heightPx,
            offsetX: widthPx / 2,
            offsetY: heightPx / 2,
            rotation: Number(rotation),
            draggable: true,
            partId: Number(partData.part_id ?? partData.partId),
            partName: partData.parts_name ?? partData.partName,
            price: Number(partData.price) || 0
        });

        imageNode.on('click tap', () => {
            tr.nodes([imageNode]);
        });

        layer.add(imageNode);
        layer.draw();
        updateTotalPrice();
    };

    image.onerror = () => {
        console.error('画像読み込み失敗:', partData.image_path);
    };

    image.src = partData.image_path;
}

function updateTotalPrice() {
    const totalPriceElement = document.getElementById('total-price');
    let totalPrice = 0;

    layer.getChildren(node => node.className === 'Image').forEach(node => {
        totalPrice += Number(node.getAttr('price')) || 0;
    });

    totalPriceElement.textContent = totalPrice.toLocaleString();
}

const PX_PER_MM = 0.8; // 変更必要 標準は4

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
            wSizeMm: parseFloat(e.currentTarget.dataset.wSizeMm) || 10,
            hSizeMm: parseFloat(e.currentTarget.dataset.hSizeMm) || 10,
            partId: e.currentTarget.dataset.partId,
            partName: e.currentTarget.dataset.partName,
            price: parseInt(e.currentTarget.dataset.price, 10) || 0
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
    const widthPx = draggedData.wSizeMm * PX_PER_MM;
    const heightPx = draggedData.hSizeMm * PX_PER_MM;

    const image = new Image();

    image.onload = () => {
        console.log('画像読み込み成功:', draggedData.src);

        const imageNode = new Konva.Image({
            image: image,
            x: dropX,
            y: dropY,
            width: widthPx,
            height: heightPx,
            offsetX: widthPx / 2,
            offsetY: heightPx / 2,
            draggable: true,
            partId: draggedData.partId,
            partName: draggedData.partName,
            price: draggedData.price
        });

        imageNode.on('click tap', () => {
            tr.nodes([imageNode]);
        });

        layer.add(imageNode);
        layer.draw();
        updateTotalPrice();
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

    // Konvaの機能でキャンバスのスクリーンショットを撮影（Base64文字列に変換）
    const dataURL = stage.toDataURL({ pixelRatio: 1 });

    // 作品のタイトル
    const workTitle = document.querySelector('[name="work_title"]').value.trim();

    if (workTitle === '') {
        alert('作品タイトルを入力してください。');
        return;
    }

    // 画像データとパーツデータの両方を1つの荷物にまとめる
    const workData = {
        work_title: workTitle,
        thumbnail: dataURL,
        items: itemsData
    };

    // fetchを使って、画面を切り替えずに裏側でPHPへデータを送信する
    fetch('workspace.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(workData) // データをJSON形式に変換して送る
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

if (Array.isArray(window.initialWorkspace)) {
    window.initialWorkspace.forEach(item => {
        addPartToCanvas(
            item,
            item.x_set,
            item.y_set,
            item.rotation
        );
    });
}