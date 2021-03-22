<?php

// Load application classes, configurations, variables
require __DIR__ . '/../private/autoload.php';

// Define CONSTANTS
define("TITLE", "Reserve Room");

// Authentication Code could go here.

// Get List of Rooms
$cats = api_get_cats(); // Get all room categories
$rooms = array();
foreach($cats as $cat){
  $cat_rooms = api_get_rooms($cat['cid'], false); // Get rooms in category
  foreach($cat_rooms as $cat_room){
    array_push($rooms, $cat_room);
  }
}


?>
<!DOCTYPE html>
<html lang="en-US">
<?php include "../templates/head.php" ?>

<body>
  <?php include "../templates/header.php" ?>
  <main>
    <h2>Make a reservation</h2>
    <form method="get" action="confirm.php">
      <div class="input-group mb-3">
        <label class="input-group-text" for="fname">First Name</label>
        <input class="form-control" type="text" id="fname" name="fname" >
      </div>
      <div class="input-group mb-3">
        <label class="input-group-text" for="lname">Last Name</label>
        <input class="form-control" type="text" id="lname" name="lname" >
      </div>
      <div class="input-group mb-3">
        <label class="input-group-text" for="email">Your Email</label>
        <input class="form-control" type="text" id="email" name="email" >
      </div>
      <div class="input-group mb-3">
        <label class="input-group-text" for="room-id">Pick Room</label>
        <select class="form-select" id="room-id" name="room-id" onchange="startTime();">
          <option disabled selected value> -- Select Room -- </option>
          <?php foreach($rooms as $room): ?>
            <option value="<?php echo $room['id']; ?>"><?php echo $room['name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="input-group date mb-3" data-provide="datepicker">
        <label class="input-group-text" for="room_id">Pick Date&nbsp;&nbsp;</label>
        <input type="text" class="form-control" name="reservation-date" onchange="startTime();">
        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
      <div id="start-time-div" class="input-group mb-3">
        <label class="input-group-text" for="start-time">Start Time</label>
        <select class="form-select" id="start-time" name="start-time" onchange="endTime(this, <?php echo $app['max']; ?>)" disabled>
          <option disabled selected value> -- Select Start Time -- </option>
        </select>
      </div>
      <div id="end-time-div" class="input-group mb-3">
        <label class="input-group-text" for="end-time">End Time&nbsp;&nbsp;</label>
        <select class="form-select" id="end-time" name="end-time" disabled onchange="$('#confirm-submit').prop('disabled', false);">
          <option disabled selected value> -- Select End Time -- </option>
        </select>
      </div>
      <input id="confirm-submit" type="submit" value="Confirm" disabled>
    </form>
    <nav class="nav">
      <a href="index.php" class="nav-link">More Examples</a>
    </nav>
  </main>
  <?php include "../templates/footer.php" ?>
</body>

</html>
