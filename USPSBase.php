<?php
/**
 * Load required classes
 */
require_once('XMLParser.php');
/**
 * USPS Base class
 * used to perform the actual api calls
 * @since 1.0
 * @author Vincent Gabriel
 */
class USPSBase {
  const LIVE_API_URL = 'http://production.shippingapis.com/ShippingAPI.dll';
  const TEST_API_URL = 'http://production.shippingapis.com/ShippingAPITest.dll';

  /**
   * @var string - the usps username provided by the usps website
   */
  protected $username = '';
  /**
   *  the error code if one exists
   * @var integer
   */
  protected $errorCode = 0;
  /**
   * the error message if one exists
   * @var string
   */
  protected $errorMessage = '';
  /**
   *  the response message
   * @var string
   */
  protected $response = '';
  /**
   *  the headers returned from the call made
   * @var array
   */
  protected $headers = '';
  /**
   * The response represented as an array
   * @var array
   */
  protected $arrayResponse = array();
  /**
   * All the post fields we will add to the call
   * @var array
   */
  protected $postFields = array();
  /**
   * The api type we are about to call
   * @var string
   */
  protected $apiVersion = '';
  /**
   * @var boolean - set whether we are in a test mode or not
   */
  public static $testMode = false;

  public $endPoint;

  protected $fields = array();

  /**
   * @var array - different kind of supported api calls by this wrapper
   */
  protected $apiCodes = array(
    'RateV2' => 'RateV2Request',
    'RateV4' => 'RateV4Request',
    'IntlRateV2' => 'IntlRateV2Request',
    'Verify' => 'AddressValidateRequest',
    'ZipCodeLookup' => 'ZipCodeLookupRequest',
    'CityStateLookup' => 'CityStateLookupRequest',
    'TrackV2' => 'TrackFieldRequest',
    'FirstClassMail' => 'FirstClassMailRequest',
    'SDCGetLocations' => 'SDCGetLocationsRequest',
    'ExpressMailLabel' => 'ExpressMailLabelRequest',
    'ExpressMailLabelCertify' => 'ExpressMailLabelCertifyRequest',
    'PriorityMail' => 'PriorityMailRequest',
    'OpenDistributePriorityV2' => 'OpenDistributePriorityV2.0Request',
    'OpenDistributePriorityV2Certify' => 'OpenDistributePriorityV2.0CertifyRequest',
    'ExpressMailIntl' => 'ExpressMailIntlRequest',
    'PriorityMailIntl' => 'PriorityMailIntlRequest',
    'FirstClassMailIntl' => 'FirstClassMailIntlRequest',
    'DeliveryConfirmationV4' => 'DeliveryConfirmationV4.0Request'
  );
  /**
   * Default options for curl.
     */
  public static $CURL_OPTS = array(
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_FRESH_CONNECT  => 1,
    CURLOPT_PORT       => 443,
    CURLOPT_USERAGENT      => 'usps-php',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
  );
  /**
   * Constructor
   * @param string $username - the usps api username
   */
  public function __construct($username='') {
    $this->username = $username;
  }

  public function addMissingRequired() {}
  
  /**
   * set the usps api username we are going to user
   * @param string $username - the usps api username
   */
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getServiceDeliveryCalculation() {
    return $this->doRequest();
  }
  /**
   * returns array of all routes added so far.
   * @return array
   */
  public function getPostFields() {
    return $this->route;
  }

  /**
   * Sets the origin 5 digit zip code
   * @param $zip string
   */
  public function setOriginZip($zip){
    $this->originZip = $zip;
  }

  /**
   * Gets the origin zip
   * @return string
   */
  public function getOriginZip(){
    return $this->originZip;
  }

  /**
   * Sets the destination 5 digit zip code
   *
   * @param $zip string
   */
  public function setDestinationZip($zip){
    $this->destinationZip = $zip;
  }
  /**
   * Return the post data fields as an array
   * @return array
   */
  public function getPostData() {
    $fields = array('API' => $this->apiVersion, 'XML' => $this->getXMLString());
    return $fields;
  }
  /**
   * Set the api version we are going to use
   * @param string $version the new api version
   * @return void
   */
  public function setApiVersion($version) {
    $this->apiVersion = $version;
  }
  /**
   * Set whether we are in a test mode or not
   * @param boolean $value
   * @return void
   */
  public function setTestMode($value) {
    self::$testMode = (bool) $value;
  }
  /**
   * Response api name
   * @return string
   */
  public function getResponseApiName() {
    return str_replace('Request', 'Response', $this->apiCodes[$this->apiVersion]);
  }
  /**
   * Makes an HTTP request. This method can be overriden by subclasses if
   * developers want to do fancier things or use something other than curl to
   * make the request.
   *
   * @param CurlHandler optional initialized curl handle
   * @return String the response text
   */
  protected function doRequest($ch=null) {
    if (!$ch) {
      $ch = curl_init();
    }

    $opts = self::$CURL_OPTS;
    $opts[CURLOPT_POSTFIELDS] = http_build_query($this->getPostData(), null, '&');
    $opts[CURLOPT_URL] = $this->getEndpoint();

    // Replace 443 with 80 if it's not secured
    if(strpos($opts[CURLOPT_URL], 'https://')===false) {
      $opts[CURLOPT_PORT] = 80;
    }

    // set options
    curl_setopt_array($ch, $opts);

    // execute
    $this->setResponse( curl_exec($ch) );
    $this->setHeaders( curl_getinfo($ch) );

    // fetch errors
    $this->setErrorCode( curl_errno($ch) );
    $this->setErrorMessage( curl_error($ch) );

    // Convert response to array
    $this->convertResponseToArray();

    // If it failed then set error code and message
    if($this->isError()) {
      $arrayResponse = $this->getArrayResponse();

      // Find the error number
      $errorInfo = $this->getValueByKey($arrayResponse, 'Error');

      if($errorInfo) {
        $this->setErrorCode( $errorInfo['Number'] );
        $this->setErrorMessage( $errorInfo['Description'] );
      }
    }

    // close
    curl_close($ch);

    return $this->getResponse();
  }

  public function setEndpoint($url){
    $this->endPoint = $url;
  }

  public function getEndpoint() {
    return $this->endPoint;
    //return self::$testMode ? self::TEST_API_URL : self::LIVE_API_URL;
  }

  /**
   * Set any other requried string make sure you set the correct position as well
   * as the position of the items matters
   * @param int $position
   * @param string $key
   * @param string $value
   * @return object
   */
  public function setField($key, $value) {
    $this->fields[$key] = $value;
    return $this;
  }

  /**
   * Set the from address
   * @param string $firstName
   * @param string $lastName
   * @param string $company
   * @param string $address
   * @param string $city
   * @param string $state
   * @param string $zip
   * @param string $address2
   * @param string $zip4
   * @param string $phone
   * @return object
   */
  public function setFromAddress($firstName=null, $lastName=null, $company=null, $address=null, $city=null, $state=null, $zip=null, $address2=null, $zip4=null, $phone=null) {
    $this->setField('FromFirstName', $firstName);
    $this->setField('FromLastName', $lastName);
    $this->setField('FromFirm', $company);
    $this->setField('FromAddress1', $address2);
    $this->setField('FromAddress2', $address);
    $this->setField('FromCity', $city);
    $this->setField('FromState', $state);
    $this->setField('FromZip5', $zip);
    $this->setField('FromZip4', $zip4);
    $this->setField('FromPhone', $phone);

    return $this;
  }

  /**
   * Set the to address
   * @param string $firstName
   * @param string $lastName
   * @param string $company
   * @param string $address
   * @param string $city
   * @param string $state
   * @param string $zip
   * @param string $address2
   * @param string $zip4
   * @param string $phone
   * @return object
   */
  public function setToAddress($firstName=null, $lastName=null, $company=null, $address=null, $city=null, $state=null, $zip=null, $address2=null, $zip4=null, $phone=null, $pobox=null) {
    $this->setField('ToFirstName', $firstName);
    $this->setField('ToLastName', $lastName);
    $this->setField('ToFirm', $company);
    $this->setField('ToAddress1', $address2);
    $this->setField('ToAddress2', $address);
    $this->setField('ToCity', $city);
    $this->setField('ToState', $state);
    $this->setField('ToZip5', $zip);
    $this->setField('ToZip4', $zip4);
    $this->setField('ToPhone', $phone);
    ($pobox) ? $this->setField('ToPOBoxFlag', $pobox) : '';
    return $this;
  }

  /**
   * Return the xml string built that we are about to send over to the api
   * @return string
   */
  protected function getXMLString() {
    // Add in the defaults
    $postFields = array(
      '@attributes' => array('USERID' => $this->username),
    );

    // Add in the sub class data
    $postFields = array_merge($postFields, $this->getPostFields());
    $xml = XMLParser::createXML($this->apiCodes[$this->apiVersion], $postFields);
    return $xml->saveXML();
  }
  /**
   * Perform the API call.
   * @return string
   */
  public function createLabel() {
    return $this->doRequest();
  }
  /**
   * Did we encounter an error?
   * @return boolean
   */
  public function isError() {
    $headers = $this->getHeaders();
    $response = $this->getArrayResponse();
    // First make sure we got a valid response
    if($headers['http_code'] != 200) {
      return true;
    }

    // Make sure the response does not have error in it
    if(isset($response['Error'])) {
      return true;
    }

    // Check to see if we have the Error word in the response
    if(strpos($this->getResponse(), '<Error>') !== false) {
      return true;
    }

    // No error
    return false;
  }
  /**
   * Was the last call successful
   * @return boolean
   */
  public function isSuccess() {
    return !$this->isError() ? true : false;
  }
  /**
   * Return the response represented as string
   * @return array
   */
  public function convertResponseToArray() {
    if($this->getResponse()) {
      $this->setArrayResponse(XML2Array::createArray($this->getResponse()));
    }

    return $this->getArrayResponse();
  }
  /**
   * Set the array response value
   * @param array $value
   * @return void
   */
  public function setArrayResponse($value) {
    $this->arrayResponse = $value;
  }
  /**
   * Return the array representation of the last response
   * @return array
   */
  public function getArrayResponse() {
    return $this->arrayResponse;
  }
  /**
   * Set the response
   *
   * @param mixed the response returned from the call
   * @return facebookLib object
   */
  public function setResponse( $response='' ) {
    $this->response = $response;
    return $this;
  }
  /**
   * Get the response data
   *
   * @return mixed the response data
   */
  public function getResponse() {
    return $this->response;
  }
  /**
   * Set the headers
   *
   * @param array the headers array
   * @return facebookLib object
   */
  public function setHeaders( $headers='' ) {
    $this->headers = $headers;
    return $this;
  }
  /**
   * Get the headers
   *
   * @return array the headers returned from the call
   */
  public function getHeaders() {
    return $this->headers;
  }
  /**
   * Set the error code number
   *
   * @param integer the error code number
   * @return facebookLib object
   */
  public function setErrorCode($code=0) {
    $this->errorCode = $code;
    return $this;
  }
  /**
   * Get the error code number
   *
   * @return integer error code number
   */
  public function getErrorCode() {
    return $this->errorCode;
  }
  /**
   * Set the error message
   *
   * @param string the error message
   * @return facebookLib object
   */
  public function setErrorMessage($message='') {
    $this->errorMessage = $message;
    return $this;
  }
  /**
   * Get the error code message
   *
   * @return string error code message
   */
  public function getErrorMessage() {
    return $this->errorMessage;
  }
  /**
   * Find a key inside a multi dim. array
   * @param array $array
   * @param string $key
   * @return mixed
   */
  protected function getValueByKey($array,$key) {
    foreach($array as $k=>$each) {
      if($k==$key) {
        return $each;
      }

      if(is_array($each)) {
        if($return = $this->getValueByKey($each,$key)) {
          return $return;
        }
      }
    }

    // Nothing matched
    return null;
  }
}
