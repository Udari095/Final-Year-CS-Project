<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in'])) {
  header('Location: index.php');
  exit();
}

include("./includes/config.php");


$title = htmlspecialchars($_GET['spec']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Colombo Green Explorer</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="map.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <style>
    #map {
      height: 100%;
      width: 100%;
    }

    .pac-container {
      z-index: 10000 !important;
    }
  </style>
</head>

<body class="bg-gray-100">
  <div id="feedbackModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h1 class="text-1xl font-semibold mb-6">Feedback Form</h1>
      <form id="feedbackForm" method="post" action="feedback.php">
        <div class="feedback-section">
          <h3>How do you feel about the experience of the app?</h3>
          <select name="experience" required class="controls w-4/5 p-2 border rounded">
            <option value="" disabled selected>Select an option</option>
            <option value="I am very satisfied, the app meets my needs well">I am very satisfied, the app meets my needs well.</option>
            <option value="I am satisfied, the app functions as expected">I am satisfied, the app functions as expected.</option>
            <option value="I feel neutral about the app, it’s just okay">I feel neutral about the app, it’s just okay.</option>
            <option value="I am somewhat dissatisfied, the app needs improvements">I am somewhat dissatisfied, the app needs improvements.</option>
            <option value="I am very dissatisfied, the app often frustrates me">I am very dissatisfied, the app often frustrates me.</option>
          </select>
        </div>

        <div class="feedback-section">
          <h3>Do you have any additional comments or suggestions for the app?</h3>
          <textarea class="controls w-4/5 p-2 border rounded" name="comment" rows="4" cols="50" placeholder="Write your comments here..." required class="controls w-4/5 p-2 border rounded"></textarea>
        </div>

        <div class="feedback-section">
          <h3>What is your selected area?</h3>
          <select name="area_text" required class="controls w-4/5 p-2 border rounded">
            <?php


            echo '<option value="" disabled selected>Select an area</option>';

            foreach ($locations as $key => $value) {
              echo '<option value="' . $key . '">' . slugToNormalString($key) . '</option>' . PHP_EOL;
            }

            ?>
          </select>
        </div>

        <div class="feedback-section">
          <h3>How do you feel about area?</h3>
          <textarea class="controls w-4/5 p-2 border rounded" name="feedback_text" rows="4" cols="50" placeholder="Write your comments here..." required class="controls w-4/5 p-2 border rounded"></textarea>
        </div>

        <div class="feedback-section">
          <h3>How you rate the area:</h3>
          <div class="rating" id="starRating">
            <span class="star" onclick="updateRating(1)">★</span>
            <span class="star" onclick="updateRating(2)">★</span>
            <span class="star" onclick="updateRating(3)">★</span>
            <span class="star" onclick="updateRating(4)">★</span>
            <span class="star" onclick="updateRating(5)">★</span>
          </div>
          <input required type="hidden" name="rating" id="ratingInput" value="0">
          <p id="output"></p>
        </div>

        <button class="bg-green-400 px-4 py-3" type="submit">Submit Feedback</button>
      </form>
    </div>
  </div>
  <div id="resultModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="result-section">
      </div>
    </div>
  </div>
  <div class="flex flex-col min-h-screen">
    <?php include("./includes/navbar.php"); ?>

    <div class="flex flex-1">

      <!-- Sidebar -->
      <div class="w-1/5 bg-green-800 text-white p-4">
        <?php include("./includes/sidebar.php"); ?>
      </div>

      <!-- Main Content -->
      <div class="flex-1 bg-gray-200 p-8">
        <h2 class="text-3xl font-semibold mb-6"><?php echo $title; ?></h2>

        <img class="mb-4" src="./images/dd.png" style="height:40px; width:500px">

        <!-- Search Box and Save Button -->
        <div class="mb-4">
          <select id="selLoc" class="controls w-4/5 p-2 border rounded" onchange="updateLocation(this.value)">
            <?php


            echo '<option value="null" disabled selected>Select an area</option>';

            foreach ($locations as $key => $value) {
              echo '<option value="' . $key . '">' . slugToNormalString($key) . '</option>' . PHP_EOL;
            }

            ?>
          </select>
          <div class="flex gap-4">
            <button onclick="process()" class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Process</button>
          </div>
        </div>




        <!-- Map Container -->
        <div class="relative w-full h-screen">
          <div id="map" class="w-full h-full"></div>
        </div>
      </div>



      <!-- Form for Saving Location -->
      <form id="save-location-form" method="POST" action="map.php" class="hidden">
        <input type="hidden" id="location-text" name="spec" value="<?php echo htmlspecialchars($_GET['spec']); ?>">
        <input type="hidden" id="location-text" name="location_text">
        <input type="hidden" id="location-coordinates" name="location_coordinates">
      </form>

      <!-- Feedback Modal -->


    </div>
  </div>

  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $mapApiKey; ?>&libraries=places&callback=initMap" async defer></script>
  <script>
    var locations = <?php echo json_encode($locations); ?>;
    var map;
    var mapCenter;
    var location;


    // Function to generate random number between min and max
    function getRandomNumber(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function getRandomColor() {
      const colors = ['#6DFE00', '#D6FF00', '#FFFE0B', '#FF9A00', '#EC0406'];
      return colors[Math.floor(Math.random() * colors.length)];
    }

    function process() {

      var loc = getQueryParam('location');

      if (!loc) {
        alert("Please select a location!");
        return;
      }


      $.get('<?php echo $APPURI; ?>prediction/?indicator=true&spec=<?php echo strtolower(htmlspecialchars($_GET['spec'])); ?>&location=' + loc, function(data) {
        // Set the fetched content as the modal content

        const jsonData = JSON.parse(data);


        // Define a custom SVG icon
        var customIcon = {
          path: google.maps.SymbolPath.CIRCLE, // Use a circle
          fillColor: jsonData.color,
          fillOpacity: 1, // Full opacity
          strokeWeight: 1, // Border thickness
          strokeColor: "#FFFFFF", // White border
          scale: 30, // Size of the circle
        };

        var marker = new google.maps.Marker({
          position: mapCenter,
          map: map,
          title: "city -> " + loc + " Score -> " + jsonData.score,
          icon: customIcon
        });

       /* marker.addListener('click', function() {
          // Create an iframe element
          var iframe = $('<iframe>', {
            src: '<?php echo $APPURI; ?>prediction/?location=' + loc,
            width: '100%',
            height: '400px', // Adjust the height as needed
            frameborder: '0'
          });

          // Clear any previous content in the modal and append the iframe
          $('#resultModal #result-section').html(iframe);

          // Show the modal
          $('#resultModal').show();
        });*/
      });
    }


    function getQueryParam(param) {
      let params = new URLSearchParams(window.location.search);
      return params.get(param);
    }

    function initMap() {
      // Coordinates for Colombo district boundary
      var colomboBounds = {
        north: 7.0000,
        south: 6.7800,
        east: 80.0300,
        west: 79.8000
      };

      var defaultLocation = {
        lat: 6.9271,
        lng: 79.8612
      };
      var locationParam = getQueryParam('location_coordinates');



      if (locationParam) {
        var coords = locationParam.split(',');
        if (coords.length === 2) {
          var lat = parseFloat(coords[0]);
          var lng = parseFloat(coords[1]);
          if (!isNaN(lat) && !isNaN(lng)) {
            mapCenter = {
              lat: lat,
              lng: lng
            };
          } else {
            mapCenter = defaultLocation;
          }
        } else {
          mapCenter = defaultLocation;
        }
      } else {
        mapCenter = defaultLocation;
      }

      var loc = getQueryParam('location');


      var selectElement = document.getElementById('selLoc');
      for (var i = 0; i < selectElement.options.length; i++) {
        var option = selectElement.options[i];
        if (option.value === loc) {
          option.selected = true;
          break;
        }
      }


      map = new google.maps.Map(document.getElementById('map'), {
        center: mapCenter,
        zoom: 12,
        restriction: {
          latLngBounds: colomboBounds,
          strictBounds: true
        },
        minZoom: 12,
        maxZoom: 20
      });

      // Prevent zoom out
      map.addListener('zoom_changed', function() {
        if (map.getZoom() < 12) map.setZoom(12);
      });
    }

    // Initialize the map
    google.maps.event.addDomListener(window, 'load', initMap);

    function updateLocation(selectedLocation) {
      if (selectedLocation !== 'null') {
        var selectedCoords = locations[selectedLocation];
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?spec=<?php echo htmlspecialchars($_GET['spec']); ?>' + '&location=' + selectedLocation + '&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
        window.location.href = newUrl;
      }
    }
  </script>
  <script>
    let feedbackVar = true;

    document.addEventListener("DOMContentLoaded", function() {
      // Get all anchor tags on the page
      var allLinks = document.getElementsByTagName("a");

      // Iterate through each link
      for (var i = 0; i < allLinks.length; i++) {
        // Add event listener to each link
        allLinks[i].addEventListener("click", function(event) {
          event.preventDefault(); // Prevent the default action (e.g., following the link)

          // Example: Log the href of the clicked link
          console.log("Clicked link:", this.href);

          var href = this.getAttribute("href");


          if (feedbackVar && href && href.indexOf("spec.php") === -1) {

            $('#feedbackModal').show();
            feedbackVar = false;


          } else {
            window.location.href = href;

          }

        });
      }
    });
  </script>

  <script>
    let stars = document.getElementsByClassName("star");
    let output = document.getElementById("output");
    let ratingInput = document.getElementById("ratingInput");

    // Function to update rating display
    function updateRating(n) {
      ratingInput.value = n;
      for (let i = 0; i < 5; i++) {
        if (i < n) {
          stars[i].classList.add("active");
        } else {
          stars[i].classList.remove("active");
        }
      }
      output.innerText = "Rating is: " + n + "/5";
    }

    // Modal functionality
    const modal1 = document.getElementById("feedbackModal");
    const modal2 = document.getElementById("resultModal");
    const span1 = document.getElementsByClassName("close")[0];
    const span2 = document.getElementsByClassName("close")[1];

    // Close the modal
    span1.onclick = function() {
      modal1.style.display = "none";
    }
    span2.onclick = function() {
      modal2.style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal1) {
        modal1.style.display = "none";
      }
      if (event.target == modal2) {
        modal2.style.display = "none";
      }
    }
  </script>

</body>

</html>