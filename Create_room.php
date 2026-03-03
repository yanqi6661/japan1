<?php
// 1. 取得使用者想建立的房號名稱
$roomName = $_POST['room_name'] ?? '';

// 清除特殊字元，避免檔名出錯
$roomName = preg_replace('/[^A-Za-z0-9_\-]/', '', $roomName);

if (empty($roomName)) {
    die("請輸入有效的房號名稱！ <a href='index.php'>返回</a>");
}

$folder = "rooms/";
$filePath = $folder . $roomName . ".json";

// 2. 檢查房號是否重複
if (file_exists($filePath)) {
    echo "<script>alert('此房號「{$roomName}」已有人使用，請換一個！'); window.location.href='index.php';</script>";
    exit;
}

// 3. 產生 4 位數隨機亂碼密碼 (英文+數字)
$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
$randomPassword = substr(str_shuffle($characters), 0, 4);

// 4. 初始化遊戲資料
$initialData = [
    'room_name' => $roomName,
    'password' => $randomPassword,
    'board' => array_fill(0, 9, ''), // 9 個空格
    'nextPlayer'=> 'X',
    'players' => 1, // 房主建立時算 1 人
    'winner' => null
];

// 確保 rooms 資料夾存在
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// 5. 儲存成 JSON 檔案
if (file_put_contents($filePath, json_encode($initialData))) {
    // 成功建立後，顯示房號與密碼給房主看
    ?>
    <!DOCTYPE html>
    <html lang="zh-TW">
    <head>
        <meta charset="UTF-8">
        <title>房間建立成功</title>
        <style>
            body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #e8f5e9; margin: 0; }
            .success-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; }
            .highlight { color: #2e7d32; font-size: 1.5rem; font-weight: bold; }
            .password-box { font-size: 3rem; letter-spacing: 10px; margin: 20px 0; color: #1565c0; background: #f0f7ff; padding: 10px; border-radius: 8px; border: 2px dashed #1565c0; }
            .btn { display: inline-block; padding: 12px 25px; background: #2e7d32; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="success-card">
            <h2>🎉 房間建立成功！</h2>
            <p>房號名稱：<span class="highlight"><?php echo htmlspecialchars($roomName); ?></span></p>
            <p>請將下方<strong>「四位數密碼」</strong>交給你的朋友：</p>
            <div class="password-box"><?php echo $randomPassword; ?></div>
            <p>輸入密碼即可進入遊戲。</p>
            <a href="game.php?room=<?php echo $roomName; ?>&pw=<?php echo $randomPassword; ?>" class="btn">直接進入遊戲</a>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "系統錯誤，無法建立房間。";
}
?>

