<?php

/** Helper functions for retreving API results **/

/**
 * Retrieve building hours for given date (c)
 * @param $date (string) = date in 'c' format
 * @return [array] - API results
 */
function api_get_hours($date) {
  global $app;
  $options = array('id' => $app['hoursid'], 'date' => $date);
  $hours_api = new APIFetch('hours', $options);
  $hours_json = $hours_api->getJSON();
  $hours_array = json_decode($hours_json, true);
  return $hours_array[0]['dates'];
}

/**
 * Retrive rooms
 * @param $cid [integer] - location type id
 * @param $date [string] - date in 'Y-m-d' format
 * @return [array] - API results as array
 */
function api_get_rooms($cid, $date) {
  $options = array('id' => $cid, 'date' => $date);
  $rooms_api = new APIFetch('cat', $options);
  $rooms_json = $rooms_api->getJSON();
  $rooms_array = json_decode($rooms_json, true, 256);
  return $rooms_array[0]['items'];
}

/**
 * Retrive room
 * @param $id [integer] - room id
 * @param $date [string] - date in 'Y-m-d' format
 * @return [array] - API results as array
 */
function api_get_room($id, $date = false) {
  $options = array('id' => $id, 'date' => $date);
  $room_api = new APIFetch('item', $options);
  $room_json = $room_api->getJSON();
  $room_array = json_decode($room_json, true, 256);
  return $room_array[0];
}

/**
 * Get categories
 * @return [array] - API results as array
 */
function api_get_cats() {
  global $app;
  $cat_api = new APIFetch('cats', array('id' => $app['locid']));
  $cat_json = $cat_api->getJSON();
  $cat_arry = json_decode($cat_json, true);
  return $cat_arry[0]['categories'];
}

/**
 * Get bookings
 * @return [array] - API results as array
 */
function api_get_bookings() {
  global $app;
  $bookings_api = new APIFetch('bookings', array('id' => $app['locid']));
  $bookings_json = $bookings_api->getJSON();
  $bookings_arry = json_decode($bookings_json, true);
  return $bookings_arry;
}
?>
