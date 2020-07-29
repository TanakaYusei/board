<?php
    //変数の初期化
    $now_date = null;         //書き込み日格納変数
    $data = null;             //入力データ格納変数
    $file_handle = null;      //ファイルアクセス情報格納変数
    $split_data = null;       //分割データ格納変数
    $message = array();       //分割データ一時格納変数
    $message_array = array(); //分割データ格納変数
    $success_massage = null;  //投稿完了判別用変数
    $error_message = array(); //未入力判別用変数
    $clean = array();         //サニタイズ用変数

    session_start();          //セッション状態確認関数

    //タイムゾーンの設定
    date_default_timezone_set('Asia/Tokyo');

    //データの受け取り
    if(!empty($_POST['btn_submit'])){
        
    }
    
    //データベースの読み込み
        //データベースに接続
    $mysqli = new mysqli('localhost', 'g031q112', 'srbsextrKR', 'g031q112');
    //接続エラーの確認
    if($mysqli -> connect_errno){
        $error_message[] = 'データの読み込みに失敗しました。エラー番号'.$mysqli -> connect_errno.'：'.$mysqli -> cennect_error;
    } else {    //データの取得    
        //文字コードのセット
        $mysqli -> set_charset('utf8');
        
        //データ取得用SQL作成
        $sql = "SELECT view_name,message,post_date FROM board ORDER BY post_date DESC";
        
        //オブジェクトの取得
        $res = $mysqli -> query($sql);
        
        //データの取得
        if($res){
            $message_array = $res -> fetch_all(MYSQLI_ASSOC);
        }
        
        //データベースを閉じる
        $mysqli -> close();
    }
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>管理ページ</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    
    <body>
        <!--未入力時のエラー表示-->
        <!--未入力かの確認-->
        <?php if(!empty($error_message)){ ?>
            <!--配列の数だけエラーメッセージを参照-->
            <ul class="error_message">
                <?php foreach($error_message as $value){ ?>
                    <!--エラー表示-->
                    <li><?php echo $value ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <section>
            <!--ログインしているかを判別-->
            <?php if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true){ ?>
            <!-- 取得メッセージの表示 -->
                <!--表示するメッセージがあるかの確認-->
                <?php if(!empty($message_array)){ ?>
                    <!--メッセージの件数分表示するための分岐-->
                    <?php foreach($message_array as $value){ ?>
                        <!--メッセージの表示-->
                        <article>
                            <div class="info">
                                <!--名前の表示-->
                                <h2><?php echo $value['view_name']; ?></h2>
                                <!--時間をタイムスタンプ形式に変換し、表示-->
                                <time><?php echo date('Y年m月d日 H:i',strtotime($value['post_date'])); ?></time>
                            </div>
                            <!--表示-->
                            <p><?php echo $value['message']; ?></p>
                        </article>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <!--ログインフォーム-->
            <?php } ?>
        </section>
    </body>
</html>