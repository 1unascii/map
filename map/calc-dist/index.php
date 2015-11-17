<!DOCTYPE html>
<html>
  <head>
    <title>Distance Matrix service</title>
    <link rel="stylesheet" href="css/default.css" type="text/css">
  </head>
  <body>
    <div id="right-panel">
      <div id="inputs">
        <pre>
var origin1 = {lat: 55.930, lng: -3.118};
var origin2 = 'Greenwich, England';
var destinationA = 'Stockholm, Sweden';
var destinationB = {lat: 50.087, lng: 14.421};
        </pre>
      </div>
      <div>
        <strong>Results</strong>
      </div>
      <div id="output"></div>
    </div>
    <div id="map"></div>
    <script src="js/distances.js" type="text/javascript"></script>
    <?php include "../google-api-key.php"; ?> <!--GOOGLE MAP API-->
  
  </body>
</html>