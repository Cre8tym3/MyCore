<?php
date_default_timezone_set('America/New_York');




$myHour = date("G");  
$baseTime = '100'-($myHour*'5');

echo $baseTime;

?>