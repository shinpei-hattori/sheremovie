<?php
try{

    //  tryの中に記述
        $db = new PDO('mysql:dbname=heroku_5959409df845877;host=us-cdbr-east-02.cleardb.com;port=3306;charset=utf8','b83e51344a27d3','5ef371b3');
    } catch (PDOException $e){
    //　例外処理を記述
        echo 'DB接続エラー:' . $e->getMessage();
    }