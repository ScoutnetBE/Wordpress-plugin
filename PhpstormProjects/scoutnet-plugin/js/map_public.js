var map = null;

function loadMap(){

var m_shadow = new google.maps.MarkerImage(templateDir +'img/m_shadow.png', new google.maps.Size(22, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));
var m_red = new google.maps.MarkerImage(templateDir + 'img/m_red.png', new google.maps.Size(12, 20), new google.maps.Point(0,0), new google.maps.Point(6, 20));

var lat = document.getElementById("lat").value;
var lng = document.getElementById("lng").value;

point = new google.maps.LatLng(lat, lng);

map = new google.maps.Map(
	document.getElementById('map'), {
	center: point,
	zoom: 13,
	zoomControl: 1,
	scaleControl: 1,
	mapTypeId: google.maps.MapTypeId.ROADMAP
});

var marker = new google.maps.Marker({
	position: point,
	map: map,
	shadow: m_shadow,
	icon: m_red,
	title: "Ons lokaal"
});

setTimeout('document.getElementById("map").style.backgroundImage = "";',1000); 
}

function activateMap(){
	var script = document.createElement("script");
	script.setAttribute("src", "http://maps.googleapis.com/maps/api/js?key=AIzaSyCwvKXGLUemghNxHVuYoiH8wEkoFfbVSgs&sensor=false&callback=loadMap");
	script.setAttribute("type", "text/javascript");
	document.documentElement.firstChild.appendChild(script);
}


function sn_scramble(code,key){
	//coded = "fbmfiVpyp@V23@S6lJMxHvpU@bu"
	//key = "98UJ3q.RmbHOyjDJFknIHNIe7PfuG8td0Fl9Vp5sog2C@hYWv1N"
	shift=code.length;
	link=""	
	for (i=0; i<code.length; i++) {
		if (key.indexOf(code.charAt(i))==-1) {
			ltr = code.charAt(i)
			link += (ltr)
	} else {     
	ltr = (key.indexOf(code.charAt(i))-shift+key.length) % key.length
	link += (key.charAt(ltr))
	}
	}
document.write("<a href='mailto:"+link+"'>"+link+"</a>")
}


/*var map = null;
var geocoder = null;
var marker;


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
		draggable: false,
		crossOnDrag: true,
		title: title
		});
	}
	
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


mylocation = new google.maps.LatLng(document.getElementById("lat").value, document.getElementById("lng").value)

if (mylocation){
createMarker(mylocation, 'Hier gaat onze werking door');
}

}


}



jQuery(document).ready(function() {

	load();

});*/
