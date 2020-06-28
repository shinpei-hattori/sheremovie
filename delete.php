<?php
require('dbconnect.php');
session_start();

$delete = $db->prepare('DELETE FROM movies WHERE id=?');
$delete->execute(array($_REQUEST['id']));
header('Location: detail.php?id=' . $_REQUEST['member_id']  );
exit();



