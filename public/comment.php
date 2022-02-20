<html>
    <head><title>書籍管理システム-自分のコメント</title></head>
<?php
    $text=$_GET['result'] ?? null;
    $cont=$_GET['text'] ?? null;
    $isbn=$_GET['isbn'] ?? null;
    $favo=$_GET['favorite'] ?? null;
    $flag=false;
    ?>
    <body>
        <?php if ($text=="notf"):?>
            <p>コメントが見つかりませんでした。下記に入力して、作成してください。</p><?php $flag=true ?>
        <?php else: ?><p>コメントが見つかったまたは登録に成功しました。</p>
            <?php endif; ?>
            コメントは最大900文字です。900文字以下にして下さい。</br/>
        <form medhot="get" action="regist.php">
            <?php if ($favo == "on"): ?>
            <input type="checkbox" name="favorite" checked>お気に入り</input>
            <?php else: ?>
            <input type="checkbox" name="favorite">お気に入り</input>
            <?php endif;?>
            <input type="hidden" name="action" value="comwrite"/>
            <input type="hidden" name="isbn" value="<?php echo $isbn ?>"/>
            <textarea rows="26" cols="67" maxlength="900" name="content"><?php echo $cont ?></textarea>
            <button type="submit">書き込む</button>
            <button onclick="window.close();">閉じる</button>
        </form>