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

$message = "";

// Create connection
$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['location_text']) && isset($_GET['location_coordinates'])) {
    $location_text = $conn->real_escape_string($_GET['location_text'] ?? '');
    $location_coordinates = $conn->real_escape_string($_GET['location_coordinates'] ?? '');
    $current_email = $_SESSION['email'];

    if (!empty($location_text) && !empty($location_coordinates)) {
        $sql = "INSERT INTO saved_locations (email, location_text, location_coordinates) VALUES ('$current_email', '$location_text', '$location_coordinates')";
        if ($conn->query($sql) === TRUE) {
            $message = "Location saved successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Both location text and coordinates are required!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_text'])) {
    $feedback_text = $conn->real_escape_string($_POST['feedback_text'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $current_email = $_SESSION['email'];

    if (!empty($feedback_text) && $rating > 0) {
        $sql = "INSERT INTO feedback (email, feedback_text, rating) VALUES ('$current_email', '$feedback_text', '$rating')";
        if ($conn->query($sql) === TRUE) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Feedback and rating are required!";
    }
}

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

            <div class="w-1/5 bg-green-800 text-white p-4">
                <?php include("./includes/sidebar.php"); ?>
            </div>

            <!-- Main Content -->
            <div class="flex-1 bg-white p-8">
                <h2 class="text-3xl font-semibold mb-6">View on Map</h2>

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
                        <button onclick="saveLocation()" class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Save Location</button>
                        <button onclick="process()" class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Process</button>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="mb-4 flex gap-4">
                    <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="walking_paths">Walking Paths</button>
                    <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="play_areas">Play Areas</button>
                    <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="green_parks">Green Parks</button>
                    <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="water_features">Water features</button>
                    <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="natural_trails">Natural trails</button>
                </div>
                <div class="mb-4 flex gap-4">


                    <a href="spec.php?spec=NDVI&location=<?php if (isset($_GET['location'])) {
                                                                            echo htmlspecialchars($_GET['location']);
                                                                        }  ?>&location_coordinates=<?php if (isset($_GET['location_coordinates'])) {
                                                                                                        echo htmlspecialchars($_GET['location_coordinates']);
                                                                                                    }  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="walking_paths">Vegetation cover</a>
                    <a href="spec.php?spec=AQI&location=<?php if (isset($_GET['location'])) {
                                                                    echo htmlspecialchars($_GET['location']);
                                                                }  ?>&location_coordinates=<?php if (isset($_GET['location_coordinates'])) {
                                                                                                echo htmlspecialchars($_GET['location_coordinates']);
                                                                                            }  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="play_areas">Air quaility</a>
                    <a href="spec.php?spec=Temperature&location=<?php if (isset($_GET['location'])) {
                                                                    echo htmlspecialchars($_GET['location']);
                                                                }  ?>&location_coordinates=<?php if (isset($_GET['location_coordinates'])) {
                                                                                                echo htmlspecialchars($_GET['location_coordinates']);
                                                                                            }  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="green_parks">Temperature</a>
                    <a href="spec.php?spec=Population&location=<?php if (isset($_GET['location'])) {
                                                                    echo htmlspecialchars($_GET['location']);
                                                                }  ?>&location_coordinates=<?php if (isset($_GET['location_coordinates'])) {
                                                                                                echo htmlspecialchars($_GET['location_coordinates']);
                                                                                            }  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="water_features">Population</a>
                </div>

                <!-- Display Message -->
                <?php if ($message): ?>
                    <div class="p-4 mb-4 text-white bg-<?php echo ($message == "Location saved successfully!") ? 'green' : 'red'; ?>-500 rounded">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Map Container -->
                <div class="relative w-full h-screen">
                    <div id="map" class="w-full h-full"></div>
                    <div id="top-layer" class="absolute inset-0 w-full flex items-center justify-center  opacity-50  pointer-events-none">
                    </div>
                </div>

                <!-- Form for Saving Location -->
                <form id="save-location-form" method="POST" action="map.php" class="hidden">
                    <input type="hidden" id="location-text" name="location_text">
                    <input type="hidden" id="location-coordinates" name="location_coordinates">
                </form>
            </div>
        </div>

        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $mapApiKey; ?>&libraries=places&callback=initMap" async defer></script>
        <script>
            var locations = <?php echo json_encode($locations); ?>;
            var map;
            var mapCenter;


            // Function to generate random number between min and max
            function getRandomNumber(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            function getRandomColor() {
                const colors = ["lightgreen", "darkgreen", "red", "yellow", "orange"];
                const randomIndex = Math.floor(Math.random() * colors.length);
                return colors[randomIndex];
            }


            function saveLocation() {
                const loc = $('#selLoc').val();

                var selectedCoords = locations[loc];
                var newUrl = 'map.php?location_text=' + loc + '&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
                window.location.href = newUrl;
            }

            function getQueryParam(param) {
                let params = new URLSearchParams(window.location.search);
                return params.get(param);
            }

            function process() {

                var loc = getQueryParam('location');


                if(loc){
                    $.get('<?php echo $APPURI; ?>prediction/?indicator=true&location=' + loc, function(data) {
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
        
        marker.addListener('click', function() {
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
        });
      });
                }else{
                // Add markers with hover functionality
                for (let key in locations) {

                    $.get('<?php echo $APPURI; ?>prediction/?indicator=true&location=' + key, function(data) {
                        // Set the fetched content as the modal content

                        const jsonData = JSON.parse(data);


                        // Define a custom SVG icon
                        var customIcon = {
                            path: google.maps.SymbolPath.CIRCLE, // Use a circle
                            fillColor: jsonData.color,
                            fillOpacity: 1, // Full opacity
                            strokeWeight: 1, // Border thickness
                            strokeColor: "#FFFFFF", // White border
                            scale: 10, // Size of the circle
                        };

                        var marker = new google.maps.Marker({
                            position: locations[key],
                            map: map,
                            title: "city -> " + key + " Score -> " + jsonData.score,
                            icon: customIcon
                        });

                        marker.addListener('click', function() {
                            // Create an iframe element
                            var iframe = $('<iframe>', {
                                src: '<?php echo $APPURI; ?>prediction/?location=' + key,
                                width: '100%',
                                height: '400px', // Adjust the height as needed
                                frameborder: '0'
                            });

                            // Clear any previous content in the modal and append the iframe
                            $('#resultModal #result-section').html(iframe);

                            // Show the modal
                            $('#resultModal').show();
                        });
                    });
                }}
            }

            function updateLocation(selectedLocation) {



                if (selectedLocation !== 'null') {
                    var selectedCoords = locations[selectedLocation];
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?location=' + selectedLocation + '&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
                    window.location.href = newUrl;
                }
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


                // Filter places based on selected type
                document.querySelectorAll('.filter-button').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var type = this.getAttribute('data-type');
                        filterPlaces(type);
                    });
                });

                var markers = [];


                function filterPlaces(type) {
                    // Clear out the old markers.
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [];

                    // Define place types and their corresponding keywords
                    var placeTypes = {
                        'walking_paths': 'walking path',
                        'play_areas': 'play area',
                        'green_parks': 'green park',
                        'water_features': 'water feature',
                        'natural_trails': 'natural trail'
                    };

                    var service = new google.maps.places.PlacesService(map);
                    service.textSearch({
                        location: defaultLocation,
                        radius: 5000,
                        query: placeTypes[type]
                    }, function(results, status) {
                        if (status === google.maps.places.PlacesServiceStatus.OK) {
                            var bounds = new google.maps.LatLngBounds();
                            results.forEach(function(place) {
                                if (!place.geometry || !place.geometry.location) {
                                    console.log("Returned place contains no geometry");
                                    return;
                                }

                                var marker = new google.maps.Marker({
                                    map: map,
                                    title: place.name,
                                    position: place.geometry.location
                                });
                                markers.push(marker);

                                if (place.geometry.viewport) {
                                    bounds.union(place.geometry.viewport);
                                } else {
                                    bounds.extend(place.geometry.location);
                                }
                            });
                            map.fitBounds(bounds);
                        }
                    });
                }
            }

            // Initialize the map
            google.maps.event.addDomListener(window, 'load', initMap);
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