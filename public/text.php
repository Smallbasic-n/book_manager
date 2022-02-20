<html>
    <head>
        <title>書籍管理システム-書籍の詳細</title>
    </head>
    <body>
<?php 
$isbn=$_GET['isbn'] ?? null;
$url = sprintf('https://www.googleapis.com/books/v1/volumes?q=isbn:%s',$isbn);// 検索するURLを生成
$content = file_get_contents($url); //APIからデータを取得する
$json=json_decode($content,true)['items'][0];// JSON形式になっているので、PHPの配列に変換
?>
<table border="1">
    <thread>
        <tr>
            <th>項目</th>
            <th>値</th>
        </tr>
    </thread>
    <tr>
        <th>タイトル</th>
        <th><?php echo $json['volumeInfo']['title'] ?? ''; ?></th>
    </tr>
    <tr>
        <th>発行者</th>
        <th><?php echo $json['volumeInfo']['authors'][0] ?? ''; ?></th>
    </tr>
    <tr>
        <th>発行日</th>
        <th><?php echo $json['volumeInfo']['publishedDate'] ?? ''; ?></th>
    </tr>
    <tr>
        <th>あらすじ・内容紹介</th>
        <th><?php echo $json['volumeInfo']['description'] ?? '';?></th>
    </tr>
    <tr>
        <th>ISBN(ISBN13)</th>
        <th><?php echo $json['volumeInfo']['industryIdentifiers'][1]['identifier'] ?? ''; ?></th>
    </tr>
    <tr>
        <th>ページ数</th>
        <th><?php echo $json['volumeInfo']['pageCount'] ?? ''; ?></th>
    </tr>
    <tr>
        <th>画像</th>
        <th>
            <img 
            src="<?php echo $json['volumeInfo']['imageLinks']['thumbnail'] ?? ''; ?>" alt="画像"/></th>
    </tr>
    <tr>
        <th>Google Booksリンク</th>
        <th>
            <a target="_blank"
             href="<?php echo $json['volumeInfo']['infoLink'] ?? ''; ?>">
             Google Books
            </a>
            </th>
    </tr>
</table>
<button onclick="window.close();">閉じる</button>
<button onclick="window.open('raw.php?isbn=<?php echo $isbn; ?>', '生の値', 'width=1000,height=500,scrollbars=1,resizable=1'); return false;">生の値</button>
</body>
</html>