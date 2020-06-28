<?php
try{

    //  tryの中に記述
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    
    $db_name = substr($url["path"], 1);
    $db_host = $url["host"];
    $user = $url["user"];
    $password = $url["pass"];
        
    $dsn = "mysql:dbname=".$db_name.";host=".$db_host;

    $db=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    } catch (PDOException $e){
    //　例外処理を記述
        echo 'DB接続エラー:' . $e->getMessage();
    }