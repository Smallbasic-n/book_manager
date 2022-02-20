<html>
    <head>
        <title>書籍管理システム-生の値</title>
    </head>
    <body>
        <pre>
<?php
$url = sprintf('https://www.googleapis.com/books/v1/volumes?q=isbn:%s',$_GET['isbn'] ?? null);// 検索するURLを生成
$content = file_get_contents($url); //APIからデータを取得する
$json=json_decode($content,true)['items'][0];// JSON形式になっているので、PHPの配列に変換
print_r($json);
?>
<button onclick="window.close();">閉じる</button></pre>
    </body>
</html>