<?php

$feed = urldecode($feed);
$nid =$_GET['nid'];
$ry= $_GET['ry'];
$nrank= $_GET['rank'];
$nrating= $_GET['rating'];

date_default_timezone_set('America/New_York');
$MyDay=date(d);

$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

if($ry) { $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rank LIKE '%".$ry."%' && rating<'90' ORDER BY RAND() LIMIT 3) s ORDER BY s.rating ASC"; }
  else { $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rating<'90' ORDER BY RAND() LIMIT 3) s ORDER BY s.rating ASC;"; }
  // Lets rate up some sleepers
  //else { $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rating='0' ORDER BY RAND() LIMIT 2) s ORDER BY s.rating ASC;"; } 

	$result = mysql_query($sql,$db);	
	if ($result) { $num_rows = mysql_num_rows($result); } else { $num_rows="0"; }

#############################################################	
		if ($num_rows!="0") {
			while ($myrow = mysql_fetch_array($result)) {	
			 $feeder = $myrow['url'];
			 $id = $myrow['id'];
			 $rank = $myrow['rank'];
			 $rating = $myrow['rating'];
			  
			}
		}

// Start counting time for the page load
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Include SimplePie
// Located in the parent directory
include_once('../autoloader.php');
include_once('../idn/idna_convert.class.php');

// Create a new instance of the SimplePie object
$feed = new SimplePie();

//$feed->force_fsockopen(true);

if (isset($_GET['js']))
{
	SimplePie_Misc::output_javascript();
	die();
}

// Make sure that page is getting passed a URL
if (isset($_GET['feed']) && $_GET['feed'] !== '')
{
	// Strip slashes if magic quotes is enabled (which automatically escapes certain characters)
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		$_GET['feed'] = stripslashes($_GET['feed']);
	}

	// Use the URL that was passed to the page in SimplePie
	
	$feed->set_feed_url($_GET['feed']);
}

// Allow us to change the input encoding from the URL string if we want to. (optional)
if (!empty($_GET['input']))
{
	$feed->set_input_encoding($_GET['input']);
}

// Allow us to choose to not re-order the items by date. (optional)
if (!empty($_GET['orderbydate']) && $_GET['orderbydate'] == 'false')
{
	$feed->enable_order_by_date(false);
}

// Trigger force-feed
if (!empty($_GET['force']) && $_GET['force'] == 'true')
{
	$feed->force_feed(true);
}

// Initialize the whole SimplePie object.  Read the feed, process it, parse it, cache it, and
// all that other good stuff.  The feed's information will not be available to SimplePie before
// this is called.
$success = $feed->init();

// We'll make sure that the right content type and character encoding gets set automatically.
// This function will grab the proper character encoding, as well as set the content type to text/html.
$feed->handle_content_type();

// When we end our PHP block, we want to make sure our DOCTYPE is on the top line to make
// sure that the browser snaps into Standards Mode.
?>

<!DOCTYPE html>

<html lang="en-US">
<head>
<title>Feed Monster</title>

</head>

<body id="bodydemo" >
<table id='tools'>
  <tr>
    <td><form name="redirect" class='searchform'>
      <form class='searchform'>
        <input type="text" size="3" name="redirect2"  class="searchfield">
      </form>
      <script>
<!--

/*
Count down then redirect script
By JavaScript Kit (http://javascriptkit.com)
Over 400+ free scripts here!
*/

//change below target URL to your own
var targetURL="temp.php?feed=<? echo $feeder; ?>&nid=<? echo $id; ?>&ry=<? echo $ry; ?>&rank=<? echo $rank; ?>&rating=<? echo $rating; ?>"
//change the second to start counting down from
var countdownfrom=30


var currentsecond=document.redirect.redirect2.value=countdownfrom+1
function countredirect(){
if (currentsecond!=1){
currentsecond-=1
document.redirect.redirect2.value=currentsecond
}
else{
window.location=targetURL
return
}
setTimeout("countredirect()",1000)
}

countredirect()
//-->
</script></td>
    <td><form action="MyRank.php" method="get"  class='searchform' name='ranker'>
        <!-- If a feed has already been passed through the form, then make sure that the URL remains in the form field. -->
        <input type="hidden" name="id" value="<?php echo $nid; ?>" />
        <label for='status'>&nbsp;Categories:</label>
        <input type="text" name="status" value="<? echo $nrank ?>" class="text searchfield" id="feed_input" size='6' />
        <label for='rating'>Rating:</label>
        <input type="text" name="rating" value="<? echo $nrating ?>" class="text searchfield" id="feed_input" size='2' />
        &nbsp;<span><? echo "(".$nid.")"; ?></span>
        <input type="submit" value="rank" class="button searchbutton" />
        &nbsp;
      </form>
      
 <script type="text/javascript">
 document.ranker.rating.focus();
</script>
      
      </td>
    <td><p class='next'><? echo "$ry $temp"; ?></p></td>
  </tr>
</table>


<?php




?>


<!-- As long as the feed has data to work with... -->
<?php if ($success): ?>

  


<!-- Let's begin looping through each individual news item in the feed. -->
<?php foreach($feed->get_items() as $item): ?>
	<?php if($MyDay==$item->get_date('j')) { 
	$LoadMe .= "<div class='chunk'>";
	$LoadMe .= $feed->get_title();
	$LoadMe .= "<a href='".$item->get_permalink().">".$item->get_title()."</a>";
	
	$LoadMe .= $item->get_content();
	$LoadMe .= "</div>";


	} 

 endforeach; ?>

<!-- From here on, we're no longer using data from the feed. -->
<?php endif; ?>


<?php
$file = 'people.txt';
// Append a new person to the file
$current = "".$LoadMe."\n";
// Open the file to get existing content
$current .= file_get_contents($file);

// Write the contents back to the file
file_put_contents($file, $current);
?>




</body>
</html>

