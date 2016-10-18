<?php
$my_account =$_GET['my_account'];
$pos = strpos($my_account , "explore/tags"); // checks if this is a TAG feed or Profile feed

//returns a big old hunk of JSON from a non-private IG account page.
function scrape_insta($username) {
	$insta_source = file_get_contents('http://instagram.com/'.$username);
	$shards = explode('window._sharedData = ', $insta_source);
	$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array;
}
//Supply a username
//$my_account = 'shermanpaggi'; 
//$my_account = 'explore/tags/red/'; 

//Do the deed
$results_array = scrape_insta($my_account);

$i = 0;
$image = 1;
while ($image) {

//An example of where to go from there
if($pos !== false) { $latest_array = $results_array['entry_data']['TagPage'][0]['tag']['media']['nodes'][$i]; }
	else { $latest_array = $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]; }
$timestamp = $latest_array['date'];
$Time = gmdate("Y-m-d\TH:i:s\Z", $timestamp);
$image = $latest_array['display_src'];
//echo 'Latest Photo:<br/>';
//echo '<a href="http://instagram.com/p/'.$latest_array['code'].'"><img src="'.$latest_array['display_src'].'"></a></br>';
//echo 'Likes: '.$latest_array['likes']['count'].' - Comments: '.$latest_array['comments']['count'].'<br/>';
$body.=" <item>
       <title>".$my_account."</title>
       <link>".$image."</link>
       <description>
	<![CDATA[<img src='".$latest_array['display_src']."'>
  ".$latest_array['caption']." - Likes: ".$latest_array['likes']['count']." - Comments: ".$latest_array['comments']['count']."]]>
	</description>
       <pubDate>".$Time."</pubDate>
    </item>
    ";

//BAH! An Instagram site redesign in June 2015 broke quick retrieval of captions, locations and some other stuff.
//echo 'Taken at '.$latest_array['location']['name'].'<br/>';
$i++;
}
echo "<?xml version='1.0'?>
<rss version='2.0'>
  <channel>
    <title>".$my_account." Instagram</title>
    <link>https://www.instagram.com/".$my_account."/</link>
    <description>".$my_account." Instagram channel</description>
   ".$body."
  </channel>
</rss>";
?>

