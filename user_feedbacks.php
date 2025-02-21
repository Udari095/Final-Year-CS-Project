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

$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_email = $_SESSION['email'];

$selected_city = isset($_POST['city']) ? $_POST['city'] : '';

// Modify the SQL query based on the selected city
$sql = "SELECT area_text, feedback_text, rating FROM feedback";
if ($selected_city && $selected_city != 'null') {
    $sql .= " WHERE area_text = '" . $conn->real_escape_string($selected_city) . "'";
}
$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);

$feedback_data = [];
while ($row = $result->fetch_assoc()) {
    $feedback_data[$row['area_text']][] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .feedback-card {
            background-size: cover;
            background-position: center;
        }

        .AluthkadeEast {
            background-image: url('cities/AluthkadeEast.jpg');
        }

        .AluthkadeWest {
            background-image: url('cities/AluthkadeWest.jpg');
        }

        .Bambalapitiya {
            background-image: url('cities/Bambalapitiya.jpg');
        }

        .Baththaramulla {
            background-image: url('cities/Baththaramulla.jpg');
        }

        .Bloemendhal {
            background-image: url('cities/Bloemendhal.jpg');
        }

        .BorellaNorth {
            background-image: url('cities/BorellaNorth.jpg');
        }

        .BorellaSouth {
            background-image: url('cities/BorellaSouth.jpg');
        }

        .ColomboFort {
            background-image: url('cities/ColomboFort.jpg');
        }

        .Dehiwala {
            background-image: url('cities/Dehiwala.jpg');
        }

        .Dematagoda {
            background-image: url('cities/Dematagoda.jpg');
        }

        .GrandpassNorth {
            background-image: url('cities/GrandpassNorth.jpg');
        }

        .GrandpassSouth {
            background-image: url('cities/GrandpassSouth.jpg');
        }

        .HavelockTown {
            background-image: url('cities/HavelockTown.jpg');
        }

        .Homagama {
            background-image: url('cities/Homagama.jpg');
        }

        .Kaduwela {
            background-image: url('cities/Kaduwela.jpg');
        }

        .Kalubovila {
            background-image: url('cities/Kalubovila.jpg');
        }

        .Kirulapone {
            background-image: url('cities/Kirulapone.jpg');
        }

        .Kohuwala {
            background-image: url('cities/Kohuwala.jpg');
        }

        .Kollupitiya {
            background-image: url('cities/Kollupitiya.jpg');
        }

        .Kolonnawa {
            background-image: url('cities/Kolonnawa.jpg');
        }

        .KotahenaEast {
            background-image: url('cities/KotahenaEast.jpg');
        }

        .KotahenaWest {
            background-image: url('cities/KotahenaWest.jpg');
        }

        .Kottawa {
            background-image: url('cities/Kottawa.jpg');
        }

        .Kurunduwatta {
            background-image: url('cities/Kurunduwatta.jpg');
        }

        .Madampitiya {
            background-image: url('cities/Madampitiya.jpg');
        }

        .Maharagama {
            background-image: url('cities/Maharagama.jpg');
        }

        .Malabe {
            background-image: url('cities/Malabe.jpg');
        }

        .MaligawattaEast {
            background-image: url('cities/MaligawattaEast.jpg');
        }

        .MaligawattaWest {
            background-image: url('cities/MaligawattaWest.jpg');
        }

        .Maradana {
            background-image: url('cities/Maradana.jpg');
        }

        .Mattakkuliya {
            background-image: url('cities/Mattakkuliya.jpg');
        }

        .Modara {
            background-image: url('cities/Modara.jpg');
        }

        .Moratuwa {
            background-image: url('cities/Moratuwa.jpg');
        }

        .MountLavinea {
            background-image: url('cities/MountLavinea.jpg');
        }

        .Narahenpita {
            background-image: url('cities/Narahenpita.jpg');
        }

        .Nawala {
            background-image: url('cities/Nawala.jpg');
        }

        .Nugegoda {
            background-image: url('cities/Nugegoda.jpg');
        }

        .Oruwala {
            background-image: url('cities/Oruwala.jpg');
        }

        .PamankadaEast {
            background-image: url('cities/PamankadaEast.jpg');
        }

        .PamankadaWest {
            background-image: url('cities/PamankadaWest.jpg');
        }

        .Panchikawatta {
            background-image: url('cities/Panchikawatta.jpg');
        }

        .Pannipitiya {
            background-image: url('cities/Pannipitiya.jpg');
        }

        .Pettah {
            background-image: url('cities/Pettah.jpg');
        }

        .Piliyandala {
            background-image: url('cities/Piliyandala.jpg');
        }

        .Rajagiriya {
            background-image: url('cities/Rajagiriya.jpg');
        }

        .Rathmalana {
            background-image: url('cities/Rathmalana.jpg');
        }

        .SlaveIsland {
            background-image: url('cities/SlaveIsland.jpg');
        }

        .Thalawathugoda {
            background-image: url('cities/Thalawathugoda.jpg');
        }

        .UnionPlace {
            background-image: url('cities/UnionPlace.jpg');
        }

        .Welikada {
            background-image: url('cities/Welikada.jpg');
        }

        .WellawattaNorth {
            background-image: url('cities/WellawattaNorth.jpg');
        }

        .WellawattaSouth {
            background-image: url('cities/WellawattaSouth.jpg');
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex flex-col min-h-screen">
        <?php include("./includes/navbar.php"); ?>

        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <div class="w-1/5 bg-green-800 text-white p-4">
                <?php include("./includes/sidebar.php"); ?>
            </div>

            <!-- Main Content -->
            <div class="flex-1 bg-gray-200 p-8">
                <h2 class="text-3xl font-semibold mb-6">View Feedback</h2>

                <!-- City Selection Form -->
                <form method="POST" action="">
                    <label for="city" class="block text-xl font-semibold mb-2">Select Area:</label>
                    <div class="flex items-center">
                        <select id="selLoc" name="city" class="controls w-4/5 p-2 border rounded mr-2" onchange="this.form.submit()">
                            <option value="null" <?php if ($selected_city == 'null') echo 'selected'; ?>>Select an area</option>
                            <option value="AluthkadeEast" <?php if ($selected_city == 'AluthkadeEast') echo 'selected'; ?>>Aluthkade East</option>
                            <option value="AluthkadeWest" <?php if ($selected_city == 'AluthkadeWest') echo 'selected'; ?>>Aluthkade West</option>
                            <option value="Bambalapitiya" <?php if ($selected_city == 'Bambalapitiya') echo 'selected'; ?>>Bambalapitiya</option>
                            <option value="Baththaramulla" <?php if ($selected_city == 'Baththaramulla') echo 'selected'; ?>>Baththaramulla</option>
                            <option value="Bloemendhal" <?php if ($selected_city == 'Bloemendhal') echo 'selected'; ?>>Bloemendhal</option>
                            <option value="BorellaNorth" <?php if ($selected_city == 'BorellaNorth') echo 'selected'; ?>>Borella North</option>
                            <option value="BorellaSouth" <?php if ($selected_city == 'BorellaSouth') echo 'selected'; ?>>Borella South</option>
                            <option value="ColomboFort" <?php if ($selected_city == 'ColomboFort') echo 'selected'; ?>>Colombo Fort</option>
                            <option value="Dehiwala" <?php if ($selected_city == 'Dehiwala') echo 'selected'; ?>>Dehiwala</option>
                            <option value="Dematagoda" <?php if ($selected_city == 'Dematagoda') echo 'selected'; ?>>Dematagoda</option>
                            <option value="GrandpassNorth" <?php if ($selected_city == 'GrandpassNorth') echo 'selected'; ?>>Grandpass North</option>
                            <option value="GrandpassSouth" <?php if ($selected_city == 'GrandpassSouth') echo 'selected'; ?>>Grandpass South</option>
                            <option value="HavelockTown" <?php if ($selected_city == 'HavelockTown') echo 'selected'; ?>>Havelock Town</option>
                            <option value="Homagama" <?php if ($selected_city == 'Homagama') echo 'selected'; ?>>Homagama</option>
                            <option value="Kaduwela" <?php if ($selected_city == 'Kaduwela') echo 'selected'; ?>>Kaduwela</option>
                            <option value="Kalubovila" <?php if ($selected_city == 'Kalubovila') echo 'selected'; ?>>Kalubovila</option>
                            <option value="Kirulapone" <?php if ($selected_city == 'Kirulapone') echo 'selected'; ?>>Kirulapone</option>
                            <option value="Kohuwala" <?php if ($selected_city == 'Kohuwala') echo 'selected'; ?>>Kohuwala</option>
                            <option value="Kollupitiya" <?php if ($selected_city == 'Kollupitiya') echo 'selected'; ?>>Kollupitiya</option>
                            <option value="Kolonnawa" <?php if ($selected_city == 'Kolonnawa') echo 'selected'; ?>>Kolonnawa</option>
                            <option value="KotahenaEast" <?php if ($selected_city == 'KotahenaEast') echo 'selected'; ?>>Kotahena East</option>
                            <option value="KotahenaWest" <?php if ($selected_city == 'KotahenaWest') echo 'selected'; ?>>Kotahena West</option>
                            <option value="Kottawa" <?php if ($selected_city == 'Kottawa') echo 'selected'; ?>>Kottawa</option>
                            <option value="Kurunduwatta" <?php if ($selected_city == 'Kurunduwatta') echo 'selected'; ?>>Kurunduwatta</option>
                            <option value="Madampitiya" <?php if ($selected_city == 'Madampitiya') echo 'selected'; ?>>Madampitiya</option>
                            <option value="Maharagama" <?php if ($selected_city == 'Maharagama') echo 'selected'; ?>>Maharagama</option>
                            <option value="Malabe" <?php if ($selected_city == 'Malabe') echo 'selected'; ?>>Malabe</option>
                            <option value="MaligawattaEast" <?php if ($selected_city == 'MaligawattaEast') echo 'selected'; ?>>Maligawatta East</option>
                            <option value="MaligawattaWest" <?php if ($selected_city == 'MaligawattaWest') echo 'selected'; ?>>Maligawatta West</option>
                            <option value="Maradana" <?php if ($selected_city == 'Maradana') echo 'selected'; ?>>Maradana</option>
                            <option value="Mattakkuliya" <?php if ($selected_city == 'Mattakkuliya') echo 'selected'; ?>>Mattakkuliya</option>
                            <option value="Modara" <?php if ($selected_city == 'Modara') echo 'selected'; ?>>Modara</option>
                            <option value="Moratuwa" <?php if ($selected_city == 'Moratuwa') echo 'selected'; ?>>Moratuwa</option>
                            <option value="MountLavinea" <?php if ($selected_city == 'MountLavinea') echo 'selected'; ?>>Mount Lavinea</option>
                            <option value="Narahenpita" <?php if ($selected_city == 'Narahenpita') echo 'selected'; ?>>Narahenpita</option>
                            <option value="Nawala" <?php if ($selected_city == 'Nawala') echo 'selected'; ?>>Nawala</option>
                            <option value="Nugegoda" <?php if ($selected_city == 'Nugegoda') echo 'selected'; ?>>Nugegoda</option>
                            <option value="Oruwala" <?php if ($selected_city == 'Oruwala') echo 'selected'; ?>>Oruwala</option>
                            <option value="PamankadaEast" <?php if ($selected_city == 'PamankadaEast') echo 'selected'; ?>>Pamankada East</option>
                            <option value="PamankadaWest" <?php if ($selected_city == 'PamankadaWest') echo 'selected'; ?>>Pamankada West</option>
                            <option value="Panchikawatta" <?php if ($selected_city == 'Panchikawatta') echo 'selected'; ?>>Panchikawatta</option>
                            <option value="Pannipitiya" <?php if ($selected_city == 'Pannipitiya') echo 'selected'; ?>>Pannipitiya</option>
                            <option value="Pettah" <?php if ($selected_city == 'Pettah') echo 'selected'; ?>>Pettah</option>
                            <option value="Piliyandala" <?php if ($selected_city == 'Piliyandala') echo 'selected'; ?>>Piliyandala</option>
                            <option value="Rajagiriya" <?php if ($selected_city == 'Rajagiriya') echo 'selected'; ?>>Rajagiriya</option>
                            <option value="Rathmalana" <?php if ($selected_city == 'Rathmalana') echo 'selected'; ?>>Rathmalana</option>
                            <option value="SlaveIsland" <?php if ($selected_city == 'SlaveIsland') echo 'selected'; ?>>Slave Island</option>
                            <option value="Thalawathugoda" <?php if ($selected_city == 'Thalawathugoda') echo 'selected'; ?>>Thalawathugoda</option>
                            <option value="UnionPlace" <?php if ($selected_city == 'UnionPlace') echo 'selected'; ?>>Union Place</option>
                            <option value="Welikada" <?php if ($selected_city == 'Welikada') echo 'selected'; ?>>Welikada</option>
                            <option value="WellawattaNorth" <?php if ($selected_city == 'WellawattaNorth') echo 'selected'; ?>>Wellawatta North</option>
                            <option value="WellawattaSouth" <?php if ($selected_city == 'WellawattaSouth') echo 'selected'; ?>>Wellawatta South</option>
                        </select>
                        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded" onclick="resetDropdown()">Reset</button>
                    </div>
                </form>

                <script>
                    function resetDropdown() {
                        document.getElementById('selLoc').value = 'null';
                        document.forms[0].submit();
                    }
                </script>

                <!-- Feedback Cards -->
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php
                    foreach ($feedback_data as $city => $feedbacks) {
                        $total_rating = 0;
                        $feedback_count = count($feedbacks);

                        foreach ($feedbacks as $feedback) {
                            $total_rating += $feedback['rating'];
                        }

                        $average_rating = $feedback_count > 0 ? $total_rating / $feedback_count : 0;


                        $city = slugToTitleCase($city);

                        // Hardcode the image paths for each city
                        switch ($city) {
                            case 'AluthkadeEast':
                                $city_image_path = 'cities/AluthkadeEast.jpg';
                                break;
                            case 'AluthkadeWest':
                                $city_image_path = 'cities/AluthkadeWest.jpg';
                                break;
                            case 'Bambalapitiya':
                                $city_image_path = 'cities/Bambalapitiya.jpg';
                                break;
                            case 'Baththaramulla':
                                $city_image_path = 'cities/Baththaramulla.jpg';
                                break;
                            case 'Bloemendhal':
                                $city_image_path = 'cities/Bloemendhal.jpg';
                                break;
                            case 'BorellaNorth':
                                $city_image_path = 'cities/BorellaNorth.jpg';
                                break;
                            case 'BorellaSouth':
                                $city_image_path = 'cities/BorellaSouth.jpg';
                                break;
                            case 'ColomboFort':
                                $city_image_path = 'cities/ColomboFort.jpg';
                                break;
                            case 'Dehiwala':
                                $city_image_path = 'cities/Dehiwala.jpg';
                                break;
                            case 'Dematagoda':
                                $city_image_path = 'cities/Dematagoda.jpg';
                                break;
                            case 'GrandpassNorth':
                                $city_image_path = 'cities/GrandpassNorth.jpg';
                                break;
                            case 'GrandpassSouth':
                                $city_image_path = 'cities/GrandpassSouth.jpg';
                                break;
                            case 'HavelockTown':
                                $city_image_path = 'cities/HavelockTown.jpg';
                                break;
                            case 'Homagama':
                                $city_image_path = 'cities/Homagama.jpg';
                                break;
                            case 'Kaduwela':
                                $city_image_path = 'cities/Kaduwela.jpg';
                                break;
                            case 'Kalubovila':
                                $city_image_path = 'cities/Kalubovila.jpg';
                                break;
                            case 'Kirulapone':
                                $city_image_path = 'cities/Kirulapone.jpg';
                                break;
                            case 'Kohuwala':
                                $city_image_path = 'cities/Kohuwala.jpg';
                                break;
                            case 'Kollupitiya':
                                $city_image_path = 'cities/Kollupitiya.jpg';
                                break;
                            case 'Kolonnawa':
                                $city_image_path = 'cities/Kolonnawa.jpg';
                                break;
                            case 'KotahenaEast':
                                $city_image_path = 'cities/KotahenaEast.jpg';
                                break;
                            case 'KotahenaWest':
                                $city_image_path = 'cities/KotahenaWest.jpg';
                                break;
                            case 'Kottawa':
                                $city_image_path = 'cities/Kottawa.jpg';
                                break;
                            case 'Kurunduwatta':
                                $city_image_path = 'cities/Kurunduwatta.jpg';
                                break;
                            case 'Madampitiya':
                                $city_image_path = 'cities/Madampitiya.jpg';
                                break;
                            case 'Maharagama':
                                $city_image_path = 'cities/Maharagama.jpg';
                                break;
                            case 'Malabe':
                                $city_image_path = 'cities/Malabe.jpg';
                                break;
                            case 'MaligawattaEast':
                                $city_image_path = 'cities/MaligawattaEast.jpg';
                                break;
                            case 'MaligawattaWest':
                                $city_image_path = 'cities/MaligawattaWest.jpg';
                                break;
                            case 'Maradana':
                                $city_image_path = 'cities/Maradana.jpg';
                                break;
                            case 'Mattakkuliya':
                                $city_image_path = 'cities/Mattakkuliya.jpg';
                                break;
                            case 'Modara':
                                $city_image_path = 'cities/Modara.jpg';
                                break;
                            case 'Moratuwa':
                                $city_image_path = 'cities/Moratuwa.jpg';
                                break;
                            case 'MountLavinea':
                                $city_image_path = 'cities/MountLavinea.jpg';
                                break;
                            case 'Narahenpita':
                                $city_image_path = 'cities/Narahenpita.jpg';
                                break;
                            case 'Nawala':
                                $city_image_path = 'cities/Nawala.jpg';
                                break;
                            case 'Nugegoda':
                                $city_image_path = 'cities/Nugegoda.jpg';
                                break;
                            case 'Oruwala':
                                $city_image_path = 'cities/Oruwala.jpg';
                                break;
                            case 'PamankadaEast':
                                $city_image_path = 'cities/PamankadaEast.jpg';
                                break;
                            case 'PamankadaWest':
                                $city_image_path = 'cities/PamankadaWest.jpg';
                                break;
                            case 'Panchikawatta':
                                $city_image_path = 'cities/Panchikawatta.jpg';
                                break;
                            case 'Pannipitiya':
                                $city_image_path = 'cities/Pannipitiya.jpg';
                                break;
                            case 'Pettah':
                                $city_image_path = 'cities/Pettah.jpg';
                                break;
                            case 'Piliyandala':
                                $city_image_path = 'cities/Piliyandala.jpg';
                                break;
                            case 'Rajagiriya':
                                $city_image_path = 'cities/Rajagiriya.jpg';
                                break;
                            case 'Rathmalana':
                                $city_image_path = 'cities/Rathmalana.jpg';
                                break;
                            case 'SlaveIsland':
                                $city_image_path = 'cities/SlaveIsland.jpg';
                                break;
                            case 'Thalawathugoda':
                                $city_image_path = 'cities/Thalawathugoda.jpg';
                                break;
                            case 'UnionPlace':
                                $city_image_path = 'cities/UnionPlace.jpg';
                                break;
                            case 'Welikada':
                                $city_image_path = 'cities/Welikada.jpg';
                                break;
                            case 'WellawattaNorth':
                                $city_image_path = 'cities/WellawattaNorth.jpg';
                                break;
                            case 'WellawattaSouth':
                                $city_image_path = 'cities/WellawattaSouth.jpg';
                                break;

                            default:
                                $city_image_path = 'path/to/default/image.jpg';
                                break;
                        }

                        $rating_text = '';
                        if ($average_rating >= 1 && $average_rating <= 2) {
                            $rating_text = 'Low Rating';
                        } elseif ($average_rating >= 3 && $average_rating <= 4) {
                            $rating_text = 'Average Rating';
                        } elseif ($average_rating >= 4 && $average_rating <= 5) {
                            $rating_text = 'Best Rating';
                        }
                    ?>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center mb-4">
                                <img src="<?php echo $city_image_path; ?>" alt="<?php echo htmlspecialchars($city); ?> Image" class="w-24 h-24 rounded-full mr-4">
                                <div>
                                    <h3 class="text-2xl font-semibold text-gray-700"><?php echo slugToNormalString(htmlspecialchars($city)); ?></h3>
                                    <div class="flex items-center">
                                        <?php for ($i = 0; $i < round($average_rating); $i++) { ?>
                                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.916c.969 0 1.371 1.24.588 1.81l-3.973 2.878a1 1 0 00-.364 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.973-2.878a1 1 0 00-1.176 0l-3.973 2.878c-.784.57-1.84-.197-1.54-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.075 9.101c-.784-.57-.38-1.81.588-1.81h4.916a1 1 0 00.95-.69l1.518-4.674z" />
                                            </svg>
                                        <?php } ?>
                                    </div>
                                    <p class="text-gray-500"><?php echo $rating_text; ?></p>
                                    <p class=" text-xs text-gray-500">Average Rating Score: <?php echo round($average_rating, 2); ?>/5</p>
                                </div>
                            </div>

                            <?php if ($selected_city != 'null') { ?>
                                <?php foreach ($feedbacks as $feedback) { ?>
                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <?php for ($i = 0; $i < round($average_rating); $i++) { ?>
                                            <?php } ?>
                                        </div>
                                        <p><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
<script>
    function resetDropdown() {
        document.getElementById('selLoc').selectedIndex = 0;
        document.forms[0].submit();
    }