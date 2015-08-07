var map = null;
var geocoder = null;
var marker;

//var m_shadow = new google.maps.MarkerImage(templateDir +'img/m_shadow.png', new google.maps.Size(22, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));
//var m_red = new google.maps.MarkerImage(templateDir + 'img/m_red.png', new google.maps.Size(12, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));

function exp(tel) {
el = document.getElementById('item'+tel)
if (el.style.display=='none'){
el.style.display='';
document.getElementById('state'+tel).src=templateDir + 'img/minus.gif';
}
else{
el.style.display='none';
document.getElementById('state'+tel).src=templateDir + 'img/plus.gif';
}
}

function createMarker(point,title) {
	var m_shadow = new google.maps.MarkerImage(templateDir +'img/m_shadow.png', new google.maps.Size(22, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));
	var m_red = new google.maps.MarkerImage(templateDir + 'img/m_red.png', new google.maps.Size(12, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));
	
	if ( marker ) {
		marker.setPosition(point);
	} else {
		marker = new google.maps.Marker({
		position: point,
		map: map,
		shadow: m_shadow,
		icon: m_red,
		draggable: true,
		crossOnDrag: true,
		title: title
		});
	}
	
	google.maps.event.addListener(marker, 'dragend', function(event) {
		document.getElementById("lat").value = marker.position.lat();
		document.getElementById("lng").value = marker.position.lng();
	});

	document.getElementById("lat").value = point.lat();
	document.getElementById("lng").value = point.lng();

	return marker;
}


function load() {

if (jQuery("#map").length > 0){
map = new google.maps.Map(
	document.getElementById('map'), {
	center: new google.maps.LatLng(50.9272, 4.3176),
	zoom: 7,
	zoomControl: 1,
	scaleControl: 1,
	mapTypeId: google.maps.MapTypeId.ROADMAP
});

geocoder = new google.maps.Geocoder();


google.maps.event.addListener(map, 'click', function(event) {
  createMarker(event.latLng,'Hier gaat onze werking door');
});


//var lat = document.getElementById("lat").value;
//var lng = document.getElementById("lng").value;
mylocation = new google.maps.LatLng(document.getElementById("lat").value, document.getElementById("lng").value)

//createMarker(new google.maps.LatLng(lat, lng), 'Hier gaat onze werking door');
if (mylocation){
createMarker(mylocation, 'Hier gaat onze werking door');
}

}


jQuery("#gem").autocomplete({
	source: function( request, response ) {
	//cc = escape(document.getElementById("countrycode").value);
	//cc = $( "#countrycode" ).value;
	cc = 'BE'; // not used
		url = "https://my.scoutnet.be/service/postcode.php?str=" + escape(request.term) + "&cc=" + cc;
        
        jQuery.getJSON(url + '&callback=?', function(data) {
            //console.log(data);
            response(data);
        });
    }
});



}

function showAddress() {

//address=document.getElementById("street").value;
//var selObj = document.getElementById("postcodeid");
//var selIndex = selObj.selectedIndex;
//city = selObj.options[selIndex].text;
address = document.getElementById("street").value + ", " + document.getElementById("gem").value;
if (geocoder){
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
		map.setCenter(results[0].geometry.location);
		map.setZoom(10);
		createMarker(results[0].geometry.location,'our location');
  		document.getElementById("lat").value = results[0].geometry.location.lat();
		document.getElementById("lng").value = results[0].geometry.location.lng();
		} else {
		alert("Geocode failed: " + status);
		}
	});
}

}


jQuery(document).ready(function() {

	load();

});
