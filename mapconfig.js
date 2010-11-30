var map;
var info;
var mgr;
var mgrPassed;
var mgrInfraction;
var mgrClosed;
var mgrAllInspected;
var mgrNotInspected;
var icons = {};
var allmarkers = [];

/* Inspection Result Types */
var inspectionResult_notInspected = "notinspected";
var inspectionResult_passed = "passed";
var inspectionResult_infraction = "infraction";
var inspectionResult_closed = "closed";
var inspectionResult_allInspected = "allinspected";

var locationsLoaded_passed = true;
var locationsLoaded_notInspected = false;
var locationsLoaded_infraction = false;
var locationsLoaded_closed = false;

var markersPassed = [];
var markersInfraction = [];
var markersClosed = [];
var markersNotInspected = [];

var iconData = {
  "green-dot": { width: 32, height: 32 },
  "red-dot": { width: 32, height: 32 },
  "yellow-dot": { width: 32, height: 32 },
  "blue-dot": { width: 32, height: 32 },
  "dkgrey-dot": { width: 32, height: 32 },
  "pin-shadow": { width: 40, height: 30 }
};

function opendialog() {
	$('#disclaimer').dialog( {
		width: 920,
		height: 320,
		resizable: true,
		autoResize: true,
		modal: true,
		autoOpen: true,
		title: 'Disclaimer',
		closeOnEscape: false, 
			buttons: { 
				"I Agree": function(){
					$('#disclaimer').dialog('close');
					var exp = new Date();     //set new date object
					exp.setTime(exp.getTime() + (1000 * 60 * 60 * 24 * 30));					
					document.cookie = 'lmhuinspect=iagree;expires='+exp.toGMTString();
				},
				"I Disagree": function() {
					window.open('http://inspection.healthunit.com/', '_self');
				}
			} 
		} );
}

function opensearch() {
	$('#searchouter').dialog( {
		width: 920,
		height: 420,
		minWidth: 720,
		minHeight: 300,
		resizable: true,
		modal: true,
		autoOpen: true,
		title: 'Search',
		closeOnEscape: false, 
			buttons: { 
				"Close": function(){
					$('#searchouter').dialog('close');
				}
			} 
		} );
}

function GetBitlyURL(resturant_id)
{
	// Default URL
	var short_url = 'http://eatsure.ca/index.php?inspect=' + resturant_id;
	
	$.ajax({
		url: 'ajax.php',
		dataType: 'text',
		data: 'action=GetBitlyURL&restaurant_id='+resturant_id,
		success: function(data)
		{
			// Setup a shortURL with bitly
			if (data != "") {
				short_url = data;
			}	
			var current = $("a#twittershare").attr('href');
			$("a#twittershare").attr('href', current+escape(short_url));
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			// Default the link to long in the event bitly fails
			var current = $("a#twittershare").attr('href');
			$("a#twittershare").attr('href', current+escape(short_url));
		}
	});
}

function BuildClosedDialog(locationData)
{
	var dialog = '';
	
	var myloc;
	var locCount = locationData.locations.length;
	if (locCount > 0) {
		for (j = 0; j < locCount; j++) {
			myloc = locationData.locations[j];
			dialog += '<div id="closed_details_' + myloc.id + '" class="closed_details">';
			dialog += '<p class="location"><span class="title">' + myloc.name + '</span><br />' + myloc.address + '<br />';
			dialog += '# of Inspections: ' + myloc.inspections + ' (<img src="images/legend_infraction.gif" alt="Infraction" /> ' + myloc.infraction + ' <img src="images/legend_closed.gif" alt="Closed" /> ' + myloc.closed + ')<br />';
			dialog += '<strong>Inspected:</strong> ' + myloc.inspected + '</p>';
			dialog += '</div>';
			dialog += '<div id="closed_click_more_' + myloc.id + '" class="closed_more">';
			dialog += '<a href="#" onclick="openinspection(' + myloc.id + ');return false;">Click Here for Details</a>';
			dialog += '</div>';
			dialog += '<hr class="closed" />';
		}
	}
	else
	{
		dialog += '<div id="closed_details" class="closed_details">';
		dialog += '<br /><br />No restaurants are closed.<br />';
		dialog += '</div>';
	}
	
	return dialog;
}

function BuildInspectionDialog(inspectionDetails)
{
	var dialog = '';
	var i = 0;
	var url = escape(window.location.hostname + '/share.php?inspect=' + inspectionDetails[0].restaurant_id);
	var url_title = escape('Check out the latest Food Inspection information for ' + inspectionDetails[0].location);
	var restaurant_id = 0;
	var inspection_date;
	var description = '';
	
	while (i < inspectionDetails.length)
	{
		if (inspectionDetails[i].inspected != '0000-00-00')
		{
			// Have to manually compensate for Timezone and DST by adding 5 hours to the date
			var tempDate = new Date(inspectionDetails[i].inspected.split('-')[0], Number(inspectionDetails[i].inspected.split('-')[1])-1, inspectionDetails[i].inspected.split('-')[2], 5, 0, 0, 0); 
			inspection_date = tempDate.toDateString();
		}else{
			inspection_date = "Never";
		}
		
		if (restaurant_id != inspectionDetails[i].restaurant_id) {			
			dialog += '<div id="inspection_details_' + inspectionDetails[i].restaurant_id + '" class="inspection_details">';
			dialog += '<p class="location"><span class="title">' + inspectionDetails[i].location + '</span><br />' + inspectionDetails[i].address + ', ' + inspectionDetails[i].city + '<br />';
			dialog += '<strong>Inspected:</strong> ' + inspection_date + '</p>';
		}
		
		description = inspectionDetails[i].description;
		description = description.replace('Fail', '<br />Fail');
		if (description.length > 0 && description.indexOf('<br />') == 0)
		{
			description = description.substring(6);
		}
		
		if (description != ''){
			dialog += '<p class="desc">' + description + '</p>';
			dialog += '<p class="category">' + inspectionDetails[i].category + '</p>';
			dialog += '<p class="severity"><strong>Severity:</strong> ' + inspectionDetails[i].severity; 
			if (inspectionDetails[i].inspection_type == 'CDI')
			{
				dialog += '&nbsp;&nbsp;<span class="cdi">Corrected During Inspection</span>';
			}
			dialog += '</p>';
		}
		else if(inspectionDetails[i].inspection_id == 0 && 
				inspectionDetails[i].infraction == 0 &&
				inspectionDetails[i].closed == 0){
			dialog += '<div class="passed"><img src="images/icon_no_infractions.gif" alt="No Infractions" /></div>';
		}
		
		restaurant_id = inspectionDetails[i].restaurant_id;
		i++;
	}
	
	dialog += '<div class="share">';
	dialog += '<a href="http://www.facebook.com/sharer.php?u=' + url + '&t=' + url_title + '" target="facebook">';
	dialog += '<img src="images/facebook_share.gif" alt="Share on Facebook" width="93" height="28" />';
	dialog += '</a></div>';	// close for <div class="share">;		
					
	dialog += '<div class="share">';
	dialog += '<a id="twittershare" href="http://twitter.com/home?status=' + url_title + '%20' + '" target="_blank">';
	dialog += '<img src="images/twitter_share.jpg" alt="Share on Twitter" width="" height="" />';
	dialog += '</a></div>';	// close for <div class="share">;
	
	// Add our short url to the tweet
	GetBitlyURL(inspectionDetails[0].restaurant_id);
	
	if (restaurant_id != 0)
	{
		dialog += '</div>';	// close for <div id="inspection_details...>
	}
	
	return dialog;
}

function showClosedDialog() {
	if (!$('.closed a').hasClass('active')) {
		var height = 315;
		$.ajax({
			url: 'ajax.php',
			dataType: 'text',
			data: 'action=GetLocationsByInspectionResult&inspectionResult=' + inspectionResult_closed,
			success: function(data){
				var locationData = JSON.decode(data, false);
				
				// Adjust height
				if (locationData.locations.length > 2)
				{
					height = 400;	
				}
				
				// Adjust the dialog height based on the users screen resolution
				if (typeof(window.innerHeight) == 'number' && height > 360) {
					height = window.innerHeight - 80;
					if (height > 600) {
						height = 600;
					}
				}
				
				$("#closedDialog").html(BuildClosedDialog(locationData));
				$("#closedDialog").dialog({
					width: 720,
					height: height,
					resizable: true,
					autoResize: true,
					modal: true,
					autoOpen: true,
					title: 'Closed Locations',
					closeOnEscape: false,
					buttons: {
						"OK": function(){
							$(this).dialog('close');
						}
					}
				});
			}
		});
	}
}

/* Jul.21, 2010 - Greg Smith: set the HTML for the inspectionDialog div, then displays a dialog */
function openinspection(inspectionid) {
	var height = 315;
	$.ajax({
		url: 'ajax.php',
		dataType: 'text',
		data: 'action=GetInspectionDetails&restaurant_id='+inspectionid,
		success: function(data)
		{
			var insp = JSON.decode(data, false);
			
			/* If there are infractions adjust the dialog height to accomodate */
			for(var i = 0; i < insp.inspectiondetails.length; i++){
				if(insp.inspectiondetails[i].severity == "Critical" || insp.inspectiondetails[i].severity == "Non-Critical" ||
				   insp.inspectiondetails[i].severity == "Closed"){
					height = 485;
					break;
				}
			}		
				
			// Adjust the dialog height based on the users screen resolution
			if(typeof(window.innerHeight)=='number' && height > 360)
			{
				height = window.innerHeight - 80;
				if (height > 600)
				{
					height = 600;
				}
			}
	
			$("#inspectionDialog").html(BuildInspectionDialog(insp.inspectiondetails));
			$("#inspectionDialog").dialog({
				width: 720,
				height: height,
				resizable: true,
				autoResize: true,
				modal: true,
				autoOpen: true,
				title: 'Inspection Details',
				closeOnEscape: false,
				buttons: { 
					"OK": function(){
						$(this).dialog('close');
						}
					}
			});
		}
	});
}

function getCookie(c_name)
{
	if (document.cookie.length>0)
	{
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1)
		{
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end == -1) {
				c_end = document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}

function preloadImages()
{
	image1 = new Image();
	image1.src = 'images/but_allinspected_over.gif';
	image2 = new Image();
	image2.src = 'images/but_closed_over.gif';	
	image3 = new Image();
	image3.src = 'images/but_infraction_over.gif';
	image4 = new Image();
	image4.src = 'images/but_notinspected_over.gif';
	image5 = new Image();
	image5.src = 'images/but_allinspected_over.gif';
	image6 = new Image();
	image6.src = 'images/disclaimer_over.gif';
	image7 = new Image();
	image7.src = 'images/subhead_over.gif';	
}

function createMarker(id, posn, title, icon, closed, infraction, inspected, address, inspections) {
	inspectiondetails = '<br /><strong><a href="#" onclick="openinspection('+id+');return false;">Click Here for Details</a></strong>';

    contentString = '<div class="info"><div class="info_title"><strong>'+
		title + '</strong></div><div class="info_address">'+
		address + '</div><div class="info_rating">Inspected: '+ 
    	inspected + '<br/>Closed: '+
    	closed + '<br/>Infraction: '+
    	infraction + inspectiondetails + '</div>';

	var markerOptions = {
    position: posn,
    title: title,
    content: contentString
  };
  if(icon != false){
    markerOptions.shadow = icon.shadow;
    markerOptions.icon   = icon.icon;
    markerOptions.shape  = icon.shape;
  }
    
  var marker = new google.maps.Marker(markerOptions);

  google.maps.event.addListener(marker, 'click', function() {
		info.setContent(marker.content);
		info.open(map, marker);
  });
  return marker;
}

function getIcon(images) {
  var icon = false;
  if (images) {
    if (icons[images[0]]) {
      icon = icons[images[0]];
    } else {                    
        var iconImage = new google.maps.MarkerImage('images/' + images[0] + '.png',
          new google.maps.Size(iconData[images[0]].width, iconData[images[0]].height),
          new google.maps.Point(0,0),
          new google.maps.Point(0, 32));
        
        var iconShadow = new google.maps.MarkerImage('images/' + images[1] + '.png',
          new google.maps.Size(iconData[images[1]].width, iconData[images[1]].height),
          new google.maps.Point(0,0),
          new google.maps.Point(0, 32));
        
        var iconShape = {
          coord: [1, 1, 1, 32, 32, 32, 32, 1],
          type: 'poly'
        };

        icons[images[0]] = {
          icon : iconImage,
          shadow: iconShadow,
          shape : iconShape
        };
    }
  }
  return icon;
}

function setupOfficeMarkers() {
  allmarkers.length = 0;
  markersPassed.length = 0;
  markersInfraction.length = 0;
  markersClosed.length = 0;
  markersNotInspected.length = 0;
  
  for (var i in restaurantLayer) {
    if (restaurantLayer.hasOwnProperty(i)) {
      var layer = restaurantLayer[i];
      
      var mPassed = [];
      for (var j in layer.places) {
        if (layer.places.hasOwnProperty(j)) {
			var place = layer.places[j];
			getIcon(place.icon);
			var posn = new google.maps.LatLng(place.posn[0], place.posn[1]);
			var marker = createMarker(place.id, posn, place.name, getIcon(place.icon), place.closed, place.infraction, place.inspected, place.address, place.inspections);
			//var marker = new com.redfin.FastMarker(id, posn, ["<img src='images/green-dot.png'>"], "myMarker", 0, 10/*px*/, 10/*px*/);

			mPassed.push(marker);
			markersPassed.push(marker);
        }
      }
      mgrPassed.addMarkers(mPassed, layer.zoom[0], layer.zoom[1]);
    }
  }
  
  //new com.redfin.FastMarkerOverlay(map, markersPassed);
  mgrPassed.refresh();
}

function load() { 
  var mapHome = new google.maps.LatLng(42.98, -81.25);
  var myOptions = {
    zoom: 16,
    center: mapHome,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map'), myOptions);
  info = new google.maps.InfoWindow();

  mgr = new MarkerManager(map);
  mgrPassed = new MarkerManager(map);
  mgrInfraction = new MarkerManager(map);
  mgrClosed = new MarkerManager(map);
  mgrNotInspected = new MarkerManager(map);
  
  google.maps.event.addListener(mgrNotInspected, 'loaded', function(){
    setupOfficeMarkers();
    google.maps.event.addListener(map, 'zoom_changed', function() {
    });
  }); 
}

function showMarkers() {
  mgr.show();
  updateStatus(mgr.getMarkerCount(map.getZoom()));
}

function hideMarkers() {
  mgr.hide();
  updateStatus(mgr.getMarkerCount(map.getZoom()));
}

function deleteMarker() {
  var markerNum = parseInt(document.getElementById("markerNum").value);
  mgr.removeMarker(allmarkers[markerNum]);
  updateStatus(mgr.getMarkerCount(map.getZoom()));
}

function clearMarkers() {
  mgr.clearMarkers();
  updateStatus(mgr.getMarkerCount(map.getZoom()));
}

function reloadMarkers() {
  setupOfficeMarkers();
}

function showPassed()
{
	mgrPassed.toggle();	
	if(!$('.passed a').hasClass('active'))
	{
		$('.passed a').addClass('active');
		if (!$('.allinspected a').hasClass('active') && $('.infraction a').hasClass('active') && $('.closed a').hasClass('active')) {
			$('.allinspected a').addClass('active');
		}
		
	} else {
		$('.passed a').removeClass('active');
		if ($('.allinspected a').hasClass('active')) {
			$('.allinspected a').removeClass('active');
		}
		info.close();
	}
}

function showInfraction()
{
	if(!locationsLoaded_infraction)
	{	  
		$.ajax({
			url: 'ajax.php',
			dataType: 'text',
			data: 'action=GetLocationsByInspectionResult&inspectionResult='+inspectionResult_infraction,
			success: function(data){
				var locationData = JSON.decode(data, false);
				
				markersInfraction.length = 0;
				
				for (var i in restaurantLayer) {
				    if (restaurantLayer.hasOwnProperty(i)) {
						var layer = restaurantLayer[i];
						
						var layer = restaurantLayer[0];
						
						var mInfraction = [];
						var place;
						var locCount = locationData.locations.length;
						for (j = 0; j < locCount; j++) {
							place = locationData.locations[j];
							getIcon(place.icon);
							var posn = new google.maps.LatLng(place.posn[0], place.posn[1]);
							var marker = createMarker(place.id, posn, place.name, getIcon(place.icon), place.closed, place.infraction, place.inspected, place.address, place.inspections);
							//var marker = new com.redfin.FastMarker(id, posn, ["<img src='images/blue-dot.png'>"], "myMarker", 0, 10, 10);
							
							mInfraction.push(marker);
							markersInfraction.push(marker);
						}
						
						mgrInfraction.addMarkers(mInfraction, layer.zoom[0], layer.zoom[1]);
					}
				}
					//new com.redfin.FastMarkerOverlay(map, mInfraction);					
					locationsLoaded_infraction = true;
					
					mgrInfraction.refresh();
					mgrInfraction.show();
					
					$('.infraction a').addClass('active');
					if (!$('.allinspected a').hasClass('active') && $('.passed a').hasClass('active') && $('.closed a').hasClass('active')) {
						$('.allinspected a').addClass('active');
					}
			}		
		});
	}
	else
	{
		mgrInfraction.toggle();
		if(!$('.infraction a').hasClass('active'))
		{
			$('.infraction a').addClass('active');
			if (!$('.allinspected a').hasClass('active') && $('.passed a').hasClass('active') && $('.closed a').hasClass('active')) {
				$('.allinspected a').addClass('active');
			}
		} else {
			$('.infraction a').removeClass('active');
			if ($('.allinspected a').hasClass('active')) {
				$('.allinspected a').removeClass('active');
			}
			info.close();
		}
	}
}

function showClosed()
{
	if (!locationsLoaded_closed) {
		$.ajax({
			url: 'ajax.php',
			dataType: 'text',
			data: 'action=GetLocationsByInspectionResult&inspectionResult=' + inspectionResult_closed,
			success: function(data){
				var locationData = JSON.decode(data, false);
				
				markersClosed.length = 0;
				
				for (var i in restaurantLayer) {
				    if (restaurantLayer.hasOwnProperty(i)) {
						var layer = restaurantLayer[i];
						
						var mClosed = [];
						
						var locCount = locationData.locations.length;
						for (j = 0; j < locCount; j++) {
							place = locationData.locations[j];
							getIcon(place.icon);
							var posn = new google.maps.LatLng(place.posn[0], place.posn[1]);
							var marker = createMarker(place.id, posn, place.name, getIcon(place.icon), place.closed, place.infraction, place.inspected, place.address, place.inspections);
							//var marker = new com.redfin.FastMarker(id, posn, ["<img src='images/blue-dot.png'>"], "myMarker", 0, 10, 10);
							
							mClosed.push(marker);
							markersClosed.push(marker);
						}
						
						mgrClosed.addMarkers(mClosed, layer.zoom[0], layer.zoom[1]);
					}
					}
					//new com.redfin.FastMarkerOverlay(map, mClosed);					
					locationsLoaded_closed = true;
					
					mgrClosed.refresh();
					mgrClosed.show();
					
					$('.closed a').addClass('active');
					if (!$('.allinspected a').hasClass('active') && $('.passed a').hasClass('active') && $('.infraction a').hasClass('active')) {
						$('.allinspected a').addClass('active');
					}
				}
		});
	}
	else {
		mgrClosed.toggle();
		if (!$('.closed a').hasClass('active')) {
			$('.closed a').addClass('active');
			if (!$('.allinspected a').hasClass('active') && $('.passed a').hasClass('active') && $('.infraction a').hasClass('active')) {
				$('.allinspected a').addClass('active');
			}
		}
		else {
			$('.closed a').removeClass('active');
			if ($('.allinspected a').hasClass('active')) {
				$('.allinspected a').removeClass('active');
			}
			info.close();
		}
	}
}

function showAllInspected()
{
	if ($('.allinspected a').hasClass('active')) {	
		mgrPassed.toggle();
		mgrInfraction.toggle();
		mgrClosed.toggle();
				
		$('.passed a').removeClass('active');
		$('.infraction a').removeClass('active');
		$('.closed a').removeClass('active');
		$('.allinspected a').removeClass('active');
	}
	else {
		if (!$('.passed a').hasClass('active')) {
			$('.passed a').addClass('active');
			mgrPassed.show();
		}
		if (!$('.infraction a').hasClass('active')) {
			showInfraction();
		}
		if (!$('.closed a').hasClass('active')) {
			showClosed();
		}
		if (!$('.allinspected a').hasClass('active')) {
			$('.allinspected a').addClass('active');
		}
	}
}

function showNotInspected()
{
	if (!locationsLoaded_notInspected) {
		$.ajax({
			url: 'ajax.php',
			dataType: 'text',
			data: 'action=GetLocationsByInspectionResult&inspectionResult=' + inspectionResult_notInspected,
			success: function(data){
				var locationData = JSON.decode(data, false);
				
				markersNotInspected.length = 0;
				
				for (var i in restaurantLayer) {
				    if (restaurantLayer.hasOwnProperty(i)) {
						var layer = restaurantLayer[i];
						
						var mNotInspected = [];
						
						var locCount = locationData.locations.length;
						for (j = 0; j < locCount; j++) {
							place = locationData.locations[j];
							getIcon(place.icon);
							var posn = new google.maps.LatLng(place.posn[0], place.posn[1]);
							var marker = createMarker(place.id, posn, place.name, getIcon(place.icon), place.closed, place.infraction, place.inspected, place.address, place.inspections);
							//var marker = new com.redfin.FastMarker(id, posn, ["<img src='images/blue-dot.png'>"], "myMarker", 0, 10, 10);
							
							mNotInspected.push(marker);
							markersNotInspected.push(marker);
						}
						
						mgrNotInspected.addMarkers(mNotInspected, layer.zoom[0], layer.zoom[1]);
					}
					}
					//new com.redfin.FastMarkerOverlay(map, mCritical);					
					locationsLoaded_notInspected = true;
					
					mgrNotInspected.refresh();
					mgrNotInspected.show();
					
					$('.notinspected a').addClass('active');
				}
		});
	}
	else
	{
		mgrNotInspected.toggle();
		
		if (!$('.notinspected a').hasClass('active')) {
			$('.notinspected a').addClass('active');
		}
		else {
			$('.notinspected a').removeClass('active');
			info.close();
		}
	}
}