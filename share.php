<?php

include('settings.php');
include('simplehtmldom/simple_html_dom.php');

if (strrpos($_SERVER['HTTP_USER_AGENT'], "acebookexternalhit")){
	echo "<html><head>";
	if (isset($_GET['inspect'])) {
		$query = "SELECT restaurant_id, location
			FROM restaurant WHERE restaurant_id = ".intval($_GET['inspect'])." AND active = 1";
		$result = queryResult($query);
		$locations = array();
		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_assoc($result);
			$string = "Check out the latest Food Inspection information for {$row["location"]}";
		} else {
			$string = "eatsure - London Food Inspection Scores";
		}
	} else {
		$string = "eatsure - London Food Inspection Scores";
	}
	echo "<title>$string<title>";
	echo "<meta name='description' content='$string' />";
	echo "</head><body>";
	echo "<img src='/images/fbimage.png' />";
	echo "<h1>eatsure - London Food Inspection Scores</h1>";
	echo "<p>$string</p>";
	echo "</body></html>";
	exit(0);
} else {
	if(isset($_GET['inspect'])) {
		$append = "?inspect={$_GET['inspect']}";
	} else {
		$append = "";
	}
	header("location:http://eatsure.ca/index.php$append");
	exit(0);
}

?>