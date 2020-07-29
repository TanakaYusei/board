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

    //データベースの接続情報
    define('DB_HOST', 'localhost');
    define('DB_USER', 'g031q112');
    define('DB_PASS', 'srbsextrKR');
    define('DB_NAME', 'board');

    //タイムゾーンの設定
    date_default_timezone_set('Asia/Tokyo');

    //データの受け取り
    if(!empty($_POST['btn_submit'])){
        //投稿者名入力確認
        if(empty($_POST['view_name'])){
            //投稿者名が未入力であることを判別
            $error_message[] = '投稿者名を入力してください。';
        } else {    //サニタイズ処理
            //「’」「”」の削除
            $clean['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
            //改行の削除
            $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);
        }
        
        //メッセージ入力確認
        if(empty($_POST['message'])){
            //投稿者名が未入力であることを判別
            $error_message[] = '内容を入力してください。';
        } else {    //サニタイズ処理
            //「’」「”」の削除
            $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
            //改行を<br>に置換
            $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
        }
        
        //エラーがある場合にファイルへの書き込みを行わないための分岐
        if(empty($error_message)){
            //データベースに接続
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            //接続エラーの確認
            if($mysqli -> connect_errno){
                $error_message[] = '書き込みに失敗しました。エラー番号'.$mysqli -> connect_errno.'：'.$mysqli -> connect_error;
            } else {    //データベースへの登録
                //文字コード設定
                $mysqli -> set_charset('utf8');
                
                //書き込み日時の取得
                $now_date = date("Y-m-d H:i:s");
                
                //データの登録用SQL作成
                $sql = "INSERT INTO board(view_name, message, post_date) VALUES ('$clean[view_name]','$clean[message]','$now_date')";
                
                //データの登録
                $res = $mysqli -> query($sql);
                
                //登録の成否を判断
                if($res){
                    $success_massage = 'メッセージを書き込みました。';
                } else {
                    $error_message = '書き込みに失敗しました。';
                }
                
                //データベースへの接続を閉じる
                $mysqli -> close();
            }
        }
    }
    
    //データベースの読み込み
        //データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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
        <title>掲示板</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    
    <body>
        <!-- 投稿成功表示 -->
            <!--投稿成功したかの確認-->
        <?php if(!empty($success_massage)){ ?>
            <!--投稿成功表示-->
            <p class="success_message"><?php echo $success_massage; ?></p>
        <?php } ?>
        
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
        
        <!--入力フォーム-->
        <form method="post">
            <div class="form_input">
                <!--投稿者名入力フォーム-->
                <div class="form_name">
                    <p>投稿者</p>
                    <input id="view_name" type="text" name="view_name" value="">
                </div>
                <!--投稿内容入力フォーム-->
                <div class="form_content">
                    <p>内容</p>
                    <textarea id="message" name="message" value=""></textarea>
                </div>
            </div>
            <!--投稿ボタン-->
            <input type="submit" name="btn_submit" value="投稿">
        </form>
        <hr>
        <hr>
        <section>
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
        </section>
    </body>
</html>