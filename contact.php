<?php
// contact.php

// タイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

// POSTリクエストの場合のみ処理を実行
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // データの取得とサニタイズ（セキュリティ対策）
    $name    = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8');
    $date    = date("Y/m/d H:i:s");

    // CSVに保存するデータの配列
    $data = [$date, $name, $email, $subject, $message];

    // CSVファイルのパス
    $csv_file = 'contacts.csv';

    // ファイルを開く（追記モード 'a'）
    // ファイルが存在しない場合は自動で作成されます
    $fp = fopen($csv_file, 'a');

    if ($fp) {
        // ロック（排他制御）
        if (flock($fp, LOCK_EX)) {
            // CSVフォーマットで書き込み
            // fputcsvはカンマや改行を自動で適切に処理します
            fputcsv($fp, $data);
            
            // ロック解除
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        // 送信完了画面を表示、またはリダイレクト
        // ここでは簡易的にJavaScriptでアラートを出して戻る処理にします
        echo "<script>
                alert('お問い合わせありがとうございます。送信が完了しました。');
                window.location.href = 'index.html';
              </script>";
    } else {
        echo "エラーが発生しました。データを保存できませんでした。";
    }

} else {
    // POST以外でのアクセス時はトップへ戻す
    header("Location: index.html");
    exit;
}
?>