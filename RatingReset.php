<?php

$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

 $sql = "SELECT * FROM DaFeeds WHERE rating='89';"; 

	$result = mysql_query($sql,$db);	
	if ($result) { $num_rows = mysql_num_rows($result); } else { $num_rows="0"; }

#############################################################	
		if ($num_rows!="0") {
			while ($myrow = mysql_fetch_array($result)) {	
			 $url = $myrow['url'];
			 $id = $myrow['id'];
			 $rank = $myrow['rank'];
			 $rating = $myrow['rating'];
			 
			 echo "<p> $rating - $id - $rank - $url </p>";
			
			  
			 # Updater #########################
			 $sql5 = "UPDATE DaFeeds SET rating='99' WHERE id =".$id.""; 
			$result5 = mysql_query($sql5,$db);
			 	
			}
		}
?>