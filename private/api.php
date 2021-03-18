<?php

/**
 * API is a class for working with LibCal API
 *
 * API is used to get tokens from LibCal
 * and is the parent class for other LibCal API classes.
 */
class API
{
  /**
   * @vars
   */
  public $config;
  public $api;
  public $app;

  /**
   * Construct function
   *
   * Uses configuration file to setup properties
   */
  public function __construct()
  {
    $config = include(__DIR__ . '/../config.php');
    $this->config = $config;
    $this->api = $config->api;
    $this->app = $config->app;
  }

  /**
   * Gets LibCal API authenticaiton token
   * Requires oauth-client library
   * @return [string] - token value
   */
  public function getToken()
  {
    // Get authentication token (using oauth-client library)
    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => $this->api['client_id'],    // The client ID assigned to you by the provider
        'clientSecret'            => $this->api['client_secret'],    // The client password assigned to you by the provider
        //'redirectUri'             => 'http://localhost/room_bloom/',
        'urlAuthorize'            => $this->api['api_oauth'] . 'authorize',
        'urlAccessToken'          => $this->api['api_oauth'] . 'token',
        'urlResourceOwnerDetails' => $this->api['api_oauth'] . 'resource',
    ]);

    try {
        // Try to get an access token using the client credentials grant.
        $accessToken = $provider->getAccessToken('client_credentials');
        return $accessToken;
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        // Failed to get the access token
        exit($e->getMessage());
    }
    return false;
  }

}

/**
 * APIFetch class is for fetching JSON from LibCal API
 *
 * APIFetch has a limited set of actions:
 * spaces, cats, cat, item, bookings
 *
 * Exampe usage:
 * $api = new APIFetch($action, $options);
 * print $api->getJSON();
 *
 */
class APIFetch extends API
{
  /**
   * @vars
   */
  public $action;
  public $id;
  public $date;
  public $category;
  public $email;
  public $token;
  public $url;

  /**
   * Construct function
   * @param $action [String],
   * @param $options [Array] - token, id, date, category, email
   */
  public function __construct($action, $options = array())
  {
    parent::__construct();
    $actions = array('spaces', 'cats', 'cat', 'item', 'booking', 'bookings', 'room_bookings', 'hours', 'events', 'event');
    $this->action = in_array($action, $actions) ? $action : false;
    // Set options
    $this->token = isset($options['token']) ? $options['token'] : $this->getToken();
    $this->id = isset($options['id']) ? $options['id'] : false;
    $this->date = isset($options['date']) ? $options['date'] : false;
    $this->category = isset($options['category']) ? $options['category'] : false;
    $this->email = isset($options['email']) ? $options['email'] : false;
    // API URL
    $this->url = $this->getURL();
  }

  /**
   * Used token and URL propertied to retrieve information from LibCal
   * @return [json] - API response
   */
  public function getJSON()
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = array();
    $headers[] = "Authorization: Bearer " . $this->token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    return $result;
  }

  /**
   * Sets up URL from class properties.
   * @return [string] - URL for for API request.
   */
  public function getURL()
  {
    $url = $this->api['api_url'];
    $function = '';
    switch ($this->action) {
      case 'spaces':
        $function = 'space/locations/';
        break;
      case 'cats':
        $function = 'space/categories/' . $this->id;
        break;
      case 'cat':
        $function  = 'space/category/' . $this->id;
        $function .= '?details=1';
        if($this->date) $function .= "&availability=" . $this->date;
        break;
      case 'item':
        $function = 'space/item/' . $this->id;
        if($this->date) $function .= "?availability=" . $this->date;
        break;
      case 'booking':
        $function = 'space/booking/' . $this->id . "?formAnswers=1";
        break;
      case 'bookings':
        $app = $this->app;
        $lid = $app['locid'];
        $function = 'space/bookings?formAnswers=1&limit=100&lid=' . $lid;
        if($this->date) $function .= "&date=" . $this->date;
        $function .= "&days=100";
        if($this->email) $function .= "&email=" . $this->email;
        break;
      case 'room_bookings':
        $function = 'space/bookings?formAnswers=1&limit=100&eid=' . $this->id;
        if($this->date) $function .= "&date=" . $this->date;
        break;
      case 'hours':
        $url = $this->api['api2_url'];
        $function = 'hours/' . $this->id;
        if($this->date) {
          $to_date = date('Y-m-d', strtotime($this->date . ' +100 days'));
          $function .= "?&from=" . $this->date . "&to=" . $to_date;
        }
        break;
      case 'events':
        $function = 'events?cal_id=' . $this->id;
        if($this->category) $function .= "&category=" . $this->category;
        if($this->date) $function .= "&date=" . $this->date;
        $function .= "&days=365";
        break;
      case 'event':
        $function = 'events/' . $this->id;
        break;
      default:
        $function = 'space/locations/';
    }
    $this->url = $url . $function;
    return $this->url;
  }
}

/**
 * APISend class is for sending post request to LibCal
 *
 * APISend can make a booking or cancellation.
 *
 * Example Usage:
 * if($reservation_id){
 *   $cancellation = new APISend('cancel');
 *   $result = $cancellation->cancel($reservation_id);
 * }
 */
class APISend extends API
{
  /**
   * @vars
   */
  public $action;
  public $url;
  public $token;

  /**
   * Construct function
   * @param $action [string] - should match one of the actions listed above
   */
  public function __construct($action, $token = false)
  {
    parent::__construct();
    $actions = array('reserve', 'cancel');
    $this->action = in_array($action, $actions) ? $action : false;
    if($this->action == 'reserve'){
      $this->url = $this->api['api_url'] . 'space/reserve';
    }elseif($this->action == 'cancel'){
      $this->url = $this->api['api_url'] . 'space/cancel';
    }else{
      $this->url = false;
    }
    $this->token = ($token) ? $token : $this->getToken();
  }

  /**
   * Prepares a booking request
   *
   * Reserve Params:
   * start(string)[required] - Booking start date/time in ISO8601 format.
   * fname(string)[required] - First name of person making the booking
   * lname(string)[required] - 	Last name of person making the booking.
   * email(string)[required] - 	Email address of person making the booking.
   * bookings(array)[required] - List of bookings to make, each element should contain "id" and "to" elements.
   *                             The id indicates the space to book.
   *                             The to is the date/time to book the space until in ISO8601 format.
   * custom form (string)[required/optional] - The answers to any custom booking form questions  (returned from /space_form request).
   * question ids                              Note that some custom form questions can be marked as requiring
   *                                           an answer, in this case the booking will fail if an answer is not provided.
   * nickname(string)[optional] - If your space has "Public Nicknames" enabled, then supply the Nickname field via this parameter
   * adminbooking(1 or 0)[Default 0] - A flag to indicate if this is an admin booking. Admin bookings are exempt from patron booking
   *                                   rules such as daily limits, booking window limits, booking duration limits, mediation, available hours, etc.
   * test(1 or 0)[Deafult 0] - A flag to indicate if this is a test booking. If this flag is set the system will process the booking rules but not actually make the booking.
   *                           This is a useful feature when developing an application that makes use of the booking API.
   *
   * @param $params [array] - booking details (see above)
   * @return [array] - decoded json request results
   */
  public function reserve($params)
  {
    $reserve_json = $this->get_reserve_json($params);
    return $reserve_json;
  }

  /**
   * Prepares a request to cancel a booking
   * @param $id [integer] - booking id
   * @return [array] - decoded json request results
   */
  public function cancel($id)
  {
    $cancel_json = $this->get_cancel_json($id);
    return $cancel_json;
  }

  /**
   * Sends cancellation request to LibCal
   * @param $id [integer] - booking id
   * @return [array] - decoded json CURL result
   */
  public function get_cancel_json($id)
  {
    $ch = curl_init($this->url . "/$id");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, TRUE);

    $headers = array();
    $headers[] = "Authorization: Bearer " . $this->token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    return json_decode($result);
  }

  /**
   * Sends booking request to Libcal
   * @param $data [array] - required data for booking (see reserve() )
   * @return [array] - decoded json CURL result
   */
  public function get_reserve_json($data){
    $ch = curl_init($this->url);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = array();
    $headers[] = "Authorization: Bearer " . $this->token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    return json_decode($result);
  }

  /**
   * Function to verify booking match requirements.
   * @param $params [array] - booking details
   * @return [bool]
   */
  private function verify_params($params)
  {
    // TODO Needs implemented
    return true;
  }
}

?>
