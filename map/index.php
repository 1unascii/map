<!DOCTYPE html>
<html>
  <head>
    <title>Geocoding service</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="geocoder/css/default.css" type="text/css">
  </head>
  <body>
      
    <div id="map"></div>

<script type="text/javascript">

  var $_GET = {};
  var errors = [];
  var lat;
  var lng;
  var center;
  var myHome;
  var wickedLocation;
  var locations = [];
  var addresses = [];
  var beaches = [
    ['Bondi Beach', -33.890542, 151.274856, 4],
    ['Coogee Beach', -33.923036, 151.259052, 5],
    ['Cronulla Beach', -34.028249, 151.157507, 3],
    ['Manly Beach', -33.80010128657071, 151.28747820854187, 2],
    ['Maroubra Beach', -33.950198, 151.259302, 1]
  ];
  var count = 0;

  document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
      function decode(s) {
          return decodeURIComponent(s.split("+").join(" "));
      }
    $_GET[decode(arguments[1])] = decode(arguments[2]);
  });

  function initMap() {

    var geocoder = new google.maps.Geocoder();
    addresses = $_GET['addresses'].split(",");
    
    for(var i = 0; i < addresses.length; i++) {
      
      geocoder.geocode({'address': addresses[i]}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          count++;
          lat = String(results[0].geometry.location.lat());
          lng = String(results[0].geometry.location.lng());
          var address = results[0].address_components[0].short_name;
          locations.push([address, lat, lng, count]);
          
        } else {
          alert('Geocode was not successful for the following reason: ' + status);
        }
      });
    }  

    window.onload = function() {

      myHome = { "lat" : locations[0][1] , "lng" : locations[0][2] };
      wickedLocation = new google.maps.LatLng( myHome.lat, myHome.lng );
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: wickedLocation
      });

      setMarkers(map);
      
      function setMarkers(map) {

        // Adds markers to the map.

        // Marker sizes are expressed as a Size of X,Y where the origin of the image
        // (0,0) is located in the top left of the image.

        // Origins, anchor positions and coordinates of the marker increase in the X
        // direction to the right and in the Y direction down.
        var image = {
          url: 'custom-markers/images/dell.png',
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(32, 32),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(16, 16)
        };
        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };
        //locations = beaches;
        for (var i = 0; i < locations.length; i++) {
          
          lat = Number(locations[i][1]);
          lng = Number(locations[i][2]);
          var marker = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            icon: image,
            shape: shape,
            title: String(beaches[0]),
            zIndex: Number(beaches[3])
          });
        }
      }
    };  
  }
</script> <!--INIT MAP-->
    <?php include "google-api-key.php"; ?> <!--GOOGLE MAP API-->
  </body>
</html>