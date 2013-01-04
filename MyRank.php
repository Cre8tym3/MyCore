<?php

$id =$_GET['id'];
$status= $_GET['status'];
$rating= $_GET['rating'];

$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

############################################################


	$sql="UPDATE DaFeeds set rank='$status', rating='$rating' WHERE id='$id'";
	
	echo "<center><h1>".$sql."</h1></center>";
			$result = mysql_query($sql,$db);
?>
<html> <body onload="history.go(-1)"> </body> </html>
