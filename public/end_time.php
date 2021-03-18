<?php

// Return JSON of possible end times from given
// Room, date, start time and max reservation.

// Force JSON output
header("Content-Type: application/json" );

// Load Private Classes
require '../private/autoload.php';

// Parse arguments
if(empty($_GET)) { return json_encode (json_decode ("{}")); }

$id = !(empty($_GET['room_id'])) ? (int) $_GET['room_id'] : false;
$start = !(empty($_GET['start_time'])) ? force_time($_GET['start_time']) : false;
if(!($id && $start)) { return json_encode (json_decode ("{}")); }

$max = !(empty($_GET['max'])) ? (int) $_GET['max'] : 24;

// Get availabilites
$date = date("Y-m-d", strtotime($start));
$options = array('id' => $id, 'date' => $date);
$api = new APIFetch('item', $options);
$api_json = $api->getJSON();
$api_arry = json_decode($api_json, true);
$availability = $api_arry[0]['availability'];
$max_time = date("c", strtotime("$start + $max hours"));
$avails = stop_times($availability, $start, $max_time);

// Make JSON and display.
print json_encode($avails);

// Functions

/**
 * Insure given start time is a valid time
 * @param $raw_time [timestamp] - time given in URL argument
 * @return [timestamp] - properly formatted start time
 */
function force_time($raw_time){
  return $raw_time;
  return date("c", strtotime($raw_time));
}

/**
 * Return available end times
 * @param $available, $start, $max [array, ISO date, ISO date] - available times, start time, max hours.
 * @return [array] - possible stop times
 */
function stop_times($available, $start_time, $max_time){
  $avails = array();
  $previous_stop = '';
  foreach($available as $avail){
    if($avail['from'] > $start_time){
      if($avail['from'] < $max_time && $previous_stop == $avail['from']){
        $avails[] = $avail['to'];
        $previous_stop = $avail['to'];
      }else{
        return $avails;
      }
    } else {
      $previous_stop = $avail['to'];
    }
  }
  return $avails;
}
