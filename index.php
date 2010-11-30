<?php
/*
 * Eatsure
 * http://www.eatsure.ca
 * 
 * Author: rtraction
 * http://www.rtraction.com
 * 
 * Released under GPLv3
 * November 30, 2010
 */

/*
 * include database credentials
 */
include('settings.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w2.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
        <title>eatsure - London Food Inspection Scores</title>
		<link rel="image_src" href="images/fbimage.png" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.custom.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
		<script src="mapconfig.js" type="text/javascript"></script>
	    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
		<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markermanager/1.0/src/markermanager_packed.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.1/mootools.js" type="text/javascript"></script>
        <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.8.custom.min.js" type="text/javascript"></script>
        <script src="js/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <!--<script src="js/FastMarkerOverlay.js" type="text/javascript"></script>-->
        <script type="text/javascript">

	        $(document).ready(function() {
		        agree=getCookie('lmhuinspect');
		        if(agree!='iagree'){
	        		opendialog();
		        }
		        preloadImages();
		        
		        /* the aoColumns can be null when standard sorting is used. Only the custom sorting
		         columns need a value */
		    	$('#searchtable').dataTable({
		    		"bPaginate": true,
					"bLengthChange": true,
		    		"bJQueryUI": true,
		    		"aoColumns": [
									null,
									null,
									null,
									null,
									{ "sType": "title-numeric" }
								]
		    	});
			
				<?php 
				if (isset($_GET['inspect'])){
					$inspect = intval($_GET['inspect']);
					echo 'openinspection('. $inspect . ');';
				}
				?>
	        });
	        
	        /* Custom sorting function used for empty span with title value */
	        jQuery.fn.dataTableExt.oSort['title-numeric-asc']  = function(a,b) {
				var x = a.match(/title="*(-?[0-9]+)/)[1];
				var y = b.match(/title="*(-?[0-9]+)/)[1];
				x = parseFloat( x );
				y = parseFloat( y );
				return ((x < y) ? -1 : ((x > y) ?  1 : 0));
			};
			
			/* Custom sorting function used for empty span with title value */
			jQuery.fn.dataTableExt.oSort['title-numeric-desc'] = function(a,b) {
				var x = a.match(/title="*(-?[0-9]+)/)[1];
				var y = b.match(/title="*(-?[0-9]+)/)[1];
				x = parseFloat( x );
				y = parseFloat( y );
				return ((x < y) ?  1 : ((x > y) ? -1 : 0));
			};
        </script>
		<?php 
			$locations = getLocations();
			$inspections = getInspectionTotals();
			
			echo googlemaps_load($locations);
		?>
    </head>
    <body onload="load()">
<!--[if lt IE 8]>  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>      </div>      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>    </div>  </div>  <![endif]-->
<div id="wrapper">
	<div id="header">
		<div id="header_left">
			<div id="logo">
				<a href="/"><span>eatsure beta - London Food Inspection Scores</span></a>
			</div>
		</div>
		<div id="results">
			<?php 
			echo displayInspectionTotals($inspections);
			?>
		</div>
		<div id="subheader">
			<div id="subtext">
				<a href="http://inspection.healthunit.com/" target="_blank" class="source" title="Data Sourced from London Middlesex County Health Unit">Data Sourced from London Middlesex County Health Unit</a>
				<img src="images/subhead-dash.gif" align="top" alt="" />
				<a href="#" onclick="opendialog();return false;" title="Disclaimer" class="disclaimer">Disclaimer</a>
			</div>
			<div id="subsearch">
				<input type="submit" id="search" class="ui-state-default ui-corner-all" name="search" value="Search" onclick="opensearch();return false;"  />
			</div>
		</div>
	</div>
	<div id="content">
        <div id="map" style="width: 100%; height: 100%; position: absolute;"></div>
        <div id="status"></div>

		</div>
	</div>
	<div id="disclaimer" class="hidden">
		<h2>This site is neither affiliated with nor endorsed by the Middlesex-London Health Unit</h2>
		<p><strong>Disclaimer from the Middlesex-London Health Unit Food Premises Inspection Disclosure Site:</strong>
		This website is designed to provide the public with information regarding inspections of food premises located in the City of London and County of Middlesex. Please be advised that the results of all inspections posted on this website describe what the Public Health Inspector observed at the time of the inspection. This website is not intended to guarantee the conditions of a food premises at all times and should not be relied upon for that purpose. Although every effort is made to ensure that the information on this site is updated weekly, the Middlesex-London Health Unit cannot guarantee that all information is accurate, complete or current at all times and makes no warranty or representation, expressed or implied, concerning the accuracy, completeness or currency of the information contained on this website. The Middlesex-London Health Unit assumes no liability for any inaccurate, delayed or incomplete information, nor for any actions taken in reliance of information on this website.  No endorsement of any food premises, or the products or services offered by a food premises, is expressed or implied by any information, material or content included on this website.</p>
		<p>If you have any questions regarding an inspection report or food premises, please contact us by phone at (519) 663-5317 ext. 2300 or e-mail us at <a href="mailto:foodinspections@mlhu.on.ca">foodinspections@mlhu.on.ca</a>. Our office is open Monday to Friday between 8:30 a.m. and 4:30 p.m.</p>
	</div>
	<div id="updated">
		<div>
			<strong>Last updated:</strong>
			<?php 
			$updated = getLatestUpdate();
			echo $updated;
			?>
			<br />
			website by <a href="http://www.rtraction.com/" target="_blank" title="rtraction: Web Design in London Ontario">rtraction</a>
		</div>
	</div>

	<div id="loading">
		<p>Loading restaurant inspection data...</p>
		<img src="images/bigrotation2.gif" alt="loading" />
	</div>

	<div id="loading_search">
		<p>Loading search data...</p>
		<img src="images/bigrotation2.gif" alt="loading" />
	</div>
	
	<div id="inspectionDialog">
	</div>
	
	<div id="closedDialog">
	</div>

	<?php 
		//load inspection details and search results
		//echo getAllInspectionDetails();
		echo getSearch();	
	?>


	<script type="text/javascript">
	var uservoiceOptions = {
	  /* required */
	  key: 'eatsure',
	  host: 'eatsure.uservoice.com', 
	  forum: '54156',
	  showTab: true,  
	  /* optional */
	  background_color:'#35552b', 
  	  text_color: 'white',
  	  hover_color: '#31c600',
  	  alignment: 'right',
	  lang: 'en'
	};
	
	function _loadUserVoice() {
	  var s = document.createElement('script');
	  s.setAttribute('type', 'text/javascript');
	  s.setAttribute('src', ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js");
	  document.getElementsByTagName('head')[0].appendChild(s);
	}
	_loadSuper = window.onload;
	window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };

	</script>	
		
	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-16030001-1");
	pageTracker._trackPageview();
	} catch(err) {}</script>

	<script type="text/javascript">
	   __compete_code = '4c5475d6581322b01a4837de8294f464';
	   (function () {
	       var s = document.createElement('script'),
	           d = document.getElementsByTagName('head')[0] ||
	               document.getElementsByTagName('body')[0],
	           t = 'https:' == document.location.protocol ?
	               'https://c.compete.com/bootstrap/' :
	               'http://c.compete.com/bootstrap/';
	       s.src = t + __compete_code + '/bootstrap.js';
	       s.type = 'text/javascript';
	       s.async = 'async';
	       if (d) { d.appendChild(s); }
	   }());
	</script>	

</body>

</html>
<?php 

/*
 * getLocations()
 * retrieve all locations with no infractions
 */

function getLocations() 
{
	/*  */
	$query = "SELECT	r.restaurant_id, r.location as title, r.address, r.city,
						UNIX_TIMESTAMP(r.inspected) As inspected, r.closed, 
						(r.critical + r.noncritical) as infraction,
						r.latitude, r.longitude, r.updated, -1 as inspections
			  FROM 		restaurant AS r 
			  WHERE		r.active = 1
						AND r.critical = 0 AND r.noncritical = 0
						AND r.inspected != '0000-00-00'
			  ORDER BY inspected DESC";
		
	$result = queryResult($query);

	$locations = array();
	
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			$locations[] = $row;
		}
	}
	
	return $locations;
}

/*
 * getSearch
 * create lightbox popups for inspection details 
 */
function getSearch()
{
	$query = "SELECT DISTINCT r.restaurant_id, r.location, r.address, r.city, r.inspected, r.closed, (r.critical + r.noncritical) as infraction,
		CASE
			WHEN (r.inspected = '0000-00-00') THEN 1
			ELSE (r.noncritical * 10) + (r.critical * 10) + (r.closed * 100)
		END as statusVal
		FROM restaurant AS r
		WHERE r.active = 1 ORDER BY location, address";
		
	$page_content = '';
	$restaurant_id = 0;
	$result = queryResult($query);
	if (mysql_num_rows($result) > 0)
	{
		$page_content .= '<div id="searchouter">';
		$page_content .= '<table id="searchtable">';
		$page_content .= '<thead><tr><th>Restaurant</th><th>Address</th><th>City</th><th>Inspected</th><th>Status</th></tr></thead><tbody>';
		
		while($row = mysql_fetch_assoc($result))
		{	
			$inspection_details = "<span title=\"".$row['statusVal']."\"></span>";
			
			if ($row['inspected'] != 0){
				$inspection_date = date('M jS Y', strtotime($row['inspected']));
			} else {
				$inspection_date = "Not Available";
			}
			
			if ($row['closed'] > 0 || $row['infraction'] > 0)
			{
				$inspection_details .= "<div class=\"status_label\"><a href=\"#\" onclick=\"openinspection(".$row['restaurant_id'].");return false;\">Details</a></div>";
				if ($row['closed'] > 0)
				{
					$inspection_details .= "<div class=\"status_icon\"><img class=\"status_img\" src=\"images/legend_closed.gif\" alt=\"Closed\" />" . $row['closed'] . "</div>"; 
				}
				if ($row['infraction'] > 0)
				{
					$inspection_details .= "<div class=\"status_icon\"><img class=\"status_img\" src=\"images/legend_infraction.gif\" alt=\"Infraction\" />" . $row['infraction'] . "</div>"; 
				}
			} else {
				if ($row['inspected'] != 0)
				{
					$inspection_details .= "<div class=\"status_label\"><a href=\"#\" onclick=\"openinspection(".$row['restaurant_id'].");return false;\">Passed</a></div><div class=\"status_icon\"><img class=\"status_img\" src=\"images/legend_passed.gif\" alt=\"Passed\" /></div>";
				} else {
					$inspection_details .= "<div class=\"status_label\"><a href=\"#\" onclick=\"openinspection(".$row['restaurant_id'].");return false;\">No Data</a></div><div class=\"status_icon\"><img class=\"status_img\" src=\"images/legend_nodata.gif\" alt=\"Not Inspected\" /></div>";
				}
			}

			$page_content .= '<tr id="search_details_' . $row['restaurant_id'] . '" valign="top">';
			$page_content .= '<td class="location">' . $row['location'] . '</td>';
			$page_content .= '<td class="address">' . $row['address'] . '</td>';
			$page_content .= '<td class="city">' . $row['city'] . '</td>';
			$page_content .= '<td class="inspected">' . $inspection_date . '</td>';
			$page_content .= '<td class="details">' . $inspection_details . '</td>';
			$page_content .= '</tr>';
			
			$restaurant_id = $row['restaurant_id'];
		}
		$page_content .= '</tbody></table>';
		$page_content .= '<div id="legend"><ul><li class="infraction">Infractions</li><li class="closed">Closed</li></ul></div>';
		$page_content .= '</div>';
	}
	
	return $page_content;
}

/*
 * getInspectionTotals
 * get the totals for all types of inspections
 */
function getInspectionTotals()
{
	$query = "SELECT (SELECT COUNT(*) FROM restaurant WHERE closed > 0 AND active = 1) AS closed, 
		(SELECT COUNT(*) FROM restaurant WHERE critical > 0 OR noncritical > 0 AND active = 1) AS infraction, 
		(SELECT COUNT(*) FROM restaurant WHERE critical = 0 AND noncritical = 0 AND inspected > 0 AND active = 1) AS passed,
		(SELECT COUNT(*) FROM restaurant WHERE inspected > 0 AND active = 1) AS total,
		(SELECT COUNT(*) FROM restaurant WHERE inspected = 0 AND active = 1) AS uninspected";
	$result = queryResult($query);

	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			$inspections[] = $row;
		}
	}
	
	return $inspections;
}

/*
 * getLatestUpdate
 * returns date of last cron update, indicating the last time the MLHU site was parsed
 */
function getLatestUpdate()
{
	$query = "SELECT `update` FROM updated ORDER BY `update` DESC LIMIT 1";
	$result = queryResult($query);

	$updateDate = null;
	
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_row($result))
		{
			$updated[] = $row;
		}
	}
	
	$date = strtotime($updated[0][0]);
	$updateDate = date('m-d-Y', $date);
	
	return $updateDate;
}

/*
 * displayInspectionTotals
 * formats inspection totals as html
 */
function displayInspectionTotals($inspections) {
	$page_content = '<div id="totals"><ul>';
	$page_content .= '<li class="passed">';
	$page_content .= '<a href="" onclick="showPassed();return false;" class="active" >' . $inspections[0]['passed'] . '</a>';
	$page_content .= '</li>';
	$page_content .= '<li class="infraction">';
	$page_content .= '<a href="" onclick="showInfraction();return false;" >' . $inspections[0]['infraction'] . '</a>';
	$page_content .= '</li>'; 
	$page_content .= '<li class="closed">';
	$page_content .= '<a href="" onclick="showClosedDialog();showClosed();return false;" >' . $inspections[0]['closed'] . '</a>';
	$page_content .= '</li>'; 
	$page_content .= '<li class="allinspected">';
	$page_content .= '<a href="" onclick="showAllInspected();return false;" >' . $inspections[0]['total'] . '</a>';
	$page_content .= '</li>'; 
	$page_content .= '<li class="notinspected">';
	$page_content .= '<a href="" onclick="showNotInspected();return false;" >' . $inspections[0]['uninspected'] . '</a>';
	$page_content .= '</li>';
	$page_content .= '</ul></div>'; 
	
	return $page_content;
}

/*
 * googlemaps_load
 * create javascript variable from address array
 */
function googlemaps_load($addressArray) {
	$page_content = '';
	
	$page_content .= '
  <script type="text/javascript">
		var restaurantLayer = [
			{
				"zoom": [0, 19],
				"places": [';
	
	$i = 0;
	
	foreach($addressArray as $address)
	{
		if ($address['address'] != '') 
		{
			if ($address['inspected'] != 0)
			{
				$inspection_date = date('M jS Y', $address['inspected']);
			} else {
				$inspection_date = "Not Available";
			}

			if ($address['latitude'] != '' && $address['longitude'] != '' && $address['address'] != '')
			{
				if ($address['closed']>0)
				{
					$icon = 'red-dot';
					$status = "closed";
				} elseif ($address['infraction'] > 0) {
					$icon = 'yellow-dot';
					$status = "infraction";
				} elseif ($address['inspected'] > 0) {
					$icon = 'green-dot';
					$status = "passed";
				} else {
					$icon = 'ltgrey-dot';
					$status = "uninspected";
				}
				$position = 'new google.maps.LatLng(' . $address['latitude'] . ', ' . $address['longitude'] . ')';

				$addressCity = $address['address'] . ', ' . $address['city'];
				if ($address['inspected'] != 0)
				{
					$inspection_date = date('M jS Y', $address['inspected']);
				} else {
					$inspection_date = "Never";
				}
				
				if ($i > 0)
				{
					$page_content .= ',';
				}
				
				$page_content .= '{ "id": "' . $address['restaurant_id'] . '",
					"name": "' . $address['title'] . '",
					"icon": ["' . $icon . '", "pin-shadow"],
					"status": ["' . $status . '"],
					"closed": ["' . $address['closed'] . '"],
					"infraction": ["' . $address['infraction'] . '"],
					"inspected": ["' . $inspection_date . '"],
					"address": ["' . $addressCity . '"],
					"inspections": ["' . $address['inspections'] . '"],
					"posn": [' . $address['latitude'] . ', ' . $address['longitude'] . '] } ';
			}
			$i++;
		}
	}
	
	$page_content .= '] } ];';
	$page_content .= '</script>';

	return $page_content;
}

?>