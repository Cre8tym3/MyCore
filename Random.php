<?php
$feeder = "http://blog.fitc.ca/feeds/atom.cfm";
?>
<html>
<head>
<script type="text/javascript">
<!--
function delayer(){
    window.location = "MyCore.php?feed=<? echo $feeder; ?>"
}
//-->
</script>
</head>
<body onLoad="setTimeout('delayer()', 5000)">
<h2>Prepare to be redirected!</h2>
<p>This page is a time delay redirect, please update your bookmarks to our new 
location!</p>

</body>
</html>

