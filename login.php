<?php
session_start();
require('dbconnect.php');
// クッキーにメールアドレスを保持する
if($_COOKIE['email'] !== ''){
    $email = $_COOKIE['email'];
}

// ボタンが押されたかチェック
if(!empty($_POST)){
    // ポストで入力があればクッキーの変数にポストの値を代入する
    $email = $_POST['email'];
	// 項目ごとに入力されていなければエラー変数にblankを代入。
	if($_POST['email']===''){
		$error['email'] = 'blank';
	}
	if($_POST['password']===''){
        $error['password'] = 'blank';
    }
    //エラーがなければemail,pass が存在するかチェック。
    if(empty($error)){
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password'])
        ));
        $member = $login->fetch();
    // アカアントがあればセッションにid,name,現在時刻をセット
        if($member){
            $_SESSION['id'] = $member['id'];
            $_SESSION['name'] = $member['name'];
            $_SESSION['time'] = time();
            // emailの値を保持にチェックがあればクッキーにセット。
            if($_POST['save']==='on'){
                setcookie('email',$_POST['email'],time()+60*60*24*14);
            }
            header('Location: index.php');
            exit();
        }else{
            // アカウントがなければエラーを出す
            $error['login'] = 'failed';
        }

    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ログイン画面</title>
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
	<p>Share movie</p>
</header>

<ul class="nav">
    <li><a href="join/index.php" class="button">新規会員登録へ</a></li>
</ul>
<div class="clear_float"></div>

<!--  入力判定  -->
<div class='main'>
   <form action="" method='post' value=''>
   <p>ログイン情報を入力してください。</p>
   <?php if($error['login']==='failed'):?>
    <p>メールアドレスまたはパスワードが間違っています。</p>
   <?php endif;?>
   メールアドレス:<br>
    <input type="text" name='email' value='<?php echo $email;?>'><br>
    <?php if($error['email']==='blank'):?>
     <p class='error'>メールアドレスを入力してください。</p>
    <?php endif; ?>


   パスワード:<br>
    <input type="password" name='password' value='<?php echo $_POST['password'];?>'><br>
    <?php if($error['password']==='blank'):?>
     <p class='error'>パスワードを入力してください。</p>
    <?php endif; ?>
    メールアドレスを保持する
    <input type="checkbox" name="save" value="on"><br><br>
    <input type="submit" value='ログイン'>
   </form>
	
</div>

<footer>
<p>-END-</p>
</footer>

</body>
</html>
