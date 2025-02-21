<?php

$DBservername = "localhost";
$DBusername = "root";
$DBpassword = "";
$DBname = "web3";


$APPURI = "http://localhost/project/";

$EmailUser = "rajapakshapwr@gmail.com";
$EmailPassword = "nqgl zafh tsyx auki";


$mapApiKey = "AIzaSyBfq-24GiH2JTdw7nmaVzJMnyAKo_EyGVM";


$locations = [
    'aluthkade_east' => ["lat" => 6.9406, "lng" => 79.8536],
    'aluthkade_west' => ["lat" => 6.9373, "lng" => 79.8545],
    'bambalapitiya' => ["lat" => 6.8881, "lng" => 79.8534],
    'baththaramulla' => ["lat" => 6.9276, "lng" => 79.9635],
    'bloemendhal' => ["lat" => 6.9590, "lng" => 79.8680],
    'borella_north' => ["lat" => 6.9274, "lng" => 79.8732],
    'borella_south' => ["lat" => 6.9244, "lng" => 79.8742],
    'fort' => ["lat" => 6.9344, "lng" => 79.8438],
    'dehiwala' => ["lat" => 6.8549, "lng" => 79.8650],
    'dematagoda' => ["lat" => 6.9364, "lng" => 79.8786],
    'grandpass_north' => ["lat" => 6.9551, "lng" => 79.8745],
    'grandpass_south' => ["lat" => 6.9492, "lng" => 79.8740],
    'havelock_town' => ["lat" => 6.8888, "lng" => 79.8652],
    'homagama' => ["lat" => 6.8424, "lng" => 80.0026],
    'kaduwela' => ["lat" => 6.9308, "lng" => 79.9691],
    'kalubovila' => ["lat" => 6.8522, "lng" => 79.8666],
    'kirulapone' => ["lat" => 6.8909, "lng" => 79.8703],
    'kohuwala' => ["lat" => 6.8608, "lng" => 79.8686],
    'kollupitiya' => ["lat" => 6.9204, "lng" => 79.8470],
    'kolonnawa' => ["lat" => 6.9305, "lng" => 79.8908],
    'kotahena_east' => ["lat" => 6.9422, "lng" => 79.8622],
    'kotahena_west' => ["lat" => 6.9435, "lng" => 79.8600],
    'kottawa' => ["lat" => 6.8406, "lng" => 79.9650],
    'kurunduwatta' => ["lat" => 6.9261, "lng" => 79.8618],
    'madampitiya' => ["lat" => 6.9600, "lng" => 79.8755],
    'maharagama' => ["lat" => 6.8463, "lng" => 79.9276],
    'malabe' => ["lat" => 6.9148, "lng" => 79.9578],
    'maligawatta_east' => ["lat" => 6.9326, "lng" => 79.8731],
    'maligawatta_west' => ["lat" => 6.9325, "lng" => 79.8699],
    'maradana' => ["lat" => 6.9286, "lng" => 79.8691],
    'mattakkuliya' => ["lat" => 6.9680, "lng" => 79.8782],
    'modara' => ["lat" => 6.9644, "lng" => 79.8657],
    'mount_lavinia' => ["lat" => 6.8333, "lng" => 79.8643],
    'narahenpita' => ["lat" => 6.8895, "lng" => 79.8723],
    'nawala' => ["lat" => 6.9039, "lng" => 79.8820],
    'nugegoda' => ["lat" => 6.8649, "lng" => 79.9016],
    'oruwala' => ["lat" => 6.9105, "lng" => 79.9815],
    'pamankada_east' => ["lat" => 6.8813, "lng" => 79.8707],
    'pamankada_west' => ["lat" => 6.8812, "lng" => 79.8671],
    'panchikawatta' => ["lat" => 6.9266, "lng" => 79.8706],
    'pettah' => ["lat" => 6.9375, "lng" => 79.8489],
    'rajagiriya' => ["lat" => 6.9124, "lng" => 79.8917],
    'rathmalana' => ["lat" => 6.8274, "lng" => 79.8718],
    'slave_island' => ["lat" => 6.9242, "lng" => 79.8506],
    'thalawathugoda' => ["lat" => 6.8730, "lng" => 79.9480],
    'walikada' => ["lat" => 6.9120, "lng" => 79.8921],
    'wellawatta_north' => ["lat" => 6.8721, "lng" => 79.8613],
    'wellawatta_south' => ["lat" => 6.8722, "lng" => 79.8581]
];


function slugToNormalString($slug) {
    // Replace underscores with spaces
    $string = str_replace('_', ' ', $slug);
    // Capitalize the first letter of each word
    return ucwords($string);
}


function slugToTitleCase($slug) {

    $string = str_replace('_', ' ', $slug);

        // Capitalize the first letter of each word
        $string = ucwords(strtolower($string));


    // Replace underscores with ""
    $string = str_replace(' ', '', $string);


    return $string;

}