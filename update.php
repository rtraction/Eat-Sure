<?php

/*
 * include database credentials
 */
include('settings.php');

/*
 * the simplehtmldom library is used because many server configurations don't have the DOM XML installed
 * if your server supports DOM XML, you may wish to use that parsing functionality
 */
include('simplehtmldom/simple_html_dom.php');

/*
 * the MLHU website has several pages that need to be navigated through
 * - disclaimer, must click 'I Agree'
 * - search form, initially shows no results
 * - postback on each restaurant
 * - each inspection
 */

global $cookie1, $cookie2, $cookie3, $cookie4, $cookie5;

$cookie1 = 'cookie1.txt';
$cookie2 = 'cookie2.txt';
$cookie3 = 'cookie3.txt';
$cookie4 = 'cookie4.txt';
$cookie5 = 'cookie5.txt';

echo 'Beginning data update<br />';
parseData(0);
updateGeocode();

echo 'Data update complete<br />';

# This function just simply gets connected and gets the location lists for all resturants
function getBaseData(&$info, &$result)
{
	global $cookie1, $cookie2, $cookie3, $cookie4, $cookie5;

	# Get the disclaimer page
	$ch = curl_init("http://inspection.healthunit.com/inspection-home.aspx");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirections
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie1);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

	# Post back with the disclaimer
	$url = $info['url']; // write here the url of your form
	$ch = curl_init(); //  Initiating the Curl Handler
	curl_setopt($ch, CURLOPT_URL,$url); // Url a donde se va a postear.
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); //I set the user Agent. In this case is Firefox 2 browser
	curl_setopt($ch, CURLOPT_FAILONERROR, 1); //finish in case of error
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirections
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Return the result page in a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout.
	curl_setopt($ch, CURLOPT_POST, 1); // I set the POST Method
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "__VIEWSTATE=%2FwEPDwUKLTUwMTQ1MTg3OA9kFgJmD2QWBAIBD2QWAgIBDxYCHgRocmVmBSgvc2l0ZXMvRm9vZEluc3BlY3Rpb24vY3NzL1N0eWxlU2hlZXQuY3NzZAIDD2QWCAIBD2QWAmYPDxYCHgRUZXh0ZWRkAgUPZBYCZg8PFgIfAWVkZAIND2QWAmYPDxYCHwEF8QEmY29weTsgMjAxMCA8YSB0YXJnZXQ9Il9ibGFuayIgIGhyZWY9Imh0dHA6Ly93d3cuaGVhbHRodW5pdC5jb20iPk1pZGRsZXNleC1Mb25kb24gSGVhbHRoIFVuaXQ8L2E%2BIC0gPGEgaHJlZj0iL2FydGljbGUuYXNweD9pZD0xMDAyNyI%2BRGlzY2xhaW1lcjwvYT4gLSBRdWVzdGlvbnM%2FIEVtYWlsIDxhIGhyZWY9Im1haWx0bzpmb29kaW5zcGVjdGlvbnNAbWxodS5vbi5jYSI%2BZm9vZGluc3BlY3Rpb25zQG1saHUub24uY2E8L2E%2BZGQCDw8WAh8BBZUDPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPiB2YXIgZ2FKc0hvc3QgPSAoKCJodHRwczoiID09IGRvY3VtZW50LmxvY2F0aW9uLnByb3RvY29sKSA%2FICJodHRwczovL3NzbC4iIDogImh0dHA6Ly93d3cuIik7IGRvY3VtZW50LndyaXRlKHVuZXNjYXBlKCIlM0NzY3JpcHQgc3JjPSciICsgZ2FKc0hvc3QgKyAiZ29vZ2xlLWFuYWx5dGljcy5jb20vZ2EuanMnIHR5cGU9J3RleHQvamF2YXNjcmlwdCclM0UlM0Mvc2NyaXB0JTNFIikpOyA8L3NjcmlwdD4gPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPiB0cnkgeyB2YXIgcGFnZVRyYWNrZXIgPSBfZ2F0Ll9nZXRUcmFja2VyKCJVQS02NDgzMTI3LTIiKTsgcGFnZVRyYWNrZXIuX3RyYWNrUGFnZXZpZXcoKTsgfSBjYXRjaChlcnIpIHt9PC9zY3JpcHQ%2BZGQR%2Fc8AWrbpK8DcBldbdx5bzph%2FwQ%3D%3D&__EVENTVALIDATION=%2FwEWAgKMrZpFApDShtcMWA0kumFHygHfXKxqf0D1w880%2F1M%3D&ctl00%24ContentPlaceHolder1%24ctl04=I+Agree"); //change it with your own field name and value
	$result = curl_exec($ch); // Execute and send the data.
	$info = curl_getinfo($ch);
	curl_close($ch);

	# Post back a blank search to get the company list
	$url = $info['url']; // write here the url of your form
	$ch = curl_init(); //  Initiating the Curl Handler
	curl_setopt($ch, CURLOPT_URL,$url); // Url a donde se va a postear.
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); //I set the user Agent. In this case is Firefox 2 browser
	curl_setopt($ch, CURLOPT_FAILONERROR, 1); //finish in case of error
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirections
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Return the result page in a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout.
	curl_setopt($ch, CURLOPT_POST, 1); // I set the POST Method
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie3);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "__VIEWSTATE=%2FwEPDwULLTE0OTMwMDIyNzEPZBYCZg9kFgQCAQ9kFgICAQ8WAh4EaHJlZgUoL3NpdGVzL0Zvb2RJbnNwZWN0aW9uL2Nzcy9TdHlsZVNoZWV0LmNzc2QCAw9kFgoCAQ9kFgJmDw8WAh4EVGV4dGVkZAIFD2QWAmYPDxYCHwFlZGQCCw9kFgQCAQ8QZBAVHwtBbGwgUmVnaW9ucwtBaWxzYSBDcmFpZwVBcHBpbgRBcnZhBEF2b24HQmVsbW9udAlCcnlhbnN0b24JQ2VudHJhbGlhCERlbGF3YXJlCERlbmZpZWxkCkRvcmNoZXN0ZXIHR2xlbmNvZQdHcmFudG9uDUhhcnJpZXRzdmlsbGUISWxkZXJ0b24HS2Vyd29vZAhLaWx3b3J0aAZLb21va2EHTGFtYmV0aAZMb25kb24FTHVjYW4JTWVsYm91cm5lB01vc3NsZXkNTW91bnQgQnJ5ZGdlcwdOZXdidXJ5CU5pbGVzdG93bghQYXJraGlsbAZQdXRuYW0JU3RyYXRocm95CVRob3JuZGFsZQpXYXJkc3ZpbGxlFR8BMAtBaWxzYSBDcmFpZwVBcHBpbgRBcnZhBEF2b24HQmVsbW9udAlCcnlhbnN0b24JQ2VudHJhbGlhCERlbGF3YXJlCERlbmZpZWxkCkRvcmNoZXN0ZXIHR2xlbmNvZQdHcmFudG9uDUhhcnJpZXRzdmlsbGUISWxkZXJ0b24HS2Vyd29vZAhLaWx3b3J0aAZLb21va2EHTGFtYmV0aAZMb25kb24FTHVjYW4JTWVsYm91cm5lB01vc3NsZXkNTW91bnQgQnJ5ZGdlcwdOZXdidXJ5CU5pbGVzdG93bghQYXJraGlsbAZQdXRuYW0JU3RyYXRocm95CVRob3JuZGFsZQpXYXJkc3ZpbGxlFCsDH2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dkZAIDDxBkEBUbAAFBAUIBQwFEAUUBRgFHAUgBSQFKAUsBTAFNAU4BTwFQAVEBUgFTAVQBVQFWAVcBWAFZAVoVGwEwAUEBQgFDAUQBRQFGAUcBSAFJAUoBSwFMAU0BTgFPAVABUQFSAVMBVAFVAVYBVwFYAVkBWhQrAxtnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dkZAIND2QWAmYPDxYCHwEF8QEmY29weTsgMjAxMCA8YSB0YXJnZXQ9Il9ibGFuayIgIGhyZWY9Imh0dHA6Ly93d3cuaGVhbHRodW5pdC5jb20iPk1pZGRsZXNleC1Mb25kb24gSGVhbHRoIFVuaXQ8L2E%2BIC0gPGEgaHJlZj0iL2FydGljbGUuYXNweD9pZD0xMDAyNyI%2BRGlzY2xhaW1lcjwvYT4gLSBRdWVzdGlvbnM%2FIEVtYWlsIDxhIGhyZWY9Im1haWx0bzpmb29kaW5zcGVjdGlvbnNAbWxodS5vbi5jYSI%2BZm9vZGluc3BlY3Rpb25zQG1saHUub24uY2E8L2E%2BZGQCDw8WAh8BBZUDPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPiB2YXIgZ2FKc0hvc3QgPSAoKCJodHRwczoiID09IGRvY3VtZW50LmxvY2F0aW9uLnByb3RvY29sKSA%2FICJodHRwczovL3NzbC4iIDogImh0dHA6Ly93d3cuIik7IGRvY3VtZW50LndyaXRlKHVuZXNjYXBlKCIlM0NzY3JpcHQgc3JjPSciICsgZ2FKc0hvc3QgKyAiZ29vZ2xlLWFuYWx5dGljcy5jb20vZ2EuanMnIHR5cGU9J3RleHQvamF2YXNjcmlwdCclM0UlM0Mvc2NyaXB0JTNFIikpOyA8L3NjcmlwdD4gPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPiB0cnkgeyB2YXIgcGFnZVRyYWNrZXIgPSBfZ2F0Ll9nZXRUcmFja2VyKCJVQS02NDgzMTI3LTIiKTsgcGFnZVRyYWNrZXIuX3RyYWNrUGFnZXZpZXcoKTsgfSBjYXRjaChlcnIpIHt9PC9zY3JpcHQ%2BZGTDQoRr0%2BMdlBQA4nhYAvvjC4LRPA%3D%3D&__EVENTVALIDATION=%2FwEWPQKdho%2BMCQLhi9H2BALv8%2BSiDAK0v4S%2FCAKqvNTuAwK%2F75PeCQLs77W6AQL84N7jDwKCzenxBAL8uqIrAp77ppYDAuKfwJIEAte7qP4KAs2t8dgFAtO12dgCApXQh4YDAon%2F7K4LAufpncAPAsrXsJAIAt3agMkCAor4qs0GAvLP%2BcUDAunQkKEJApq0vqQLAvOd9LYIApSfm44OAsz0v04CmYzm4gICmueQqAUCg%2BzIzA8Cnfyk2gYCib2b9gcCiNKK5wYCx9KK5wYCxtKK5wYCxdKK5wYCxNKK5wYCw9KK5wYCwtKK5wYCwdKK5wYC8NKK5wYC%2F9KK5wYC%2FtKK5wYC%2FdKK5wYC%2FNKK5wYC%2B9KK5wYC%2BtKK5wYC%2BdKK5wYC6NKK5wYC99KK5wYC9tKK5wYC9dKK5wYC9NKK5wYC89KK5wYC8tKK5wYC8dKK5wYC4NKK5wYC79KK5wYC7tKK5wYClvfhkgkC97rzhgKyiZ%2BEJBoEpZ%2BKEGqLJFQTVBtP2Q%3D%3D&ctl00%24ContentPlaceHolder1%24ddlRegion=0&ctl00%24ContentPlaceHolder1%24ddlAlphabet=0&ctl00%24ContentPlaceHolder1%24txtKeywords=&ctl00%24ContentPlaceHolder1%24btnSearch=Search"); //change it with your own field name and value
	$result = curl_exec($ch); // Execute and send the data.
	$info = curl_getinfo($ch);
	curl_close($ch);
}

function parseData($counterPosition)
{
	global $cookie1, $cookie2, $cookie3, $cookie4, $cookie5;
	
	# Store a timestamp of our start point
	$startTime = mktime();

	# Variable used for post backs
	$dropdowndsAndSearchText = '&ctl00%24ContentPlaceHolder1%24ddlRegion=0&ctl00%24ContentPlaceHolder1%24ddlAlphabet=0&ctl00%24ContentPlaceHolder1%24txtKeywords=';
	
	# Get our first set of data
	$info = '';
	$result = '';
	getBaseData($info, $result);
	
	# We need this for our next two post backs
	$url = $info['url']; // write here the url of your form

	# Our base HTML from the healthunit site
	$html = str_get_html($result);

	# Require for our Next PostBack(s)
	$viewstate = $html->find('#__VIEWSTATE');
	$viewstate = $viewstate[0]->attr['value'];
	$validation = $html->find('#__EVENTVALIDATION');
	$validation = $validation[0]->attr['value'];

	# Our looping variable & break variable
	$break = false;
	$i = 0;
	
	foreach($html->find('#ctl00_ContentPlaceHolder1_tblSearchResults') as $el)
	{
		# Our primary loop
		foreach($el->find('tr') as $row)
		{
			# Counter Position Check
			if ($i < $counterPosition)
			{
				$i++;
				continue;
			}
			
			# TimeCheck
			# If were over 25minutes - then we break out and restart
			if ($startTime + 60 * 25 < mktime())
			{
				$break = true;
				break;
			}
			
			echo 'in our process';
			
			# Increment our counter
			$i++;
			
			# Start processing our records
			$location = strip_tags($row->childNodes(0)->innertext); //estID is parameter of a tag
			$location_link = $row->childNodes(0)->find('a');
			$location_id = $location_link[0]->attr['estid'];
			$location_linkid = $location_link[0]->attr['id'];
			$address = $row->childNodes(1)->innertext;
			$city = $row->childNodes(2)->innertext;
			$date = $row->childNodes(4)->innertext; //njs - 10-27-10 - column changes
			$critical = $row->childNodes(5)->innertext; //njs - 10-27-10 - column changes
			$noncritical = $row->childNodes(6)->innertext; //njs - 10-27-10 - column changes
				
			echo 'location ' . $location . '<br>';
			echo 'location link id'.$location_linkid.'<br />';
			echo 'location link ' . $location_link . '<br />';
			echo 'location id ' . $location_id . '<br>';
			echo 'address ' . $address . '<br>';
			echo 'city ' . $city . '<br>';
			echo 'date ' . $date . '<br>';
			echo 'critical ' . $critical . '<br>';
			echo 'noncritical ' . $noncritical . '<br>';
				
			if ($date != '')
			{
				$inspected = date('Y-m-d', strtotime($date));

				//njs - 10-28-10
				//reset closures if reinspections have occurred
				boolReInspection($location_id, $inspected);
			} else {
				$inspected = '0000-00-00';
			}
			
			$location_id = updateLocation($location, $address, $city, $inspected, $critical, $noncritical);
			//njs - 10-28-10
			//update inspection was returning false positives on infractions
			//changed to only check inspection date
			//$update_inspect = boolUpdateInspection($location_id, $inspected, $critical, $noncritical);
			$update_inspect = boolUpdateInspection($location_id, $inspected);
			
			if ($update_inspect)
			{
				try {
					# Get any of the inspection information
					$ch = curl_init(); //  Initiating the Curl Handler
					curl_setopt($ch, CURLOPT_URL,$url); // Url a donde se va a postear.
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); //I set the user Agent. In this case is Firefox 2 browser
					curl_setopt($ch, CURLOPT_FAILONERROR, 1); //finish in case of error
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirections
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Return the result page in a variable
					curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout.
					curl_setopt($ch, CURLOPT_POST, 1); // I set the POST Method
					curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie3);
					curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie4);
					$post = '__EVENTTARGET='.str_replace('_','%24',$location_linkid);
					$post.= '&__EVENTARGUMENT=&__VIEWSTATE='.urlencode($viewstate);
					$post.= '&__EVENTVALIDATION='.urlencode($validation);
					$post.= $dropdowndsAndSearchText;
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //change it with your own field name and value
					$result2 = curl_exec($ch); // Execute and send the data.
					$info = curl_getinfo($ch);
					curl_close($ch);
	
					# Parse the results & loop through each inspection date
					$html2 = str_get_html($result2);
					$viewstate2 = $html2->find('#__VIEWSTATE');
					$viewstate2 = $viewstate2[0]->attr['value'];
					$validation2 = $html2->find('#__EVENTVALIDATION');
					$validation2 = $validation2[0]->attr['value'];
					$divLocation2 = strpos($result2, '<div id="ctl00_ContentPlaceHolder1_pnlViolations"');
					$pos2 = substr($result2, $divLocation2, strpos($result2, '</div>', $divLocation2) - $divLocation2);
					$html2->clear();
					unset($html2);
					$htmldata2 = str_get_html('<html><body>'.$pos2.'</body></html>');
					$skip2 = false;
				} catch (Exception $e) {
					error_log('Error processing document ' . $e->getMessage());
					die('Error processing document ' . $e->getMessage());
				}
				
				echo '<br/>process rows '.date('h:i:s');
				
				foreach($htmldata2->find('tr') as $row2)
				{				
					# Skip the header row
					if (!$skip2)
					{
						$skip2 = true;
						continue;
					}
					
					# Grab our data
					$inspectionLink = $row2->childNodes(0)->find('a');
					if ($inspectionLink[0]->innertext != '')
					{
						$inspectionDate = date('Y-m-d', strtotime($inspectionLink[0]->innertext));
					}
					else
					{
						$inspectionDate = '0000-00-00';
					}
					$estid = $inspectionLink[0]->attr['estid'];
					$inspectionId = $inspectionLink[0]->attr['inspectionid'];
					$inspectionLinkId = $inspectionLink[0]->attr['id'];
					$inspectionType = $row2->childNodes(2)->innertext; // rtraction djm - Nov.2 2010 - blank row added
					$critical = $row2->childNodes(3)->innertext; // rtraction djm - Nov.2 2010 - blank row added
					$nonCritical = $row2->childNodes(4)->innertext; // rtraction djm - Nov.2 2010 - blank row added
					
					# Testing
					echo 'Inspecd:'.$inspectionDate.'<br />';
					echo 'ESTID:'.$estid.'<br />';
					echo 'InspecId:'.$inspectionId.'<br />';
					echo 'InspecT:'.$inspectionType.'<br />';
					echo 'Critical:'.$critical.'<br />';
					echo 'NonCritical:'.$nonCritical.'<br />';
					echo '<br />';
	
					# Grab the text
					$ch = curl_init(); //  Initiating the Curl Handler
					curl_setopt($ch, CURLOPT_URL,$url); // Url a donde se va a postear.
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); //I set the user Agent. In this case is Firefox 2 browser
					curl_setopt($ch, CURLOPT_FAILONERROR, 1); //finish in case of error
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirections
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Return the result page in a variable
					curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout.
					curl_setopt($ch, CURLOPT_POST, 1); // I set the POST Method
					curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie4);
					curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie5);
					$post = '__EVENTTARGET='.str_replace('_','%24',$inspectionLinkId);
					$post.= '&__EVENTARGUMENT=&__VIEWSTATE='.urlencode($viewstate2);
					$post.= '&__EVENTVALIDATION='.urlencode($validation2);
					$post.= $dropdowndsAndSearchText;
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //change it with your own field name and value
					$result3 = curl_exec($ch); // Execute and send the data.
					$info = curl_getinfo($ch);
					curl_close($ch);
	
					# Parse the results & loop through each item
					$divLocation3 = strpos($result3, '<div id="ctl00_ContentPlaceHolder1_pnlViolationDetails"');
					$pos3 = substr($result3, $divLocation3, strpos($result3, '</div>', $divLocation3) - $divLocation3);
					$htmldata3 = str_get_html('<html><body>'.$pos3.'</body></html>');
					$skip3 = false;
					foreach($htmldata3->find('tr') as $row3)
					{				
						# Skip any header rows	
						if ($row3->class == "inspectionTableHeader")
						{
							continue;
						}	
						
						# Grab our data for all normal rows
						if (isset($row3->childNodes(2)->innertext))
						{						
							//severity can be critical, noncritical, satisfactory
							$severity = strip_tags($row3->childNodes(0)->innertext);
							$desc = $row3->childNodes(1)->innertext;
							$resultText = $row3->childNodes(2)->innertext;
								
							# Testing
							echo 'Severity:'.$severity.'<br />';
							echo 'Desc:'.$desc.'<br />';
							echo 'Res:'.$resultText.'<br />';
							echo '<br />';
	
							$details = '';
							$category = '';
							
							if ($desc != '')
							{
								$desc = strip_tags($desc);
								$failPos = stripos($desc, 'Fail');
								$category = substr($desc, 0, $failPos);
								$details = substr($desc, $failPos, strlen($desc));
							}
														
							# Update details in db
							updateInspection($location_id, $inspectionDate, $severity, $resultText, $details, $category, $inspected);
						}
						else
						{						
							// Do we have an order 13?
							$text = $row3->childNodes(0)->innertext;
							if (strpos(strtolower($text), 'section 13 order served') !== FALSE)
							{
								echo 'Order 13 Served!<br />';
								
								// We found an Order 13 - so lets capture that information
								updateInspection($location_id, $inspectionDate, 'Closed', 'No', strip_tags($text), 'Order 13 Served', $inspected);
							}
							else if (strpos(strtolower($text), 'section 13 order revoked') !== FALSE)
							{
								echo 'Order 13 Revoked!<br />';
																
								// We found an Order 13 - so lets capture that information
								updateInspection($location_id, $inspectionDate, 'Closed', 'No', strip_tags($text), 'Order 13 Revoked', $inspected);
							}
							else
							{
								echo 'No infractions - record note!<br />';
								
								// We record a simple note as there was no infractions
								updateInspection($location_id, $inspectionDate, 'Note', 'No', ' ', strip_tags($text), $inspected);
							}
						}
					}
					$htmldata3->clear();
					unset($htmldata3);
	
					# Only process one inspection
					///break; Process all inspections - Aug. 5, 2010 - rtraction djm
				}
				
				$htmldata2->clear();
				unset($htmldata2);			
			}
		}
		
		# We've hit our timelimit above so we want to break out
		if ($break) { break; }
	}
	$html->clear();
	unset($html);

	// updated log table
	if (!$break)
	{
		updateLog();
	}
	else
	{
		# We hit a time block above and broke out of our loops
		# We're starting the process again but jumping ahead
		
		parseData($i);
	}
}

function updateLog()
{
	$query = "INSERT INTO updated (`update`) VALUES (NOW())";
	$result = queryResult($query);
	
	// Clean up any restaurants that have been removed
	// rtraction djm - Nov.3, 2010 - handle deleted restaurants
	$insert_query = "UPDATE restaurant SET active = 0 WHERE TO_DAYS(updated)+2 < TO_DAYS(now());";
	queryResult($insert_query);
}

function updateLocation($location, $address, $city, $inspected, $critical, $noncritical)
{
	$location = addslashes($location);
	$address = addslashes($address);
	$city = addslashes($city);

	$count_query = "SELECT restaurant_id, inspected FROM restaurant WHERE location = '$location' AND address = '$address' AND city = '$city'";
	$result = queryResult($count_query);

	if (mysql_num_rows($result) > 0)
	{
		//update
		$row = mysql_fetch_array($result);
		$restaurant_id = $row[0];
		$date_array = explode(" ", $row[1]);
		$date_day = $date_array[0];

		if ($date_day != $inspected)
		{
			$insert_query = "UPDATE restaurant SET location = '$location', address = '$address',
				city = '$city', inspected = '$inspected', critical = $critical, noncritical = $noncritical, updated = NOW(), active = 1 
				WHERE location = '$location' AND address = '$address' AND city = '$city'";
			queryResult($insert_query);
		}
		else
		{
			// Clean up any restaurants that have been removed
			// rtraction djm - Nov.3, 2010 - handle deleted restaurants
			$insert_query = "UPDATE restaurant SET updated = NOW() WHERE location = '$location' AND address = '$address' AND city = '$city'";
			queryResult($insert_query);
		}
	} else {
		//insert
		$insert_query = "INSERT INTO restaurant (location, address, city, inspected, critical, noncritical, updated, active) VALUES ('$location', '$address', '$city', '$inspected', $critical, $noncritical, NOW(), 1)";
		queryResult($insert_query);

		$restaurant_id = mysql_insert_id();
	}

	return $restaurant_id;
}

//njs - 10-28-10
//function was returning false positives
//removed critical/non-critical parameters and evaluation, only check for most recent inspection
function boolUpdateInspection($restaurant_id, $inspected)
{
	$query = "SELECT COUNT(inspection_id) FROM inspection WHERE restaurant_id = $restaurant_id AND inspected = '$inspected'";
	$result = queryResult($query);
	$update = true;

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$inspections = $row[0];
		
		if ($inspections > 0)
		{
			$update = false;
		}
	}
	
	return $update;
}

//njs - 10-28-10
//checks existing closed records and verifies that most recent inspection was a closure
//if it's a re-inspection, set closed to 0 but retain last closure date
function boolReInspection($restaurant_id, $inspected)
{
	$query = "SELECT COUNT(restaurant_id) AS count FROM restaurant WHERE restaurant_id = $restaurant_id AND closed_date < '$inspected' AND closed > 0 AND active = 1";
	$result = queryResult($query);
	$update = false;

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$closed = $row[0];
		
		if ($closed > 0)
		{
			$update = true;
			$query = "UPDATE restaurant SET closed = 0 WHERE restaurant_id = $restaurant_id";
			queryResult($query);
		}
	}
	
	return $update;
}

function updateInspection($restaurant_id, $inspectionDate, $severity, $inspection_type, $description, $category, $inspected)
{
	$add_inspection = false;
	$description = addslashes($description);
	$category = addslashes($category);
	$severity = addslashes($severity);
	$inspection_id = 0;

	$count_query = "SELECT inspection_id, inspected FROM inspection WHERE restaurant_id = $restaurant_id AND severity = '$severity' AND description = '$description' AND category = '$category' AND inspected = '$inspectionDate'";
	$result = queryResult($count_query);
	
	if (mysql_num_rows($result) < 1)
	{	
		$add_inspection = true;
	}

	if ($add_inspection)
	{
		$match = 0;
		if ($inspectionDate == $inspected)
		{
			$match = 1;
			$query = "UPDATE inspection SET active = 0 WHERE inspected < '$inspectionDate' and restaurant_id = $restaurant_id";
			queryResult($query);
		}
		
		$insert_query = "INSERT INTO inspection (restaurant_id, severity, inspection_type, category, description, inspected, active) VALUES ($restaurant_id, '$severity', '$inspection_type', '$category', '$description', '$inspectionDate', ".$match.")";
		queryResult($insert_query);
		$inspection_id = mysql_insert_id();
		
		// Handle adjustments to closed flag
		if ($match == 1)
		{
			$value = 0;
			if ($category == addslashes("Order 13 Served")) { 
				$value = 1; 
			}
			else if ($category == addslashes("Order 13 Revoked")) { 
				$value = -1; 
			}
			if ($severity == addslashes("Closed") && $value != 0)
			{
				$update_query = "UPDATE restaurant SET closed = closed+$value, closed_date = '$inspectionDate' where restaurant_id = $restaurant_id and active = 1";
				queryResult($update_query);
			}
		}
	}

	return $inspection_id;
}

function updateGeocode()
{
	$query = "SELECT restaurant_id, location, address, city, latitude, longitude FROM restaurant WHERE active = 1
		AND ((latitude IS NULL OR latitude = 0) OR (longitude IS NULL OR longitude = 0))";
	$result = queryResult($query);

	if (mysql_num_rows($result) > 0)
	{
		// Initialize delay in geocode speed
		$delay = 0;
		$base_url = "http://maps.google.com/maps/geo?output=xml"; //. "&key=" . KEY;

		// Iterate through the rows, geocoding each address
		while ($row = @mysql_fetch_assoc($result)) {
			$geocode_pending = true;

			while ($geocode_pending) {
				$address = $row["address"] . ', ' . $row["city"] . ', ON';
				$id = $row["restaurant_id"];
				$request_url = $base_url . "&q=" . urlencode($address);
				$xml = simplexml_load_file($request_url) or die("url not loading");

				$status = $xml->Response->Status->code;
				if (strcmp($status, "200") == 0) {
			  // Successful geocode
			  $geocode_pending = false;
			  $coordinates = $xml->Response->Placemark->Point->coordinates;
			  $coordinatesSplit = split(",", $coordinates);
			  // Format: Longitude, Latitude, Altitude
			  $lat = $coordinatesSplit[1];
			  $lng = $coordinatesSplit[0];

			  $query = sprintf("UPDATE restaurant " .
					 " SET latitude = '%s', longitude = '%s' " .
					 " WHERE restaurant_id = '%s' LIMIT 1;",
			  mysql_real_escape_string($lat),
			  mysql_real_escape_string($lng),
			  mysql_real_escape_string($id));
			  $update_result = mysql_query($query);
			  if (!$update_result) {
			  	die("Invalid query: " . mysql_error());
			  }
				} else if (strcmp($status, "620") == 0) {
			  // sent geocodes too fast
			  $delay += 100000;
				} else {
			  // failure to geocode
			  $geocode_pending = false;
			  echo "Address " . $address . " failed to geocoded. ";
			  echo "Received status " . $status . "
		\n";
				}
				usleep($delay);
			}
		}

	}
}


?>