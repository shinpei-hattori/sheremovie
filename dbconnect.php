<?php
try{

    //  tryの中に記述
        $db = new PDO('mysql:dbname=movies_db;host=localhost;port=8889;charset=utf8','root','root');
    } catch (PDOException $e){
    //　例外処理を記述
        echo 'DB接続エラー:' . $e->getMessage();
    }