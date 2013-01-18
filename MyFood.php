<!DOCTYPE html>

<html lang="en-US">
<body>
<?php



// Conect to DB
$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

$sql = "SELECT * FROM DaFeeds WHERE rank LIKE '%m%' ORDER BY rating DESC"; 


	$result = mysql_query($sql,$db);	
	if ($result) { $num_rows = mysql_num_rows($result); } else { $num_rows="0"; }

#############################################################	
		if ($num_rows!="0") {
			while ($myrow = mysql_fetch_array($result)) {	
			 $feeder = $myrow['url'];
			 $id = $myrow['id'];
			 $rank = $myrow['rank'];
			 $rating = $myrow['rating'];
			  
			 echo "$rating - $rank $feeder<br />";		
			}
		}	

?>
</body>
</html>