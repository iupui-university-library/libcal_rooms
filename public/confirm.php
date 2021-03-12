<?php

// Load application classes, configurations, variables
require __DIR__ . '/../private/autoload.php';

// Define CONSTANTS
define("TITLE", "Confirm Room");

// Authentication Code could go here.

$error = '';
if( !(empty($_GET)) &&
    !(empty($_GET['fname'])) &&
    !(empty($_GET['lname'])) &&
    !(empty($_GET['email'])) &&
    !(empty($_GET['room-id'])) &&
    !(empty($_GET['reservation-date'])) &&
    !(empty($_GET['start-time'])) &&
    !(empty($_GET['end-time']))
  ){
  $booking = array(
    'start'    => $_GET['start-time'],
    'fname'    => $_GET['fname'],
    'lname'    => $_GET['lname'],
    'email'    => $_GET['email'],
    'bookings' => array(array('id' => $_GET['room-id'], 'to' => $_GET['end-time'])),
  );
  // Add any additional required quesiton fields
  // Example - adding username
  list($username, $domain) = explode('@', $_GET['email'], 2);
  $booking[$app['qid_username']] = $username;

  $reservation = new APISend('reserve');
  $token = $reservation->token;
  $result = $reservation->reserve($booking);
  $confirmation = @$result->booking_id;
}
if(!$confirmation){
  $error_message = @$result->errors;
  $error .= $error_message ? print_r($error_message, true) . "<br/>" : "Sorry, something has gone wrong. \n";
  $error .= "Please try again.";
}

?>
<!DOCTYPE html>
<html lang="en-US">
<?php include "../templates/head.php" ?>

<body>
  <?php include "../templates/header.php" ?>
  <main>
    <?php if($confirmation): ?>
      <p>
        <?php echo $_GET['fname']; ?>, you room reservation has been booked! <br/>
        You confirmation nunmber is <b><?php echo $confirmation; ?></b>.
      </p>
    <?php else: ?>
      <p>
        <?php print $error; ?>
      </p>
      <pre>
        <?php print_r($booking); ?>
      </pre>
    <?php endif; ?>
  </main>
  <?php include "../templates/footer.php" ?>
</body>

</html>
