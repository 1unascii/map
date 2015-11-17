<!DOCTYPE html>
<html>
  <head>
    <title>Geocoding service</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="geocoder/css/default.css" type="text/css">
  </head>
  <body>
    <div id="floating-panel">
    <form>
      <label>Company</label>
      <input id="oem-icon" type="radio" name="icon-set" value="OEM">
      <label>Type</label>
      <input id="type-icon" type="radio" name="icon-set" value="MT">
    </form>
    </div>  
    <div id="map"></div>

<script type="text/javascript">

  var $_GET = {};
  var errors = [];
  var center;
  var locations = [];
  var addresses = [];
  var markers = [];
  var centerCoords = {"lat": 0, "lng": 0};

  document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
      function decode(s) {
          return decodeURIComponent(s.split("+").join(" "));
      }
    $_GET[decode(arguments[1])] = decode(arguments[2]);
  });

  function initMap() {

    var styles = [
      {
          "stylers": [
              {
                  "hue": "#ff1a00"
              },
              {
                  "invert_lightness": false
              },
              {
                  "saturation": -100
              },
              {
                  "lightness": 33
              },
              {
                  "gamma": 0.5
              }
          ]
      },
      {
          "featureType": "water",
          "elementType": "geometry",
          "stylers": [
              {
                  "color": "#2D333C"
              }


          ]
      }
    ];

    var geocoder = new google.maps.Geocoder();
    addresses = $_GET['addresses'].split(",");//Do not move!!! 
    center = $_GET['center'];
    var count = 0;
    
    for(var i = 0; i < addresses.length; i++) {
      
      geocoder.geocode({'address': addresses[i]}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {

          count++;//count *inside* the function, which is inside the loop

          locations.push({ 
            address: results[0].address_components[0].short_name, 
            lat: results[0].geometry.location.lat(), 
            lng: results[0].geometry.location.lng(),
            index: count 
          });
          
        } else {
          alert('Geocode was not successful for the following reason: ' + status);
        }
      });
    } 

    geocoder.geocode({'address': center}, function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {
        centerCoords.lat = results[0].geometry.location.lat();
        centerCoords.lng = results[0].geometry.location.lng();
      }
    });

    window.onload = function() {

      // Create a new StyledMapType object, passing it the array of styles,
      // as well as the name to be displayed on the map type control.
      var styledMap = new google.maps.StyledMapType(styles,
        {name: "Styled Map"});

      // Create a map object, and include the MapTypeId to add
      // to the map type control.
      var mapOptions = {
        zoom: 9,
        center: new google.maps.LatLng(centerCoords.lat, centerCoords.lng),
        mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
      };

      var map = new google.maps.Map(document.getElementById('map'),
        mapOptions);

      /**
      Radio Buttons
      **/
      document.getElementById('oem-icon').addEventListener('click', function() {
        setMarkers(map);
      });
      document.getElementById('type-icon').addEventListener('click', function() {
        setMarkers(map);
      });

      //Associate the styled map with the MapTypeId and set it to display.
      map.mapTypes.set('map_style', styledMap);
      map.setMapTypeId('map_style');

      setMarkers(map);
      
      function setMarkers(map) {

        deleteMarkers();

        if(document.getElementById('oem-icon').checked){
          var image = {
            url: 'custom-markers/images/dell.png',
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 16)
          };
        } if (document.getElementById('type-icon').checked) {
          var image = {
            url: 'custom-markers/images/laptop.png',
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 16)
          };
        }
        
      
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };

        for (var i = 0; i < locations.length; i++) {
          
          var marker = new google.maps.Marker({

            position: {lat: Number(locations[i].lat), lng: Number(locations[i].lng)},
            map: map,
            icon: image,
            shape: shape,
            title: String(locations[i].address),
            zIndex: Number(locations[i].index)

          });
          markers.push(marker);
        }
      }

      // Sets the map on all markers in the array.
      function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }

      // Removes the markers from the map, but keeps them in the array.
      function clearMarkers() {
        setMapOnAll(null);
      }


      // Deletes all markers in the array by removing references to them.
      function deleteMarkers() {
        clearMarkers();
        markers = [];
      }

    };  
  }
</script> <!--INIT MAP-->
    <?php include "google-api-key.php"; ?> <!--GOOGLE MAP API-->
  </body>
</html>