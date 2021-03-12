<?php

// Return JSON of possible start times from given room and date

// JSON output
header("Content-Type: application/json" );

// Load Private Classes
require '../private/autoload.php';

// Parse arguments
if(empty($_GET)) { exit(json_encode (json_decode ('{"Error": "No Data"}'))); }

$room_id = !(empty($_GET['room_id'])) ? (int) $_GET['room_id'] : false;
$reservation_date = !(empty($_GET['reservation_date'])) ? $_GET['reservation_date'] : false;
if(!($room_id && $reservation_date)) { exit(json_encode (json_decode ('{"Error": "Missing Data"}'))); }

// Get availabilites
// Date shoud be in Y-m-d
$api = new APIFetch('item', $room_id, $reservation_date);
$api_json = $api->getJSON();
$api_arry = json_decode($api_json, true);
$availability = $api_arry[0]['availability'];
$avails = start_times($availability);

// Make JSON and display.
print json_encode($avails);

// Functions

/**
 * Return available start times
 * @param $available [array]
 * @return [array] - possible start times
 */
function start_times($available){
  $avails = array();
  $previous_stop = '';
  foreach($available as $avail){
    if($previous_stop == $avail['from']){
      $avails[] = $avail['to'];
    }
    $previous_stop = $avail['to'];
  }
  return $avails;
}
