<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class SDCGetLocations extends USPSBase {

  public $originZip;

  /**
   * @var string - the api version used for this type of call
   */
  protected $apiVersion = 'SDCGetLocations';
  /**
   * @var array - route added so far.
   */
  protected $route = array();
  /**
   * Perform the API call.
   * @return string
   */


  /**
   * Gets the destination zip
   * @return string
   */
  public function getDestinationZip(){
    return $this->destinationZip;
  }
  /**
   * @param $mail_class integer from 0 to 6 indicating the class of mail.
   *   “0” = All Mail Classes
   *   “1” = Express Mail
   *   “2” = Priority Mail
   *   “3” = First Class Mail
   *   “4” = Standard Mail
   *   “5” = Periodicals
   *   “6” = Package Services
   * @param null $accept_date string in the format dd-mmm-yyyy. for Example: <AcceptDate>29‐Sep‐2014</AcceptDate>
   * @param null $accept_time string in the format HHMM. For Example: <AcceptTime>1600</AcceptTIme>
   */
  public function addRoute($mail_class = NULL, $accept_date = NULL, $accept_time = NULL) {
    if (empty($mail_class)) {
      $mail_class = 0;
    }
    $route = array(
      'MailClass' => $mail_class,
      'OriginZIP' => $this->getOriginZip(),
      'DestinationZIP' => $this->getDestinationZip()
    );
    if (empty($accept_date)) {
      $accept_date = date("d-M-Y");
    }
    if (empty($accept_time)) {
      $accept_time = "1600";
    }
    $route['AcceptDate'] = $accept_date;
    $route['AcceptTime'] = $accept_time;
    $this->route = $route;
  }
}



