<?php

$id =$_GET['id'];
$status= $_GET['status'];

$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

############################################################


	$sql="UPDATE DaFeeds set rank='$status' WHERE id='$id'";
	
	echo $sql;
			$result = mysql_query($sql,$db);
?>
<html> <body onload="history.go(-1)"> </body> </html>
