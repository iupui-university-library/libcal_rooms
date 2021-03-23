# LibCal Room Reservation Application
This application uses the Springshare LibCal API for
making room reservations through simple and quick user interfaces.

# Dependencies

* Composer - to install php packages
* OAuth 2.0 Client
* Bootstrap
* Bootstrap datepicker

# API Classes

## APIFetch
Authenticates and makes request to LibCal to retrieve JSON data.

### Example
 Retrieve details and availability for a given room on a particular date:
```
$options = array('id' => 123, 'date' => '2021-04-01');
$room_api = new APIFetch('item', $options);
$room_json = $room_api->getJSON();
$room_array = json_decode($room_json, true, 256);
```

## APISend

Authenticate and post request to LibCal. Returns JSON reply.

### Example
```
$cancellation = new APISend('cancel');
$result = $cancellation->cancel('ab_123');
```

# API Functions
 Helper functions for gathering data to present on web pages.

 ### Example
Get a list of rooms
```
$rooms = array();
$cats = api_get_cats(); // Get all room categories
foreach($cats as $cat){
  $cat_rooms = api_get_rooms($cat['cid'], false); // Get rooms in category
  foreach($cat_rooms as $cat_room){
    array_push($rooms, $cat_room);
  }
}
```
