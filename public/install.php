<?php

// install.php

$actp = $_POST["act"] ?? null;
$acte = $_GET["suc"] ?? null;
$aptc = false;
if ($actp == "installsta"){
    header("location: regist.php?action=dbmake");
    exit;
}elseif $scte == "okay"(){
    $aptc=true;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
    <head>
        <title>書籍管理システム-インストール</title>
    </head>
    <body>
        <?php if ($aptc == false): ?>
            <h3>ようこそ、書籍管理システムへ。ここでは、あなたのサーバに、書籍管理システムを設定します。<h3>
            <form method="post" action="">
                設定を開始するには続行ボタンをおしてください。<br/>
                初期設定としてMySQLのrootユーザでのログインの許可と、rootユーザのパスワードをrootにしておいてください。
                <input type="hidden" name="act" value="installsta"/>
                <button type="submit">続行</button> 
            </form>
        <?php else: ?>
            <h1>インストールに成功しました。<a href="/">こちら</a>をクリックして、使い始めてください。</h1>
        <?php endif; ?>
    </body>
</html>