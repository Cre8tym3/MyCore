<?php

$DisPlayFeed = $_GET['feed'];
$feed = urldecode($feed);
$nid =$_GET['nid'];
$ry= $_GET['ry'];
$nrank= $_GET['rank'];
$nrating= $_GET['rating'];
$i= $_GET['i'];
$ntitle= $_GET['ntitle'];
$ii = "1";


date_default_timezone_set('America/New_York');
$MyDay=date(d);
$MyYesterday = ($MyDay-"1");
$clockTime = date("h:i A");

function random_pic($dir = 'RavesRuns')
{
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}

$myHour = date("G");  
$baseTime = round('100'-($myHour*'6.9')); // smaller numbers here are long display times
// Auto shift categories per time of the day
$myMinute = date("i"); 
	if($myMinute <= "5") { $ry = "r"; }
	if($myMinute >= "5" && $myMinute <= "10") { $ry = "d"; }
	if($myMinute >= "10" && $myMinute <= "15") { $ry = "l"; }
	if($myMinute >= "15" && $myMinute <= "20") { $ry = "f"; }
	if($myMinute >= "20" && $myMinute <= "30") { $ry = "q"; }
	if($myMinute >= "30" && $myMinute <= "40") { $ry = "v"; }
	if($myMinute >= "40") { $ry = ""; }

// Refresh timer logic	
			if($nrating >= "90") { $myTimer = $baseTime+'5'; }
			if($nrating == "7") { $myTimer = $baseTime+'65'; }
			if($nrating == "6") { $myTimer = $baseTime+'55'; }
			if($nrating == "5") { $myTimer = $baseTime+'45'; }
			if($nrating == "4") { $myTimer = $baseTime+'35'; }
			if($nrating == "3") { $myTimer = $baseTime+'25'; }
			if($nrating == "2") { $myTimer = $baseTime+'15'; }
			if($nrating == "1") { $myTimer = $baseTime+'5'; }
	 /*
	if($ry) { 
			$myTimer = $baseTime+'50'; 
		}else{ 
		
			if($nrating >= "90") { $myTimer = $baseTime+'5'; }
			if($nrating == "7") { $myTimer = $baseTime+'65'; }
			if($nrating == "6") { $myTimer = $baseTime+'55'; }
			if($nrating == "5") { $myTimer = $baseTime+'45'; }
			if($nrating == "4") { $myTimer = $baseTime+'35'; }
			if($nrating == "3") { $myTimer = $baseTime+'25'; }
			if($nrating == "2") { $myTimer = $baseTime+'15'; }
			if($nrating == "1") { $myTimer = $baseTime+'5'; }			
		 } 
		
		 // Found that the longer reading time lead to more distraction
		 $myTimerPlus = $myTimer+$i;
		 echo $myTimerPlus;
		 */

// Conect to DB
$db = mysql_connect("localhost","root","password"); 
mysql_select_db("feeder",$db);

if($ry) { $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rank LIKE '%".$ry."%' && rating<'80' ORDER BY RAND() LIMIT 3) s ORDER BY s.rating ASC"; 
  } else { 
  		// Random dead page every 10
		if($i %10 == 0) {
			$sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rating>'90' ORDER BY RAND() LIMIT 3) s;"; 			
		} else {
		  $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rating<'80' ORDER BY RAND() LIMIT 3) s ORDER BY s.rating ASC;"; 
  } 
}
 // Count pages for random cue
$i++;	
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
			  $title = $myrow['title'];
			 //$i++;
			  
			  if($title) { $temp .= "$rating - $rank <a href='MyCore2.php?feed=".$feeder."&nid=".$id."&ry=".$ry."&rank=".$rank."&rating=".$rating."&i=".$i."&ntitle=".$title."'>".$title."</a><br>";	 }
			  else { $temp .= "$rating - $rank <a href='MyCore2.php?feed=".$feeder."&nid=".$id."&ry=".$ry."&rank=".$rank."&rating=".$rating."&i=".$i."&ntitle=".$title."'>".$feeder."</a><br>";	 }
			  unset($title);
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
<title>FM: <?php echo $ntitle; ?></title>
<link rel="stylesheet" href="MyStyle.css" type="text/css" media="screen">
<style>
 <?php  
 //echo "body { background: url('".random_pic()."') #252a32; } ";
 
echo " #contentBloks:after {
    content: \"\"; background: url(".random_pic().") no-repeat center center fixed; 
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
    opacity: 0.5;  top: 0; left: 0; bottom: 0; right: 0; position: absolute; z-index: -1;  }";
 /*
 if($ry) {
		echo ".chunk { max-width: 16.666%; }";
		echo ".Today { max-width: 33.33%; }"; // 16.66*2=33.33
		echo ".Yesterday { max-width: 25%; }"; // 16.66*1.5=25
	}else{ 
		echo ".chunk { max-width: 14.285%; }";
		echo ".Today { max-width: 28.5%;  font-size: 100%;}"; // 14.28*2=28.5
		echo ".Yesterday { max-width: 21.5%;  font-size: 90%; }"; // 14.28*1.5=21.5
	}
	*/
echo ".chunk { max-width: 20%; }";
echo ".Today { max-width: 40%;  font-size: 100%; }"; // 16.66*2=33.33
echo ".Yesterday { max-width: 30%;  font-size: 90%; }"; // 16.66*1.5=25	
	
// 	6 = 16.666% 7 = 14.285% 8 = 12.5%
if(strpos($nrank,'h') !== false){ 
	echo ".chunk,  .Today,  .Yesterday { max-width: 20%; font-size: 75%; max-height: 630px; }";
	} else {
		echo ".chunk { height: 630px;  }";
	}
if(strpos($nrank,'s') !== false){ 
	echo ".chunk,  .Today,  .Yesterday {  height: 1300px; max-height: 1300px; }";
	} 
if(strpos($nrank,'b') !== false){ 
	//echo ".chunk,  .Today,  .Yesterday { max-height: 630px; }";
	echo ".content {  font-size: 1px; }";
	} 	
?>

</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
$(document).ready(function() {
      $('#bodydemo').delay(15).fadeIn(1000);

      
});
</script>
</head>
<body id="bodydemo"  style="display: none;">

<table id='tools'>
  <tr>
   
    <td>
    
    <form action="MyRank.php" method="get"  class='searchform' name='ranker'>
   
        <!-- If a feed has already been passed through the form, then make sure that the URL remains in the form field. -->
        <input type="hidden" name="id" value="<?php echo $nid; ?>" />
        <label for='status'>&nbsp;Categories:</label>
        <input type="text" name="status" value="<?php  echo $nrank ?>" class="text searchfield" id="feed_input" size='6' />
        <label for='rating'>Rating:</label>
        <input type="text" name="rating" value="<?php  echo $nrating ?>" class="text searchfield" id="feed_input" size='2' />
        
       <input type="submit" value="rank" class="button searchbutton" />
        &nbsp;-<br />
         <span class='next'><?php  echo $DisPlayFeed; ?>&nbsp;<?php  echo "(".$nid.")"; ?></span>
      </form>
      <script type="text/javascript">
 document.ranker.rating.focus();
</script></td>
    <td><p class='next'><?php  echo "$temp"; ?></p></td>
    <td>
<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,'name','height=600,width=450');
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
</script>
<p class='next'>
<a href="README.txt" onclick="return popitup('README.txt')">Category Key</a><br />
<a href="http://paggis.library.musc.edu/~paggis/phpmyadmin/index.php">PHPMyAdmin</a><br />


	<?php echo "" .$clockTime."</p>";  ?>
</td>
 <td>
    <form name="redirect" class='searchform'>
      <form class='searchform'>
        <input type="text" size="3" name="redirect2"  class="searchfield"><br />
         
      </form>
      <script>
<!--

/*
Count down then redirect script
By JavaScript Kit (http://javascriptkit.com)
Over 400+ free scripts here!
*/

//change below target URL to your own
var targetURL="MyCore2.php?feed=<?php  echo $feeder; ?>&nid=<?php  echo $id; ?>&ry=<?php  echo $ry; ?>&rank=<?php  echo $rank; ?>&rating=<?php  echo $rating; ?>&i=<?php  echo $i; ?>&ntitle=<?php  echo $title; ?>"
//change the second to start counting down from
var countdownfrom=
<?php  
		
		 echo $myTimer;
?>

var currentsecond=document.redirect.redirect2.value=countdownfrom+1
	function countredirect(){
		if (currentsecond!=1){
		currentsecond-=1
		document.redirect.redirect2.value=currentsecond
		}else{
			window.location=targetURL
			return
		}
			setTimeout("countredirect()",1000)
	}

countredirect()
//-->
</script>
  <p><?php  echo $i; ?></p></td>

  <td>
 <a> <h1><?php  echo $ry; ?></h1></a>
  </td>
  </tr>
</table>

<div id='contentBloks'>

<?php
if(strpos($nrank,'h') !== false) { echo "<div class='clearfix'>"; }

			// Check to see if there are more than zero errors (i.e. if there are any errors at all)
			if ($feed->error())
			{
				// If so, start a <div> element with a classname so we can style it.
				echo '<div class="sp_errors">' . "\r\n";

					// ... and display it.
					echo '<p>' . htmlspecialchars($feed->error()) . "</p>\r\n";

				// Close the <div> element we opened.
				echo '</div>' . "\r\n";
			}
			?>
<div id="sp_results">

<!-- As long as the feed has data to work with... -->
<?php if ($success): ?>
<div class="chunk focus" align="center"> 
  
  <!-- If the feed has a link back to the site that publishes it (which 99% of them do), link the feed's title to it. -->
  <h3 class="header">
    <?php
	$blogTitle=$feed->get_title();
	 if ($feed->get_link()) echo '<a href="' . $feed->get_link() . '">'; echo $blogTitle; if ($feed->get_link()) echo '</a>'; ?>
  </h3>
  
  <!-- If the feed has a description, display it. --> 
  <?php echo $feed->get_description(); ?> </div>

<!-- Let's begin looping through each individual news item in the feed. -->
<?php foreach($feed->get_items() as $item): 
$ii++; ?>

<div class="chunk 
	<?php 
    // Note with CSS class Todays and Yesterdays posts
    if($MyDay==$item->get_date('j')) { echo " Today"; } if($MyYesterday==$item->get_date('j')) { echo " Yesterday"; }  
     ?>" id="box<?php  echo $ii; ?>"> 
  
  <!-- If the item has a permalink back to the original post (which 99% of them do), link the item's title to it. -->
  <h4>
    <?php if ($item->get_permalink()) echo '<a href="' . $item->get_permalink() . '">'; echo $item->get_title(); if ($item->get_permalink()) echo '</a>'; ?>
    <br / >
    <span>( <?php echo $item->get_date('j M Y, g:i a'); ?> )</span></h4>
  
  <!-- Display the item's primary content. --> 
  <?php 
  
 // Height deleter
$content = str_replace("height=", "", $item->get_content()); 
  
echo "<div class='content'<p>".$content."";						
						
						
						// Check for enclosures.  If an item has any, set the first one to the $enclosure variable.
						if ($enclosure = $item->get_enclosure(0))
						{
							// Use the embed() method to embed the enclosure into the page inline.
							echo '<div align="center">';
							echo '<p>' . $enclosure->embed(array(
								'audio' => './for_the_demo/place_audio.png',
								'video' => './for_the_demo/place_video.png',
								'mediaplayer' => './for_the_demo/mediaplayer.swf',
								'altclass' => 'download'
							)) . '</p></div>';

							if ($enclosure->get_link() && $enclosure->get_type())
							{
								echo '<p class="footnote" align="center">(' . $enclosure->get_type();
								if ($enclosure->get_size())
								{
									echo '; ' . $enclosure->get_size() . ' MB';
								}
								echo ')</p>';
							}
							if ($enclosure->get_thumbnail())
							{
								echo '<div><img src="' . $enclosure->get_thumbnail() . '" alt="" /></div>';
							}
							//echo $ii;
							//echo $ii %7 ;
							
							echo '</div>';
							if(($ii %5 == 0)&&(strpos($nrank,'h') !== false)) { echo "</div>\n<div class='clearfix'>"; }
							
						}
						?> </div>

<!-- Stop looping through each item once we've gone through all of them. -->
<?php
/*
// Was for the name every 5 "chunks" 1-5 for CSS tranisistions (loaded poorly and made some "chucks" not visable?)
$i++;
if($i > 5) { $i="1"; }
*/
endforeach;			 
 ?>

<!-- From here on, we're no longer using data from the feed. -->
<?php endif; ?>
</div>
</div>
<?php
 # Updater #########################
			 $sql5 = "UPDATE DaFeeds SET title='".$blogTitle."' WHERE id =".$nid.""; 
			 //echo $sql5;
			$result5 = mysql_query($sql5,$db);
?>

</body>
</html>
