<?php
/*
 * This file contains functions used by the AJAX handler (ajax.php)
 */

include('settings.php');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

function test(){
	return "<p>Testing...</p>";	
}

/* Gets a Bitly short URL for the eatsure login & app key */
function GetBitlyURL($restaurant_id)
{
	$url = 'http://eatsure.ca/index.php?inspect='.$restaurant_id;
	$login = 'eatsure';
	$appkey = 'R_3eebbcdee51a10ed239b39b41272ba62';
	$format = 'txt';
	
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$connectURL);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

/* Gets all of the Inspection Details for a Restaurant */
function GetInspectionDetails($restaurant_id)
{	
	// Open the JSON string
	$json = "{\"inspectiondetails\":[";
	
	/* Aug.9th - 2010 - rtraction djm - Includes inspections count */	
	$query = "SELECT 	i.restaurant_id, i.inspection_id, i.severity, i.inspection_type,
						i.description, i.category, i.inspected, r.location, r.address, r.city,
						'' as inspections, '' as closed, '' as infraction, '' as latitude, '' as longitude, 
						'' as updated
						FROM inspection AS i 
						LEFT JOIN restaurant AS r ON i.restaurant_id = r.restaurant_id
						WHERE i.active = 1 /* remove to get history */ 
							AND r.restaurant_id = ".intval($restaurant_id)."
			  UNION
			  SELECT	r.restaurant_id, 0, '', '', '', '', r.inspected, r.location, r.address, r.city,
        				CASE UNIX_TIMESTAMP(r.inspected) WHEN 0 THEN 0 ELSE COUNT(a.restaurant_id) END as inspections,
        				r.closed, (r.critical + r.noncritical) as infraction, r.latitude, r.longitude, r.updated
			  FROM
			  (
			  	SELECT res.restaurant_id, i.inspected
        		FROM restaurant res
        		LEFT JOIN inspection i on res.restaurant_id = i.restaurant_id
				WHERE res.restaurant_id = ".intval($restaurant_id)."
        		GROUP BY res.restaurant_id, i.inspected
			  ) AS a
			  RIGHT JOIN restaurant r on a.restaurant_id = r.restaurant_id
			  WHERE r.active = 1 and r.restaurant_id = ".intval($restaurant_id)."
/* required for live */   GROUP BY r.restaurant_id, r.location, r.address, r.city, r.closed, infraction, r.latitude, r.longitude, r.updated		  
			  ORDER BY severity, inspected desc";
	
	$result = queryResult($query);
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			$json .= InspectionRecordToJson($row);
			$json .= ",";	
		}
		
		$json = substr($json, 0, strlen($json)-1);	// remove the trailing comma
	}
	
	// Close the JSON string
	$json .= "]}";
	
	return $json;
}

/* Creates a JSON string for an Inspection Record*/
function InspectionRecordToJson($record)
{
	$format = "{\"restaurant_id\":%d,\"inspection_id\":%d,\"severity\":\"%s\",\"inspection_type\":\"%s\",\"description\":\"%s\", \"category\":\"%s\",\"inspected\":\"%s\",\"location\":\"%s\", \"address\":\"%s\",\"city\":\"%s\",\"inspections\":\"%s\",\"closed\":\"%s\",\"infraction\":\"%s\",\"latitude\":\"%s\",\"longitude\":\"%s\",\"updated\":\"%s\"}";
	return sprintf($format, $record["restaurant_id"], $record["inspection_id"],
		$record["severity"], $record["inspection_type"],$record["description"],
		$record["category"], $record["inspected"], $record["location"],
		$record["address"], $record["city"], $record["inspections"], $record["closed"], 
		$record["infraction"], $record["latitude"], $record["longitude"],
		$record["updated"]);
}

function GetLocationsByInspectionResult($inspResult, $latitude = 0.000, $longitude = 0.000, $radius = 0.000)
{	
	// Open the JSON string
	$json = "{\"locations\":[";
	
	$query = "";
	if ($inspResult == "closed")
	{
		$query = "SELECT	r.restaurant_id, r.location as title, r.address, r.city,
       						UNIX_TIMESTAMP(r.inspected) as inspected, r.closed, 					
							(r.critical + r.noncritical) as infraction, 
							r.latitude, r.longitude, r.updated,
       						CASE UNIX_TIMESTAMP(r.inspected) WHEN 0 THEN 0 ELSE COUNT(a.restaurant_id) END as inspections
		  		  FROM
		  		  (
		  			SELECT res.restaurant_id, i.inspected
        			FROM restaurant res
        			LEFT JOIN inspection i on res.restaurant_id = i.restaurant_id
        			WHERE res.closed > 0 AND res.inspected != '0000-00-00'
        			GROUP BY res.restaurant_id, i.inspected
		  		  ) AS a
		  		  RIGHT JOIN restaurant r on a.restaurant_id = r.restaurant_id
		  		  WHERE r.active = 1";
	}
	else
	{
		$query = "SELECT 	r.restaurant_id, r.location as title, r.address, r.city,
							UNIX_TIMESTAMP(r.inspected) As inspected, r.closed, 
							(r.critical + r.noncritical) as infraction,
							r.latitude, r.longitude, r.updated, -1 as inspections
				  FROM		restaurant AS r 
				  WHERE 	r.active = 1";
	}
	
	if($inspResult == "passed")
	{
		$query .= " AND r.noncritical = 0 AND r.critical = 0";
		$query .= " AND r.inspected != '0000-00-00'";
	}
	else if($inspResult == "infraction")
	{
		$query .= " AND r.noncritical > 0 OR r.critical > 0";
		$query .= " AND r.inspected != '0000-00-00'";
	}
	else if($inspResult == "closed")
	{
		$query .= " AND r.closed > 0";
		$query .= " AND r.inspected != '0000-00-00'";
	}
	else if($inspResult == "notinspected")
	{
		$query .= " AND r.inspected = '0000-00-00'";
	}
	else if($inspResult == "allinspected")
	{
		$query .= " AND r.inspected != '0000-00-00'";
	}
	
	if($latitude != 0 && $longitude != 0 && $radius != 0)
	{
		// The radius of the earth is 6371Km and 3959miles
		$query .= " AND (6371 * acos(cos(radians(".$latitude.")) * cos(radians(latitude)) * cos(radians(longitude) - radians(".$longitude.")) + sin(radians(".$latitude.")) * sin(radians(latitude)))) < ".$radius;
	}
	
	$query .= " ORDER BY r.inspected DESC";
		
	$result = queryResult($query);
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{
			$json .= LocationRecordToJson($row, $inspResult);
			$json .= ",";
		}
		
		$json = substr($json, 0, strlen($json)-1);	// remove the trailing comma
	}
	
	// Close the JSON string
	$json .= "]}";
	
	return $json;
}

function LocationRecordToJson($record, $inspResult)
{
	/* JSON Format */
	/* { "id": "518",
					"name": "Tamarack Ridge Golf Club",
					"icon": ["green-dot", "pin-shadow"],
					"status": ["passed"],
					"closed": ["0"],
					"infraction": ["0"],
					"inspected": ["May 18th 2010"],
					"address": ["3950 Cromarty Rd, Putnam"],
					"inspections": ["0"],
					"posn": [42.967940, -80.957980] }
	*/
	if($record["address"] == "")
	{
		return "";
	}
	else
	{
		$icon = "";
		if($inspResult == "passed")
		{
			/* passed */
			$icon = "\"green-dot\", \"pin-shadow\"";
			$status = "passed";
		}
		else if($inspResult == "infraction" || $inspResult == "closed")
		{
			if($record["infraction"] > 0)
			{
				$icon = "\"yellow-dot\", \"pin-shadow\"";
				$status = "infraction";
			}
			if($record["closed"] > 0)
			{
				$icon = "\"red-dot\", \"pin-shadow\"";
				$status = "closed";
			}
		}
		else if($inspResult == "notinspected")
		{
			$icon = "\"dkgrey-dot\", \"pin-shadow\"";
			$status = "uninspected";
		}
		
		$inspection_date = "Not Available";
		if ($record['inspected'] != 0)
		{
			$inspection_date = date('M jS Y', $record['inspected']);
		}
	
		// Clean up the data...
		if(strlen($record['address']) < 2)$record['address']="";
		if(strlen($record['city']) < 2)$record['city']="";
		
		$recordCity = $record['address'] . ', ' . $record['city'];
		$position = 'new google.maps.LatLng(' . $record['latitude'] . ', ' . $record['longitude'] . ')';
		
		$format = "{\"id\":\"%d\",\"name\":\"%s\",\"icon\":[%s],\"status\":[\"%s\"],\"closed\":[\"%d\"],\"infraction\":[\"%d\"],\"inspected\":[\"%s\"],\"address\":[\"%s\"],\"inspections\":[\"%s\"],\"posn\":[%f,%f]}";
		return sprintf($format, $record["restaurant_id"], $record["title"], $icon, $status,
			$record["closed"], $record["infraction"], $inspection_date, $recordCity, $record["inspections"],
			$record["latitude"], $record["longitude"]);
	}
}
?>