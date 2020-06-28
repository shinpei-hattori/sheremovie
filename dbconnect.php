<?php
try{

    //  tryの中に記述
    $db = parse_url($_SERVER['mysql://b83e51344a27d3:5ef371b3@us-cdbr-east-02.cleardb.com/heroku_5959409df845877?reconnect=true
    ']);
    $db['heroku_5959409df845877'] = ltrim($db['path'], '/');
    $dsn = "mysql:host={$db['us-cdbr-east-02.cleardb.com']};dbname={$db['heroku_5959409df845877']};charset=utf8";
    $user = $db['b83e51344a27d3'];
    $password = $db['5ef371b3'];
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );

    $dbh = new PDO($dsn,$user,$password,$options);
    } catch (PDOException $e){
    //　例外処理を記述
        echo 'DB接続エラー:' . $e->getMessage();
    }