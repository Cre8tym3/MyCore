<html>
<head>
<!-- For ease i'm just using a JQuery version hosted by JQuery- you can download any version and link to it locally -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script>
 $(document).ready(function() {
 	 $("#responsecontainer").load("people.txt");
   var refreshId = setInterval(function() {
      $("#responsecontainer").load('people.txt?randval='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});
</script>
<link rel="stylesheet" href="MyStyle.css" type="text/css" media="screen">
<style>
.chunk {  
width: <? if($ry){ echo "16.666"; }else{ echo "14.285"; } ?>%;  }
<!--
6 = 16.666% 
7 = 14.285%
8 = 12.5%
-->
</style>
</head>
<body>
 
<div id="responsecontainer">
</div>
</body>