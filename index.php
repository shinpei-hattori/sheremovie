<?php
session_start();
require('dbconnect.php');
require('common.php');
date_default_timezone_set('Asia/Tokyo');
// ログイン判定
if(empty($_SESSION['id'])){
	header('Location: login.php');
	exit();
}
// ボタンが押されたかチェック
if(!empty($_POST)){
    $post = sanitize($_POST);
	// 項目ごとに入力されていなければエラー変数にblankを代入。
	if($post['title']===''){
		$error['title'] = 'blank';
	}
	if($post['score']===''){
		$error['score'] = 'blank';
	}
	if($post['content']===''){
		$error['content'] = 'blank';
    }
    // 画像の拡張子が指定のものかチェック
    $fileName = $_FILES['m_picture']['name'];
    if(!empty($fileName)){
        $ext = substr($fileName,-3,3);

        var_dump($ext);
        
        if($ext != 'jpg' && $ext != 'gif' && $ext != 'png' && $ext != 'JPG' && $ext != 'peg'){
            $error['image'] = 'type';
        }
        
    }
    
    // エラーがなければの処理
    if(empty($error)){
        // 画像がアップされていたら指定の場所に保存
        if(!empty($_FILES['m_picture']['name'])){
            $image = date('YmdHis') . $_FILES['m_picture']['name'];
            move_uploaded_file($_FILES['m_picture']['tmp_name'],'movie_picture/' . $image);

        // 画像がアップされていなければnoneという文字列を指定する
        }else{
			$image = 'none';
        }
        // 投稿内容をDBに保存
        $movies = $db->prepare('INSERT INTO movies SET member_id=?,title=?,score=?,content=?,m_picture=?,created=NOW()');
        $movies->execute(array(
            $_SESSION['id'],
            $post['title'],
            $post['score'],
            $post['content'],
            $image
        ));
        header('Location: index.php');
        exit();
    }
}
// ユーザーの投稿内容を取得。
$tables = $db->query('SELECT m.name,v.* FROM movies v , members m WHERE m.id = v.member_id ORDER BY v.created DESC');


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
    <div>
        <p>Share movie</p>
    </div>
</header>

<ul class="nav">
    <li><a href="logout.php?id=<?php echo $_SESSION['id'];?>" class="button">ログアウト</a></li>
    <li><a href="detail.php?id=<?php echo $_SESSION['id'];?>" class="button">アカウント</a></li>
</ul>

<div class="clear_float"></div>



<div class='main'>
    
    <p class="gest">ようこそ<?php echo $_SESSION['name'];?>さん</p>
    <h1>おすすめ映画を投稿しよう！</h1>
    
<!-- ユーザーの投稿を表示 -->

	<form action="" method="post" enctype="multipart/form-data">
     タイトル<br><input type="text" name="title" value="<?php echo $post['title']; ?>"><br>
     <?php if($error['title']==='blank'):?>
        <p class="error">タイトルが未記入です</p>
     <?php endif;?>


     評価点<br><input type="number" name="score" value="<?php echo $post['score']; ?>">点<br>
     <?php if($error['score']==='blank'):?>
        <p class="error">評価点が未記入です</p>
     <?php endif;?>


     おすすめ内容<br><textarea name="content"><?php echo $post['content']; ?></textarea><br>
     <?php if($error['content']==='blank'):?>
        <p class="error">おすすめ内容が未記入です</p>
     <?php endif;?>

     作品画像<br><input type="file" name="m_picture" size="35"><br><br>
     <?php if($error['image']==='type'):?>
        <p class="error">jpg,gif,png形式のみ登録可能です</p>
     <?php endif;?>
     <input type="submit" value="登録">    
	</form>
	

	
	
	
	
	<div class="tables" >
    <!-- foreachで投稿の内容を表示 -->
        <?php foreach($tables as $table):?>
        <div class="table">
         <!-- 画像がなければdefalut.png、あればリンクを代入。 -->
            <?php if($table['m_picture']==='none'){
                $picture = 'defalut.png';
            }else{
                $picture = 'movie_picture/' . $table['m_picture'];
            }
            ?>
        <!-- 登録画像を表示 -->
            <img src="<?php echo $picture; ?>" alt="" >
            <ul>
                <!-- タイトルを表示 -->
                <li><p class="title"><?php echo $table["title"];?></p></li>
                <!-- 投稿日時を表示 -->
                <li><time class="created"><?php echo $table['created'];?></time></li>
                <!-- リンク付きユーザー名を表示。IDパラメーターを付与。 -->
                <li><a href="detail.php?id=<?php echo $table['member_id'];?>" class="name_link"><?php echo $table['name']; ?></a></li>
                
            </ul>
            <div class="clear_float"></div>
        <!-- 内容を表示 -->  <!-- 点数を表示 -->
            <p class="content_head">おすすめ内容</p><p class="score"><?php echo $table['score'];?>点</p>
            <div class="clear_float"></div>
            <hr><p class="content"><?php echo $table['content'];?></p>
        </div>
        <?php endforeach; ?>
    </div>
	

</div>

<footer>
<p>-END-</p>
</footer>

</body>
</html>
