<?php
session_start();
require('../dbconnect.php');
require('../common.php');
// 入力判定
if(!empty($_POST)){
	$post = sanitize($_POST);
	// 項目ごとに入力されていなければエラー変数にblankを代入。
	if($post['name']===''){
		$error['name'] = 'blank';
	}
	if($post['email']===''){
		$error['email'] = 'blank';
	}
	if($post['gender']==='' || !isset($post['gender'])){
		$error['gender'] = 'blank';
	}
	if($post['age']===''){
		$error['age'] = 'blank';
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
	// アカウント重複チェック
   if(empty($error)){
	   $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
	   $member->execute(array($_POST['email']));
	   $record = $member->fetch();
	   if($record['cnt'] > 0){
		   $error['email'] = 'duplicate';
	   }

   }


	if(empty($error)){
		$_SESSION['join'] = $post;
		// 画像がアップされていたら指定の場所に保存
        if(!empty($_FILES['picture']['name'])){
            $_SESSION['join']['image'] = date('YmdHis') . $_FILES['picture']['name'];
            move_uploaded_file($_FILES['picture']['tmp_name'],'../member_picture/' . $_SESSION['join']['image']);
        // 画像がアップされていなければnoneという文字列を指定する
        }else{
			$_SESSION['join']['image'] = 'none';
        }

		header('Location: check.php');
		exit();
	}
}

if($_REQUEST['action']==='rewrite'){
	$post = $_SESSION['join'];
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>
	<link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
	<p>Share movie</p>
</header>

<ul class="nav">
    <li><a href="../login.php" class="button">ログイン画面へ</a></li>
</ul>
<div class="clear_float"></div>

<div class='main'>
	<div class='form'>
		<h2>新規会員情報登録画面</h2>
		<form action="" method="post" enctype="multipart/form-data">
		
			名前<br><input type="text" name="name" value="<?php if(!empty($post['name'])){ echo $post['name'];}?>"><br>
			<?php if($error['name']==='blank'):?>
				<p class="error">名前を入力してください。</p>
			<?php endif; ?>


			email<br><input type="text" name="email" value="<?php if(!empty($post['email'])){ echo $post['email'];}?>"><br>
			<?php if($error['email']==='blank'):?>
				<p class="error">メールアドレスを入力してください。</p>
			<?php endif; ?>
			<?php if($error['email']==='duplicate'):?>
				<p class="error">このメールアドレスはすでに登録されています。</p>
			<?php endif; ?>


			性別<br>
			男:<input type="radio" name="gender" value="man" <?php if(!empty($post['gender']) && $post['gender']==='man'){ echo 'checked';}?>>
			女:<input type="radio" name="gender" value="woman" <?php if(!empty($post['gender']) && $post['gender']==='woman'){ echo 'checked';}?>><br>
			<?php if($error['gender']==='blank'):?>
				<p class="error">性別を選択してください。</p>
			<?php endif; ?>


			年齢<br>
			<select name="age" id="">
			    <option value="">-</option>
			<?php for($i=1;$i<=100;$i++):?>
				<option value="<?php echo $i; ?>" 
				    <?php if((Integer)$post['age']===$i){
					echo 'selected';}?>>
					<?php echo $i; ?>
				</option>
			<?php endfor; ?>
			</select><br>
			<?php if($error['age']==='blank'):?>
				<p class="error">年齢を選択してください。</p>
			<?php endif; ?>


			[パスワード]<br><input type="password" name="password" value="<?php echo $post['password']?>"><br>
			<?php if($error['password']==='blank'):?>
				<p class="error">パスワードを入力してください。</p>
			<?php endif; ?>
			<?php if($error['password']==='length'):?>
				<p class="error">4文字以上で入力してください。</p>
			<?php endif; ?>

			作品画像<br><input type="file" name="picture" size="35"><br><br>
			<?php if($error['image']==='type'):?>
				<p class="error">jpg,gif,png形式のみ登録可能です</p>
			<?php endif;?>
			   

			<input type="submit" value='送信'>

		</form>
	</div>
</div>

<footer>
<p>-END-</p>
</footer>

</body>
</html>
