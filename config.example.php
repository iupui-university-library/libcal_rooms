<?php

return (object) array(
  // Application Info
  'app' => array(
    'base_url'      => 'http://localhost/libcal_rooms/public',
    'locid'         => 'xxxx',
    'hoursid'       => 'yyyy', // building Location for hours
    'qid_username'  => 'qxxxx', // optional field, may not be needed.
    'debug'         => false,
    'timezone'      => 'America/New_York',
    'max'           => 4, // Maximium hours room can be reserved.
  ),
  // API Info
  'api' => array(
    'api_base'      => 'university.libcal.com/1.1/',
    'api_url'       => 'https://university.libcal.com/1.1/',
    'api_oauth'     => 'https://university.libcal.com/1.1/oauth',
    'client_id'     => 'xxxxxx',
    'client_secret' => 'xxxxxx',
  ),
);

?>
