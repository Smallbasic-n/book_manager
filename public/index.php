<?php
// index.php
//docker compose exec db /bin/bash
$title = $_GET['title'] ?? null;
$pubdate = $_GET['pubdate'] ?? null;
$cover = $_GET['cover'] ?? null;
$isbn = $_GET['isbn'] ?? null;
$type = $_GET['type'] ?? null;
$typeod = $_GET['typeod'] ?? null;
$act = $_GET['action'] ?? null;
$susume = $_GET['1st'] ?? null;
$susume2 = $_GET['2nd'] ?? null;
$susume3 = $_GET['3rd'] ?? null;
if ($act == null && $title == null && $type == null && $typeod == null && $susume == null) {
    $setss=$_GET["oks"] ?? null;
    if ($setss !== "ok"){
        header("location: regist.php?action=dbsee");
        exit;
    }
}
$pdo = new PDO('mysql:host=db;port=3306;dbname=booklist','root', 'root'); // データベースへの接続
$stmt = $pdo->query("SELECT * FROM books"); // クエリの実行
$stmt2 = $pdo->query("SELECT * FROM hadbooks"); // クエリの実行
$stmt3 = $pdo->query("SELECT * FROM wantbooks"); // クエリの実行
?>

<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8"/>
        <title>書籍管理システム-メインページ</title>
        <script language="javascript" type="text/javascript">
            function endinit() {
                if (document.getElementById("regdbookb").innerHTML == "持っている本-表示" && document.getElementById("regdbookc").innerHTML == "持っていた本-表示" && document.getElementById("regdbookd").innerHTML == "ほしい本-表示"){
                    target = document.getElementById("mains");
                    target.style.display = "block";
                }
                return false;
            }
             function regdbooko() {
                target = document.getElementById("regdbookb");
                 if (target.innerHTML == "持っている本-表示"){
                    target.innerHTML = "持っている本-非表示";
                    target = document.getElementById("regdbook");
                    target.style.display = "block";
                    target = document.getElementById("mains");
                    target.style.display = "none";
                  } else {
                    target.innerHTML = "持っている本-表示";
                    target = document.getElementById("regdbook");
                    target.style.display = "none";
                    endinit();
                 }
                return false;
            }
            function hadbooko() {
                target = document.getElementById("regdbookc");
                 if (target.innerHTML == "持っていた本-表示"){
                    target.innerHTML = "持っていた本-非表示";
                    target = document.getElementById("hadbook");
                    target.style.display = "block";
                    target = document.getElementById("mains");
                    target.style.display = "none";
                } else {
                    target.innerHTML = "持っていた本-表示";
                    target = document.getElementById("hadbook");
                    target.style.display = "none";
                    endinit();
                }
                return false;
            }
            function wantbooko() {
                target = document.getElementById("regdbookd");
                 if (target.innerHTML == "ほしい本-表示"){
                    target.innerHTML = "ほしい本-非表示";
                    target = document.getElementById("wantbook");
                    target.style.display = "block";
                    target = document.getElementById("mains");
                    target.style.display = "none";
                 } else {
                    target.innerHTML = "ほしい本-表示";
                    target = document.getElementById("wantbook");
                    target.style.display = "none";
                    endinit();
                }
                return false;
            }
        </script>
        <style>
            *{margin: 0;padding: 0;}
            table{
                width: 100%;
                border-collapse:separate;
                border-spacing: 0;
            }

            table th:first-child{
                border-radius: 5px 0 0 0;
            }

            table th:last-child{
                border-radius: 0 5px 0 0;
                border-right: 1px solid #3c6690;
            }

            table th{
                text-align: center;
                color:white;
                background: linear-gradient(#829ebc,#225588);
                border-left: 1px solid #3c6690;
                border-top: 1px solid #3c6690;
                border-bottom: 1px solid #3c6690;
                box-shadow: 0px 1px 1px rgba(255,255,255,0.3) inset;
                width: 1%;
                height: 2%;
                padding: 1px 0;
            }

            table td{
                text-align: center;
                border-left: 1px solid #a8b7c5;
                border-bottom: 1px solid #a8b7c5;
                border-top:none;
                box-shadow: 0px -3px 5px 1px #eee inset;
                width: 1%;
                height: 2%;
                padding: 1px 0;
            }

            table td:last-child{
                border-right: 1px solid #a8b7c5;
            }

            table tr:last-child td:first-child {
                border-radius: 0 0 0 5px;
            }

            table tr:last-child td:last-child {
                border-radius: 0 0 5px 0;
            }
            .btn-square {
                display: inline-block;
                padding: 0.001em 0.5em;
                text-decoration: none;
                background: #668ad8;/*ボタン色*/
                color: #FFF;
                border-radius: 100px; /*ボックス角の丸み*/
                border: 2px solid #ddd; /*枠線*/
                border-bottom: solid 1px #627295;
                border-radius: 1px;
            }
            .btn-square:active {
                /*ボタンを押したとき*/
                -webkit-transform: translateX(10px);
                transform: translateX(10px);/*下に動く*/
                border-bottom: none;/*線を消す*/
            }
            .text1{
                width: 170px; /*親要素いっぱい広げる*/
                padding: 0.5px 10px; /*ボックスを大きくする*/
                font-size: 16px;
                border-radius: 100px; /*ボックス角の丸み*/
                border: 2px solid #ddd; /*枠線*/
                box-sizing: border-box; /*横幅の解釈をpadding, borderまでとする*/
            }
            .text1:focus {
                border: 2px solid #ff9900; 
                z-index: 10;
                outline: 0;
            }
            .radiobutton {
                display: none;
            }
            label {
                background-color: skyblue;
                padding: 1px 0.01px;
            }
            .radiobutton:checked + label {
                background-color: pink;
            }
        </style>
    </head>
    <body style="background: silver;">
        <header style="background: skyblue;">
        <?php 
        $ishow=false;
        if ($type !== null){
            echo $type === 'successful' ? "成功しました。":"この本は存在しないか、既に登録されています。";
            $ishow=true;
        } elseif ($act !== null){
            echo $act === 'successful' ? "見つかりました。":"見つかりませんでした。";
            $ishow=true;
        } elseif ($typeod !== null) {
            echo $typeod === 'successful' ? "成功しました。":"処理に失敗しました。";
            $ishow=true;
        };
        if ($ishow == true):?><a class="btn-square" href="index.php">情報を破棄</a><?php endif;?>
            <h2 style="text-align: center;">書籍管理システム</h2>
            <nav><form style="background: skyblue;" method="get" action="regist.php">
                　
                <a id="regdbookb" class="btn-square" href="javascript:regdbooko();">持っている本-表示</a>
                <a id="regdbookc" class="btn-square" href="javascript:hadbooko();">持っていた本-表示</a>
                <a id="regdbookd" class="btn-square" href="javascript:wantbooko();">ほしい本-表示</a>
                <a id="recommend" class="btn-square" href="regist.php?action=recom">おすすめの本</a>
                <input type="hidden" name="action" value="search"/>
                <input type="text" class="text1" placeholder="ISBN" name="isbn" value="" />属性
                <input type="radio" class="radiobutton" name="wheres" value="openbd" id="openbd" checked/>
                <label for="openbd">書籍DB</label>
                <input type="radio" class="radiobutton" name="wheres" value="have" id="have"/>
                <label for="have">持っている</label>
                <input type="radio" class="radiobutton" name="wheres" value="had" id="had"/>
                <label for="had">持っていた</label>
                <input type="radio" class="radiobutton" name="wheres" value="want" id="want"/>
                <label for="want">ほしい</label>本
                <button class="btn-square" type="submit">検索</button>
            </form></nav>
            <div>
            <strong
             style="color: white;">これはGoogle Books APIs,国立国会図書館サーチWebAPI(Open Search,iss-ndl-opac)を利用しています。国立国会図書館サーチは、NDC取得(おすすめ機能)に利用しています。</strong>
        </div>
            </header>
            <?php if ($act == null && $title !== null && $pubdate !== null && $cover !== null && $isbn !== null): ?>
                <div id="regist">
                <h2>
                    <img src="<?php echo $cover ?>"
                         align="left"
                         alt="<?php echo $isbn . ',' . $title ?>"/>
                    <?php echo $title;?>
                </h2>
                <?php echo $pubdate === null ? "":(new \DateTime($pubdate))->format('Y年m月d日'); ?> <br/>    
                <?php echo $isbn ?><br/>
                <a class="btn-square" href="text.php?isbn=<?php echo $isbn; ?>"
                            onClick="window.open('text.php?isbn=<?php echo $isbn; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            この本の詳細</a><br/>
                この本を、持っている本に登録するなら以下のボタンを押します。
                <form medhot="get" action="regist.php">
                    <input type="hidden" name="action" value="register"/>             
                    <input type="hidden" name="isbn" value="<?php echo $isbn; ?>"/>
                    <button class="btn-square" type="submit">持っている本へ登録する</button>
                </form>
                この本をほしい本に登録するなら以下のボタンを押します。
                <form medhot="get" action="regist.php">
                    <input type="hidden" name="action" value="wantreg"/>             
                    <input type="hidden" name="isbn" value="<?php echo $isbn; ?>"/>
                    <button class="btn-square" type="submit">ほしい本へ登録する</button>
                </form>
                <a class="btn-square" href="index.php">閉じる</a>
            </div>
            <?php endif; ?>       
            <?php if ($act !== null && $title !== null && $pubdate !== null && $cover !== null && $isbn !== null): ?>
            
            <div>
                <h2>
                    <img src="<?php echo $cover ?>"
                         align="left"
                         alt="<?php echo $isbn . ',' . $title ?>"/>
                    <?php echo $title;?>
                </h2>
                <?php echo $pubdate === null ? "":(new \DateTime($pubdate))->format('Y年m月d日'); ?> <br/>    
                <?php echo $isbn ?><br/>
                <a class="btn-square" href="index.php">閉じる</a>
            </div>
            <?php endif; ?>
            <?php if($susume !== null && $susume2 !== null && $susume3 !== null): ?>
                <p>あなたにお勧めの本の種類(日本十進分類法にて表示)は、以下の通りです。 <br/>
                    <?php echo $susume ?>類　　<a target="_blank" href="https://www.kinokuniya.co.jp/disp/CSfDispListPage_001.jsp?qsd=true&ptk=01&ndc-dec-key=<?php echo $susume ?>&rpp=20">紀伊國屋書店Webストアで<?php echo $susume ?>類の本を見る</a><br/>   
                    <?php echo $susume2 ?>類　　<a target="_blank" href="https://www.kinokuniya.co.jp/disp/CSfDispListPage_001.jsp?qsd=true&ptk=01&ndc-dec-key=<?php echo $susume2 ?>&rpp=20">紀伊國屋書店Webストアで<?php echo $susume2 ?>類の本を見る</a><br/>   
                    <?php echo $susume3 ?>類　　<a target="_blank" href="https://www.kinokuniya.co.jp/disp/CSfDispListPage_001.jsp?qsd=true&ptk=01&ndc-dec-key=<?php echo $susume3 ?>&rpp=20">紀伊國屋書店Webストアで<?php echo $susume3 ?>類の本を見る</a><br/>
                    <a class="btn-square" href="index.php">閉じる</a>
                </p> 
            <?php endif; ?>
        <font style="display: block;" id="mains" size="10"><br/>ようこそ、書籍管理システムへ。上部のメニューから実行したい作業を選択してください。</font>
        <div style="display: none;" id="regdbook">
            <div style="background: greenyellow;">持っている書籍</div>
            <table border="1">
                <thead>
                    <tr>
                        <th style="width: 0.01%;">ID</th>
                        <th>書籍名</th>
                        <th>画像</th>
                        <th style="width: 0.2%;">状態</th>
                        <th>登録日</th>
                        <th>更新日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <?php foreach ($stmt as $books) : ?>
                <tr>
                    <td style="width: 0.01%;"><?php echo $books['id'] ?></td>
                    <td><?php echo $books['name'] . "<br/>" . $books['isbn']; ?></td>
                    <td>
                        <?php if ($books['image'] !== null) : ?>
                        <img src="<?php echo $books['image'] ?>" />
                        <?php endif; ?>
                    </td>
                    <td style="width: 0.2%;">
                        <?php echo $books['readed'] === 1 ? '既読' : '未読' ?>
                    </td>
                    <td>
                        <?php echo (new \DateTime($books['created_at']))->format('Y年m月d日') ?>
                    </td>
                    <td>
                        <?php echo (new \DateTime($books['updated_at']))->format('Y年m月d日') ?>
                    </td>
                    <td>
                        <?php if ($books['readed'] === 0): ?>
                        <a href="<?php echo sprintf('regist.php?action=action&isbn=%d', $books['id']); ?>">既読</a><br/>
                        <?php endif; ?>
                        <a href="<?php
                                       echo sprintf('regist.php?action=delete&isbn=%d&type=assi',
                                                     $books['isbn']); ?>">あげた</a><br/>
                        <a href="<?php
                                       echo sprintf('regist.php?action=delete&isbn=%d&type=sell',
                                                     $books['isbn']); ?>">売った</a><br/>
                        <a href="<?php
                                      echo sprintf('regist.php?action=delete&isbn=%d&type=disc',
                                                     $books['isbn']); ?>">捨てた</a><br/>
                        <a href="text.php?isbn=<?php echo $books['isbn']; ?>"
                            onClick="window.open('text.php?isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            詳細</a>
                        <a href="regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>"
                            onClick="window.open('regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            コメント</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div style="display: none;" id="hadbook">
            <div style="background: greenyellow;">持っていた書籍</div>
            <table border="1">
                <thead>
                    <tr>
                        <th style="width: 0.01%;">ID</th>
                        <th>書籍名</th>
                        <th>画像</th>
                        <th style="width: 0.2%;">状態</th>
                        <th>なぜか</th>
                        <th>登録日</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <?php foreach ($stmt2 as $books) : ?>
                <tr>
                    <td style="width: 0.01%;"><?php echo $books['id'] ?></td>
                    <td><?php echo $books['name'] . "<br/>" . $books['isbn']; ?></td>
                    <td>
                        <?php if ($books['image'] !== null) : ?>
                        <img src="<?php echo $books['image'] ?>" />
                        <?php endif; ?>
                    </td>
                    <td style="width: 0.2%;">
                        <?php echo $books['readed'] === 1 ? '既読' : '未読' ?>
                    </td>
                    <td>
                        <?php if ($books['how'] == "assi") {
                            echo "あげた";
                        }elseif ($books['how'] == "sell"){
                            echo "売った";
                        }elseif ($books['how'] == "disc"){
                            echo "捨てた";
                        };?>
                    </td>
                    <td>
                        <?php echo (new \DateTime($books['created_at']))->format('Y年m月d日') ?>
                    </td>
                    <td>
                    <a href="<?php
                                      echo sprintf('regist.php?action=tohave&isbn=%d',
                                                     $books['isbn']); ?>">手に入った</a><br/>
                    <a href="text.php?isbn=<?php echo $isbn; ?>"
                            onClick="window.open('text.php?isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            詳細</a>
                            <a href="regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>"
                            onClick="window.open('regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            コメント</a>
                     </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div style="display: none;" id="wantbook">
            <div style="background: greenyellow;">ほしい書籍</div>
            <table border="1">
                <thead>
                    <tr>
                        <th style="width: 0.01%;">ID</th>
                        <th>書籍名</th>
                        <th>画像</th>
                        <th>登録日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <?php foreach ($stmt3 as $books) : ?>
                <tr>
                    <td style="width: 0.01%;"><?php echo $books['id'] ?></td>
                    <td><?php echo $books['name'] . "<br/>" . $books['isbn']; ?></td>
                    <td>
                        <?php if ($books['image'] !== null) : ?>
                        <img src="<?php echo $books['image'] ?>" />
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo (new \DateTime($books['created_at']))->format('Y年m月d日') ?>
                    </td>
                    <td>
                        <a href="<?php
                                      echo sprintf('regist.php?action=tohave&isbn=%d',
                                                     $books['isbn']); ?>">手に入った</a><br/>
                        <a href="text.php?isbn=<?php echo $books['isbn']; ?>"
                            onClick="window.open('text.php?isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            詳細</a>
                            <a href="regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>"
                            onClick="window.open('regist.php?action=comread&isbn=<?php echo $books['isbn']; ?>', '詳細', 'width=500,height=500,scrollbars=1,resizable=1'); return false;">
                            コメント</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
  </body>
</html>