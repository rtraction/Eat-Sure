<?php
//if the user leaves the page or closes the browser prematurely, this will help prevent half completed statements
ignore_user_abort();

include('ajaxfunctions.php');
   
if($_GET['action'])
{
	$json = "";
	switch($_GET['action'])
    {
		case 'test':
            echo test();
            exit;
        break;
		case 'GetBitlyURL':
			if($_GET['restaurant_id'])
			{
				$json = GetBitlyURL($_GET['restaurant_id']);
			}
		break;
		case 'GetInspectionDetails':
			if($_GET['restaurant_id'])
			{
				$json = GetInspectionDetails($_GET['restaurant_id']);
			}
		break;
		case 'GetLocationsByInspectionResult':
    		if($_GET['inspectionResult'])
			{
				$json = GetLocationsByInspectionResult($_GET['inspectionResult']);
			}
		break;
	}//end switch
	
	echo $json;
}//end if
?>

