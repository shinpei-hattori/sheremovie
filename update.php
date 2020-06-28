<?php
require('dbconnect.php');
require('common.php');
session_start();

// ログイン判定
if(empty($_SESSION['id'])){
	header('Location: login.php');
	exit();
}

// 会員情報を取得
    $statement = $db->prepare('SELECT * FROM members WHERE id=?');
    $statement->execute(array($_SESSION['id']));
    $member = $statement->fetch();

    $checks = $db->prepare('SELECT picture FROM members WHERE id=?');
        $checks->execute(array($_SESSION['id']));
        $check = $checks->fetch();
        


// ボタンが押されたか判定。
if(!empty($_POST)){
    $post = sanitize($_POST);
    // 項目ごとに入力されていなければエラー変数にblankを代入。
    if($post['name']===''){
		$error['name'] = 'blank';
	}
	if($post['email']===''){
		$error['email'] = 'blank';
	}
    if(strlen($post['password']) < 4){
        $error['password'] = 'length';
    }
	if($post['password']===''){
        $error['password'] = 'blank';
    }
     // 画像の拡張子が指定のものかチェック
     $fileName = $_FILES['picture']['name'];
     if(!empty($fileName)){
         $ext = substr($fileName,-3);
         if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
             $error['image'] = 'type';
         }
         
     }
     $image = date('YmdHis') .  $_FILES['picture']['name'];
     if(!empty($fileName) && $image !== $check['picture']){
       unlink('member_picture/' . $check['picture']);
        move_uploaded_file($_FILES['picture']['tmp_name'],'member_picture/' . $image);
     }else{
         $image = $check['picture'];
     }
// エラーがなければDBにUPDATE処理。
    if(empty($error)){
        


        $statement = $db->prepare('UPDATE members SET name=?,email=?,password=?,picture=? WHERE id=?');
        $statement->execute(array(
            $post['name'],
            $post['email'],
            sha1($post['password']),
            $image,
            $_SESSION['id'],
            

        ));
        $_SESSION['name'] = $post['name'];
        // detail.phpに戻る時パラメーターを送信。
        header('Location: detail.php?update=result');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
<div class='wrap'>
        <p>Share movie</p>
    </div>
</header>

<ul class="nav">
    <li><a href="logout.php" class="button">ログアウト</a></li>
    <li><a href="index.php" class="button">トップへ</a></li>
</ul>
<div class="clear_float"></div>

<div class='main'>
<h1>編集画面</h1>
<form action="update.php" method="POST" enctype="multipart/form-data">
    名前<br>
    <input type="text" name="name" value="<?php echo $member['name'];?>"><br>
    <?php if($error['name']==='blank'):?>
        <p class="error">名前を入力してください。</p>
    <?php endif; ?>
    メールアドレス<br>
    <input type="text" name="email" value="<?php echo $member['email'];?>"><br>
    <?php if($error['email']==='blank'):?>
        <p class="error">メールアドレスを入力してください。</p>
    <?php endif; ?>
    パスワード<br>
    <input type="password" name="password" value=""><br>
    <?php if($error['password']==='blank'):?>
        <p class="error">パスワードを入力してください。</p>
    <?php endif; ?>

    <?php if($error['password']==='length'):?>
        <p class="error">４文字以上でを入力してください。</p>
    <?php endif; ?>

    作品画像<br><input type="file" name="picture" size="35"><br><br>
     <?php if($error['image']==='type'):?>
        <p class="error">jpg,gif,png形式のみ登録可能です</p>
     <?php endif;?>

    <input type="submit" value="変更する">

    
</form>
	
</div>

<footer>
<p>-END-</p>
</footer>

</body>
</html>
