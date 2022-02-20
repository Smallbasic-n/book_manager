<?php
$action = $_GET['action'] ?? null;
$main = new MainClass(preg_replace('/[^0-9]/','',$_GET['isbn'] ?? ""),$_GET['wheres'] ?? null,$_GET['type'] ?? null,$action);
match ($action) {
    'search' => $main->whserch(),
    'register' => $main->register(),
    'action' => $main->readed(),
    'delete' => $main->delbok(),
    'wantreg' => $main->wantreg(),
    'tohave' => $main->gotohave(),
    'comread' => $main->commentread(),
    'comwrite' => $main->commentwrite(),
    'dbmake' => $main->makeall(),
    'dbsee' => $main->seeall(),
    'recom' => $main->recommend(),
    null => $main->nullto(),
};
class MainClass
{
    private $isbn;
    private $where;
    private $type;
    private $pdo;
    private function haveserc(string $tabe):bool {
        $pdo = new PDO('mysql:host=db;port=3306;dbname=booklist','root', 'root'); // データベースへの接続
        $tmt= $pdo->prepare("show tables where Tables_in_booklist=:tex");
        $tmt->bindValue(":tex",$tabe);
        $tmt->execute();
        foreach ($tmt as $bookas){
        return true;
        }
        return false;
    }
    function __construct(?string $isbn,?string $where,?string $type,?string $action){
        $this->isbn=$isbn;
        $this->type=$type;
        $this->where=$where;
        if ($action == "dbmake"){
            return;
        }
        $pdo = new PDO('mysql:host=db;port=3306','root', 'root'); // データベースへの接続
        $tmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'booklist'");
        $okay=false;
        foreach ($tmt as $bookas){
            $okay=true;
        }
        if ($okay == false){
            header("location: install.php");
            exit;
        }elseif ($this->haveserc("books") == false){
            $this->makebooks();
        }elseif ($this->haveserc("hadbooks") == false){
            $this->makehadbooks();
        }elseif ($this->haveserc("wantbooks") == false){
            $this->makewantbooks();
        }elseif ($this->haveserc("comment") == false){
            $this->makecomment();
        }
        $this->pdo= new PDO('mysql:host=db;port=3306;dbname=booklist', 'root', 'root');
    }
    private function deltable(string $tabd){
        $tmt = $this->pdo->prepare("DROP TABLE IF EXISTS :tablesss");
        $tmt->bindValue(":tablesss",$tabd);
    }
    private function makebooks(){
        $this->deltable("books");
        $delb=$this->pdo->prepare("CREATE TABLE books (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            isbn VARCHAR(255) NOT NULL, 
            image VARCHAR(255), 
            readed TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL, 
            updated_at DATETIME NOT NULL,
            ndc INT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $delb->execute();    
        }
    private function makehadbooks(){
        $this->deltable("hadbooks");
        $delb=$this->pdo->prepare("CREATE TABLE hadbooks (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            isbn VARCHAR(255) NOT NULL, 
            image VARCHAR(255), 
            readed TINYINT(1) NOT NULL DEFAULT 0,
            how CHAR(4) NOT NULL, 
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $delb->execute();
        }
    private function makewantbooks(){
        $this->deltable("wantbooks");
        $delb=$this->pdo->prepare("CREATE TABLE wantbooks (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            isbn VARCHAR(255) NOT NULL, 
            image VARCHAR(255), 
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $delb->execute();
        }
    private function makecomment(){
        $this->deltable("comment");
        $delb=$this->pdo->prepare("CREATE TABLE comment (
            id INT AUTO_INCREMENT NOT NULL, 
            isbn VARCHAR(255) NOT NULL, 
            content VARCHAR(5000) NOT NULL,
            favorite TINYINT(1) NOT NULL DEFAULT 0, 
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $delb->execute();
        }
    public function makeall(){
            $pdo = new PDO('mysql:host=db;port=3306','root', 'root'); // データベースへの接続
            $delb=$pdo->prepare("DROP DATABASE IF EXISTS " . $dbname);
            $delb->execute();
            $delb=$pdo->prepare("CREATE DATABASE " . $dbname);
            $delb->execute();
            $this->makebooks();
            $this->makehadbooks();
            $this->makewantbooks();
            $this->makecomment();
            header("loation: install.php?scu=okay");
            exit;
        }
    public function seeall(){
            header("location: /?oks=ok");
        }
   
    public function nullto(){
        header("location:/");
    }
    private function hasBook(): bool {
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        return !empty($stmt->fetchAll());
    }
    private function hadBook(): bool {
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM hadbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        return !empty($stmt->fetchAll());
    }
    
    private function wanthaBook(): bool {
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM wantbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        return !empty($stmt->fetchAll());
    }
    
    /**
     * 検索用の関数
     */
    private function search():array {
        $url = sprintf('https://www.googleapis.com/books/v1/volumes?q=isbn:%s', $this->isbn);// 検索するURLを生成
        $content = file_get_contents($url); //APIからデータを取得する
        $json=json_decode($content,true);// JSON形式になっているので、PHPの配列に変換
        return array_key_exists('items',$json) === false ? [false, []]:[true, $json['items'][0]];
    }
    
    /**
     * 登録用関数
     */
    public function register() {
        //$this->ndc($this->isbn);
         //データベースへの接続
        if ($this->isbn === null) {
            // ISBNがない
            return [false, []];
        }
        $book = $this->search()[1];
        if($this->hasBook() || $this->hadBook()) {
            // 既にデータベースに登録されているのでここで処理を抜ける
            header('location: index.php?type=failed');
            return [false, $book];
        }
        $stmt = $this->pdo->prepare("INSERT INTO books (isbn, name, image, created_at, updated_at, ndc) VALUES (:isbn, :name, :image, now(), now(), :ndc)");
        $stmt->bindValue(':isbn', $this->isbn);
        $stmt->bindValue(':name', $book['volumeInfo']['title']);
        $stmt->bindValue(':ndc', $this->ndc($this->isbn));
        if (array_key_exists('imageLinks',$book['volumeInfo']) && array_key_exists('thumbnail',$book['volumeInfo']['imageLinks'])) {
            $stmt->bindValue(':image', $book['volumeInfo']['imageLinks']['thumbnail']);
        }else{
            $stmt->bindValue(':image', "http://localhost/noimg.png");
        }
        $stmt->execute();
        header('location: index.php?type=successful');
        //return[true,$book];
    }
    
    public function readed() {
        //データベースへの接続
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->bindValue(1,$this->isbn,PDO::PARAM_INT);
        $stmt->execute();
        foreach ($stmt as $book){
            $stmt = $this->pdo->prepare('UPDATE books SET readed = 1,updated_at = now() WHERE id = ?');
            $stmt->bindValue(1,$this->isbn,PDO::PARAM_INT);
            $stmt->execute();
            header('location: index.php?type=successful');
            exit;
            return [true,[]];
        }
        header('location: index.php?typeod=faild');
        //return [true,[]];
    }
    
    public function delbok() {
         //データベースへの接続
        if($this->hasBook()) {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE isbn=:isbn"); // クエリの実行
            $stmt->bindParam(':isbn',$this->isbn);
            $stmt->execute();
            foreach ($stmt as $books){
                $stmt2 = $this->pdo->prepare("INSERT INTO hadbooks (isbn, name, image, readed, how, created_at) VALUES (:isbn, :name, :image, :rde, :how, now())");
                $stmt2->bindParam(':isbn', $books['isbn']);
                $stmt2->bindParam(':name', $books['name']);
                $stmt2->bindParam(':image', $books['image']);
                $stmt2->bindParam(':rde', $books['readed']);
                $stmt2->bindParam(':how', $this->type);
                $stmt2->execute();
                break;
            }
            $stmt3 = $this->pdo->prepare("DELETE FROM books WHERE isbn=:isbn"); // クエリの実行
            $stmt3->bindParam(':isbn',$this->isbn);
            $stmt3->execute();
            $stmt4 = $this->pdo->prepare("SELECT * FROM books WHERE isbn=:isbn"); // クエリの実行
            $stmt4->bindParam(':isbn',$this->isbn);
            $stmt4->execute();
            $res = $stmt === null ? "faild":"successful";
            header('location: index.php?typeod=' . $res);
            //return $stmt === null ? [true,[]]:[false,[]];
        }
    }
    
    private function haveser():array {
         //データベースへの接続
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        foreach($stmt as $book) {
            header("location: index.php?action=successful&title=" . $book['name'] . '&pubdate=' . $book['created_at'] . '&cover=' . $book['image'] . '&isbn=' . $book['isbn']);
            exit;
        };
        header("location: index.php?action=faild");
        return [true,[]];
    }
    
    private function hadser():array {
         //データベースへの接続
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM hadbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        foreach($stmt as $book) {
            header("location: index.php?action=successful&title=" . $book['name'] . '&pubdate=' . $book['created_at'] . '&cover=' . $book['image'] . '&isbn=' . $book['isbn']);
            exit;
        };
        header("location: index.php?action=faild");
        return [true,[]];
    }
    
    private function wantser():array {
         //データベースへの接続
        // 既に登録があるか確認する -- WHERE isbn = :isbn
        $stmt = $this->pdo->prepare("SELECT * FROM wantbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        foreach($stmt as $book) {
            header("location: index.php?action=successful&title=" . $book['name'] . '&pubdate=' . $book['created_at'] . '&cover=' . $book['image'] . '&isbn=' . $book['isbn']);
            exit;
        };
        header("location: index.php?action=faild");
        return [true,[]];
    }
    
    private function searcs():array {
        $json2 = $this->search();
        $json = $json2[1];
        if ($json2[0] == false){
            header('location: index.php?action=faild');
            exit;
        }
        $img="";
        if (array_key_exists('imageLinks',$json['volumeInfo']) && array_key_exists('thumbnail',$json['volumeInfo']['imageLinks'])) {
            $img=urlencode($json['volumeInfo']['imageLinks']['thumbnail']);
        }
        //header('location: index.php?title=' . $json['title'] . '&pubdate=' . $json['pubdate'] . '&cover=' . $json['cover'] . '&isbn=' . $json['isbn']);
        header('location: index.php?title=' . $json['volumeInfo']['title'] . '&pubdate=' . $json['volumeInfo']['publishedDate'] . '&cover=' . $img . '&isbn=' . $this->isbn);
        exit;
        return [true,[]];
    }
    
    public function whserch() {
        match ($this->where) {
            'openbd' => $this->searcs(),
            'had' => $this->hadser(),
            'want' =>$this->wantser(),
            'have' =>$this->haveser(),
            null => [false,[]]
        };
    }
    
    public function wantreg() {
        $book = $this->search()[1];
        if($this->hasBook() || $this->wanthaBook()) {
            // 既にデータベースに登録されているのでここで処理を抜ける
            header('location: index.php?type=failed');
            return [false, $book];
        }
            $stmt = $this->pdo->prepare("INSERT INTO wantbooks (isbn, name, image, created_at) VALUES (:isbn, :name, :image, now())");
        $stmt->bindValue(':isbn', $this->isbn);
        $stmt->bindValue(':name', $book['volumeInfo']['title']);
        if (array_key_exists('imageLinks',$book['volumeInfo']) && array_key_exists('thumbnail',$book['volumeInfo']['imageLinks'])) {
            $stmt->bindValue(':image', $book['volumeInfo']['imageLinks']['thumbnail']);
        }else{
            $stmt->bindValue(':image', "http://localhost/noimg.png");
        }
        $stmt->execute();
        header('location: index.php?type=successful');
        //return[true,$book];
    }
    
    public function gotohave() {
        $stmt = $this->pdo->prepare("DELETE FROM hadbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        $stmt = $this->pdo->prepare("DELETE FROM wantbooks WHERE isbn=:isbn"); //クエリの実行
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->execute();
        $this->register();
        exit;
        //return [true,[]];
    }
    public function commentread(){
        $stmt=$this->pdo->prepare("SELECT * FROM comment WHERE isbn=:isbn");
        $stmt->bindParam(":isbn",$this->isbn);
        $stmt->execute();
        foreach($stmt as $books){
            $fax= $books['favorite'] === 1 ? "on":"off";
            header("location: comment.php?favorite=".$fax."&result=found&isbn=".$this->isbn."&text=".$books['content']);
            exit;
        }
        header("location: comment.php?isbn=".$this->isbn."&result=notf");
    }
    public function commentwrite(){
        $stmt=$this->pdo->prepare("DELETE FROM comment WHERE isbn=:isbn");
        $stmt->bindParam(":isbn",$this->isbn);
        $stmt->execute();
        $stmt=$this->pdo->prepare("INSERT INTO comment (isbn,content,favorite,created_at) VALUES (:isbn,:cont,:fav,now())");
        $stmt->bindParam(":isbn",$this->isbn);
        $cont=$_GET['content'] ?? null;
        $stmt->bindParam(":cont",$cont);
        $fav=$_GET['favorite'] ?? null;
        $fax= $fav === "on" ? 1:0;
        $stmt->bindParam(":fav",$fax);
        $stmt->execute(); 
        $this->commentread();
    }
    public function recommend(){
        $douka=[[0,0],[0,1],[0,2],[0,3],[0,4],[0,5],[0,6],[0,7],[0,8],[0,9]];
        $stmt = $this->pdo->query("SELECT * FROM books"); // クエリの実行
        foreach ($stmt as $books) {
            $ndc=substr($books["ndc"],0,1);
            match ($ndc) {
                "0" => $douka[0][0] +=1,
                "1" => $douka[1][0] +=1,
                "2" => $douka[2][0] +=1,
                "3" => $douka[3][0] +=1,
                "4" => $douka[4][0] +=1,
                "5" => $douka[5][0] +=1,
                "6" => $douka[6][0] +=1,
                "7" => $douka[7][0] +=1,
                "8" => $douka[8][0] +=1,
                "9" => $douka[9][0] +=1,
                null => $douka[0][0] +=1
            };
        }
        $osusume=[0,0,0];
        rsort($douka);
        for ($i=0; $i < 3; $i++) { 
            match ($douka[$i][1]) {
                0 => $osusume[$i] =0,
                1 => $osusume[$i] =1,
                2 => $osusume[$i] =2,
                3 => $osusume[$i] =3,
                4 => $osusume[$i] =4,
                5 => $osusume[$i] =5,
                6 => $osusume[$i] =6,
                7 => $osusume[$i] =7,
                8 => $osusume[$i] =8,
                9 => $osusume[$i] =9,
                null => $osusume[$i] =0
            };
        }
        $teian=[0,0,0];
        for ($i=0; $i < 3; $i++) { 
            match ($osusume[$i]) {
                0 => $teian[$i] =8,
                1 => $teian[$i] =4,
                2 => $teian[$i] =6,
                3 => $teian[$i] =7,
                4 => $teian[$i] =1,
                5 => $teian[$i] =9,
                6 => $teian[$i] =2,
                7 => $teian[$i] =3,
                8 => $teian[$i] =0,
                9 => $teian[$i] =5,
                null => $teian[$i] =0
            };
        }
        header("location: /?1st=".$teian[0]."&2nd=".$teian[1]."&3rd=".$teian[2]);
}
    private function ndc(string $isbn):string{
        $url = sprintf('https://iss.ndl.go.jp/api/opensearch?isbn=%s', $isbn);// 検索するURLを生成
        $content = file_get_contents($url); //APIからデータを取得する
        $xml = simplexml_load_string($content);
        $ndc=$xml->xpath('//* [@xsi:type="dcndl:NDC10"]');
        return substr($ndc[0],0,1);
    }

    }
?>
<html>
    <head>
        <title>書籍管理システム-処理を実行中</title>
    </head>
</html>