<?php
session_start();
require('../dbconnect.php');
if(empty($_SESSION['join'])){
	header('Location: index.php');
	exit();
}

if(!empty($_POST)){
	$statement = $db->prepare('INSERT INTO members SET name=?,gender=?,age=?,email=?,password=?,picture=?,created=NOW()');
	$statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['gender'],
		$_SESSION['join']['age'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image'],
	));
	unset($_SESSION['join']);
	header('Location: result.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録確認画面</title>
	<link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
	<p>Share movie</p>
</header>

<div class='main'>
	<div class="form">
		<p>以上の内容で登録してよろしいでしょうか？</p>

		<table>
        <tr>
            <!-- 名前表示 -->
            <th>名前</th>
            <td><?php echo $_SESSION['join']['name'];?></td>
        </tr>
        <tr>
            <th>性別</th>
            <!-- DBでは英語登録されているため条件分岐で日本語に変換 -->
            <?php if($_SESSION['join']['gender']=='woman'):?>
                <td><?php echo '女';?></td>   
            <?php else: ?>
                <td><?php echo '男';?></td>   
            <?php endif; ?>
        </tr>
        <tr>
            <th>年齢</th>
            <td><?php echo $_SESSION['join']['age'];?></td>
        </tr>
        <tr>
            <th>email</th>
            <td><?php echo $_SESSION['join']['email'];?></td>  
        </tr>
    </table>
        
		<img src="../member_picture/<?php echo $_SESSION['join']['image'];?>" alt="">
		
		<form action="" method="post">
			<a href="index.php?action=rewrite">書き直す</a>
			|
			<input type="submit" value="はい" >
			<input type="hidden" value='action' name='result'>
		</form>
	</div>	


</div>

<footer>
<p>-END-</p>
</footer>

</body>
</html>
