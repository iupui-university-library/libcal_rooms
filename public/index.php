<?php

// Load application classes, configurations, variables
require __DIR__ . '/../private/autoload.php';

// Define CONSTANTS
define("TITLE", "LibCal Room Reservation Examples");

// Authentication Code could go here.


?>
<!DOCTYPE html>
<html lang="en-US">
<?php include "../templates/head.php" ?>

<body>
  <?php include "../templates/header.php" ?>
  <main>
    <h2>Example Pages</h2>
    <nav class="nav flex-column">
      <a href="reserve.php" class="nav-link">
        Make a reservation
      </a>
      <a href="cancel.php" class="nav-link">
        Cancel a reservation
      </a>
    </nav>
  </main>
  <?php include "../templates/footer.php" ?>
</body>

</html>
