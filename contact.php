<?php
// contact.php

// タイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

// エラーを表示する設定（デバッグ用）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// POSTリクエストの場合のみ処理を実行
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // データの取得とサニタイズ
    $name    = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8');
    $date    = date("Y/m/d H:i:s");

    // CSVに保存するデータ
    $data = [$date, $name, $email, $subject, $message];

    // ★重要：ファイルの場所を「絶対パス」で指定する
    // __DIR__ はこのPHPファイルがあるフォルダを指します
    $csv_file = __DIR__ . '/contacts.csv';

    // 書き込みチェック（デバッグ用）
    if (!is_writable(__DIR__)) {
        echo "<h1>エラー：書き込み権限がありません</h1>";
        echo "<p>フォルダ: " . __DIR__ . " に書き込む許可がありません。</p>";
        exit;
    }

    // ファイルを開く（追記モード 'a'）
    $fp = fopen($csv_file, 'a');

    if ($fp) {
        if (flock($fp, LOCK_EX)) {
            fputcsv($fp, $data);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        // 成功時の処理
        echo "<script>
                alert('お問い合わせありがとうございます。送信が完了しました。');
                window.location.href = 'index.html';
              </script>";
    } else {
        // 失敗時のエラー表示
        echo "<h1>システムエラー</h1>";
        echo "<p>CSVファイルを開けませんでした。</p>";
        echo "<p>保存先パス: " . $csv_file . "</p>";
        echo "<p>エラー詳細: ";
        print_r(error_get_last());
        echo "</p>";
    }

} else {
    header("Location: index.html");
    exit;
}
?>
