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

// Fetch all feedback data


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
        .min-w-full.bg-white.border {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            border: 1px solid #003366;
            /* Dark Blue Border */
        }

        .min-w-full.bg-white.border th,
        .min-w-full.bg-white.border td {
            border: 1px solid #003366;
            /* Dark Blue Border */
            padding: 8px;
        }

        .min-w-full.bg-white.border th {
            background-color: #003366;
            /* Dark Blue */
            color: white;
        }

        .min-w-full.bg-white.border tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .min-w-full.bg-white.border tr:hover {
            background-color: #ddd;
        }

        .min-w-full.bg-white.border td {
            color: #003366;
            /* Dark Blue for text */
        }

        .table-container {
            margin: 20px;
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <a href="app_feedback.php" class="bg-blue-500 text-white p-6 rounded-lg shadow-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-mobile-alt fa-3x mr-4"></i>
                            <span class="text-xl font-semibold">App Feedbacks</span>
                        </div>
                        <i class="fas fa-arrow-right fa-2x"></i>
                    </a>
                    <a href="./user_feedbacks.php" class="bg-blue-500 text-white p-6 rounded-lg shadow-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-users fa-3x mr-4"></i>
                            <span class="text-xl font-semibold">Area Feedbacks</span>
                        </div>
                        <i class="fas fa-arrow-right fa-2x"></i>
                    </a>
                </div>

                <!-- Feedback Table -->

            </div>
        </div>

</body>

</html>