<?php
require('dbconnect.php');
session_start();
// ログイン判定
if(empty($_SESSION['id'])){
	header('Location: login.php');
	exit();
}
// 送られてきたIDパラメータを＄idに格納。
$id = $_REQUEST['id'];
// update.phpから戻ってきた場合は＄idにセッションのIDを格納。update.phpから戻ってきた場合は＄idの値が破棄されているため。
if($_REQUEST['update']==='result'){
    $id = $_SESSION['id'];
}
// 会員の投稿履歴を取得
$stmt = $db->prepare('SELECT m.name,v.* FROM movies v , members m WHERE m.id = v.member_id  AND member_id=? ORDER BY v.created DESC');
$stmt->execute(array($id));
$tables = $stmt->fetchAll();

// 会員情報を取得
$acounts = $db->prepare('SELECT * FROM members WHERE id=?');
$acounts->execute(array($id));
$acount = $acounts->fetch();





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
    <?php if($_SESSION['id']===$_REQUEST['id'] || $_REQUEST['update']==='result'):?>
   <li><a href="update.php" class="button">会員情報を編集する</a></li>
    <?php endif; ?>
   <li><a href="index.php" class="button">トップへ</a></li>
        
</ul>
<div class="clear_float"></div>   

  <div class="main">
　  <h1>会員情報画面</h1>
	<div class="acount">
    <table>
        <tr>
            <!-- 名前表示 -->
            <th>名前</th>
            <td><?php echo $acount['name'];?></td>
        </tr>
        <tr>
            <th>性別</th>
            <!-- DBでは英語登録されているため条件分岐で日本語に変換 -->
            <?php if($acount['gender']=='woman'):?>
                <td><?php echo '女';?></td>   
            <?php else: ?>
                <td><?php echo '男';?></td>   
            <?php endif; ?>
        </tr>
        <tr>
            <th>年齢</th>
            <td><?php echo $acount['age'];?></td>
        </tr>
        <tr>
            <?php if($id===$_SESSION['id']):?> 
            <th>email</th>
            <?php endif; ?>
            <?php if($id===$_SESSION['id']):?> 
            <td><?php echo $acount['email'];?></td>  
            <?php endif; ?>
        </tr>
    </table>
        
<!-- 画像がなければ,dafalut.pngを代入。あればリンクを代入 -->
        <?php 
        if($acount['picture']==='none'){
            $picture = 'defalut.png';
        } else{
            $picture = 'member_picture' . '/' . $acount['picture'];
        }
        ?>
        <p>プロフィール画像</p>
        <p><img src="<?php echo $picture; ?>" alt=""></p>
	</div>
   


<!-- 会員の投稿履歴を表示 -->
<h1>投稿履歴</h1>

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
            <img src="<?php echo $picture; ?>" alt="" width="100" height="100">
            <ul>
                <!-- タイトルを表示 -->
                <li><p class="title"><?php echo $table["title"];?></p></li>
                <!-- 投稿日時を表示 -->
                <li><time class="created"><?php echo $table['created'];?></time></li>
                <?php if($id==$_SESSION['id']): ?>
                    <li><a href="delete.php?id=<?php echo $table['id']; ?>&member_id=<?php echo $table['member_id']; ?>">削除</a></li>
                <?php endif; ?>
                <!-- リンク付きユーザー名を表示。IDパラメーターを付与。 -->
                <!-- <li><a href="detail.php?id=<?php echo $table['member_id'];?>"><?php echo $table['name']; ?></a></li> -->
        
                <!-- <li><p><?php echo $table['score'];?>点</p></li> -->
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
