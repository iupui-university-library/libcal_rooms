<?php

// Load application classes, configurations, variables
require __DIR__ . '/../private/autoload.php';

// Define CONSTANTS
define("TITLE", "Cancel Reservation");

// Authentication Code could go here.

// Check for canel request
$cancel_id = !empty($_GET) && isset($_GET['booking_id']) ? $_GET['booking_id'] : false;
$error = false;
$result = '';
if($cancel_id){
  $cancellation = new APISend('cancel');
  $result = $cancellation->cancel($cancel_id)[0];
  if(isset($result->error)){
    $error = $result->error;
  }
}

// Get list of reservations
$bookings = api_get_bookings();

// Need to filter and sort reservations
// For example, confirmed only from newest to oldest.

?>
<!DOCTYPE html>
<html lang="en-US">
<?php include "../templates/head.php" ?>

<body>
  <?php include "../templates/header.php" ?>
  <main>
    <?php if($error): ?>
      <div class="alert alert-warning" role="alert">
        Error: <?php echo $error; ?>
      </div>
    <?php elseif($cancel_id): ?>
      <div class="alert alert-primary" role="alert">
        Reservation <?php print $cancel_id; ?> cancelled.
      </div>
    <?php endif; ?>
    <h2>Cancel a reservation</h2>
    <form action="cancel.php" method="get">
      <select name="booking_id">
      <?php foreach($bookings as $booking): ?>
        <option value="<?php echo $booking['bookId']; ?>">
          <?php echo $booking['bookId']; ?>
        </option>
      <?php endforeach; ?>
      </select>
      <input type="submit" value="Cancel" />
    </form>
    <nav class="nav">
      <a href="index.php" class="nav-link">More Examples</a>
    </nav>
  </main>
  <?php include "../templates/footer.php" ?>
</body>

</html>
