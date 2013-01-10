<?php

$DisPlayFeed = $_GET['feed'];
$feed = urldecode($feed);
$nid =$_GET['nid'];
$ry= $_GET['ry'];
$nrank= $_GET['rank'];
$nrating= $_GET['rating'];
$i= $_GET['i'];
$ii = "1";

function random_pic($dir = 'RavesRuns')
{
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}


date_default_timezone_set('America/New_York');
$MyDay=date(d);
$MyYesterday = ($MyDay-"1");


$db = mysql_connect("localhost","root","password");
mysql_select_db("feeder",$db);

if($ry) { $sql = "SELECT * FROM (SELECT * FROM DaFeeds WHERE rank LIKE '%".$ry."%' && rating<'80' ORDER BY RAND() LIMIT 3) s ORDER BY s.rating ASC"; 
  } else { 
  		// Random dead page every ~5
		if($i %5 == 0) {
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
			 //$i++;
			  
			 $temp .= "$rating - $rank <a href='MyCore.php?feed=".$feeder."&nid=".$id."&ry=".$ry."&rank=".$rank."&rating=".$rating."&i=".$i."'>".$feeder."</a><br>";		
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
<link rel="stylesheet" href="MyStyle.css" type="text/css" media="screen">
<style>
 <? 
 //echo "body { background: url('".random_pic()."') #252a32; } ";
 
echo " #sp_results:after {
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
echo ".chunk { max-width: 16.666%; }";
echo ".Today { max-width: 33.33%; }"; // 16.66*2=33.33
echo ".Yesterday { max-width: 25%; }"; // 16.66*1.5=25	
	
// 	6 = 16.666% 7 = 14.285% 8 = 12.5%
if(strpos($nrank,'h') !== false){ 
	echo ".chunk,  .Today, .Yesterday { max-width: 14.285%; font-size: 75%; max-height: 530px; }";
	} else {
		echo ".chunk { height: 530px;  }";
	}
	

?>
</style>
</head>

<body id="bodydemo" >
<table id='tools'>
  <tr>
    <td>
    <form name="redirect" class='searchform'>
      <form class='searchform'>
        <input type="text" size="3" name="redirect2"  class="searchfield"><br />
         <center>
           <span class='next'>
           <? echo $ry;
		   	echo $i; ?>
           </span>
         </center>
      </form>
      <script>
<!--

/*
Count down then redirect script
By JavaScript Kit (http://javascriptkit.com)
Over 400+ free scripts here!
*/

//change below target URL to your own
var targetURL="MyCore.php?feed=<? echo $feeder; ?>&nid=<? echo $id; ?>&ry=<? echo $ry; ?>&rank=<? echo $rank; ?>&rating=<? echo $rating; ?>&i=<? echo $i; ?>"
//change the second to start counting down from
var countdownfrom=
<? 
	if($ry) { 
			$myTimer = "65"; 
		}else{ 
			if($nrating >= "90") { $myTimer = "20"; }
			if($nrating == "5") { $myTimer = "60"; }
			if($nrating == "4") { $myTimer = "50"; }
			if($nrating == "3") { $myTimer = "40"; }
			if($nrating == "2") { $myTimer = "30"; }
			if($nrating == "1") { $myTimer = "20"; }			
		 } 
		 $myTimerPlus = $myTimer+$i;
		 echo $myTimerPlus;
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
</script></td>
    <td>
    
    <form action="MyRank.php" method="get"  class='searchform' name='ranker'>
   
        <!-- If a feed has already been passed through the form, then make sure that the URL remains in the form field. -->
        <input type="hidden" name="id" value="<?php echo $nid; ?>" />
        <label for='status'>&nbsp;Categories:</label>
        <input type="text" name="status" value="<? echo $nrank ?>" class="text searchfield" id="feed_input" size='6' />
        <label for='rating'>Rating:</label>
        <input type="text" name="rating" value="<? echo $nrating ?>" class="text searchfield" id="feed_input" size='2' />
        
        <input type="submit" value="rank" class="button searchbutton" />
        &nbsp;<br />
         <span class='next'><? echo $DisPlayFeed; ?>&nbsp;<? echo "(".$nid.")"; ?></span>
      </form>
      <script type="text/javascript">
 document.ranker.rating.focus();
</script></td>
    <td><p class='next'><? echo "$temp"; ?></p></td>
    <td>
    
    <script type="text/javascript">
<!--
	var currentTime = new Date()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()

	if (minutes < 10)
	minutes = "0" + minutes

	var suffix = "am";
	if (hours >= 12) {
	suffix = "pm";
	hours = hours - 12;
	}
	if (hours == 0) {
	hours = 12;
	}

	document.write("<h4><span>" + hours + ":" + minutes + " " + suffix + "</span></h4>")
//-->
</script>
</td>
  </tr>
</table>

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
    <?php if ($feed->get_link()) echo '<a href="' . $feed->get_link() . '">'; echo $feed->get_title(); if ($feed->get_link()) echo '</a>'; ?>
  </h3>
  
  <!-- If the feed has a description, display it. --> 
  <?php echo $feed->get_description(); ?> </div>

<!-- Let's begin looping through each individual news item in the feed. -->
<?php foreach($feed->get_items() as $item): 
$ii++; ?>

<div class="chunk <?php if($MyDay==$item->get_date('j')) { echo " Today"; } if($MyYesterday==$item->get_date('j')) { echo " Yesterday"; }   ?>" id="box<? echo $ii; ?>"> 
  
  <!-- If the item has a permalink back to the original post (which 99% of them do), link the item's title to it. -->
  <h4>
    <?php if ($item->get_permalink()) echo '<a href="' . $item->get_permalink() . '">'; echo $item->get_title(); if ($item->get_permalink()) echo '</a>'; ?>
    <br / >
    <span>( <?php echo $item->get_date('j M Y, g:i a'); ?> )</span></h4>
  
  <!-- Display the item's primary content. --> 
  <?php echo "<p>".$item->get_content()."";						
						
						
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
							)) . '</p>';

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
							if(($ii %7 == 0)&&(strpos($nrank,'h') !== false)) { echo "</div>\n<div class='clearfix'>"; }
							
						}
						?> </div>

<!-- Stop looping through each item once we've gone through all of them. -->
<?php
				$i++;
				if($i > 5) { $i="1"; }
				 endforeach; ?>

<!-- From here on, we're no longer using data from the feed. -->
<?php endif; ?>
</div>
</body>
</html>
