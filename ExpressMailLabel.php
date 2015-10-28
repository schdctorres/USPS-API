<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class ExpressMailLabel extends USPSBase {
  /**
   * @var string - the api version used for this type of call
   */
  protected $apiVersion = 'ExpressMailLabel';
  /**
   * @var array - route added so far.
   */
  protected $fields = array();


  /**
   * Return the USPS confirmation/tracking number if we have one
   * @return string|bool
   */
  public function getConfirmationNumber() {
  	$response = $this->getArrayResponse();
  	// Check to make sure we have it
  	if(isset($response[$this->getResponseApiName()])) {
  		if(isset($response[$this->getResponseApiName()]['EMConfirmationNumber'])) {
  			return $response[$this->getResponseApiName()]['EMConfirmationNumber'];
  		}
  	}

  	return false;
  }

  /**
   * Return the USPS label as a base64 encoded string
   * @return string|bool
   */
  public function getLabelContents() {
    $response = $this->getArrayResponse();
    // Check to make sure we have it
    if(isset($response[$this->getResponseApiName()])) {
      if(isset($response[$this->getResponseApiName()]['EMLabel'])) {
        return $response[$this->getResponseApiName()]['EMLabel'];
      }
    }

    return false;
  }

  /**
   * Return the USPS receipt as a base64 encoded string
   * @return string|bool
   */
  public function getReceiptContents() {
    $response = $this->getArrayResponse();
    // Check to make sure we have it
    if(isset($response[$this->getResponseApiName()])) {
      if(isset($response[$this->getResponseApiName()]['EMReceipt'])) {
        return $response[$this->getResponseApiName()]['EMReceipt'];
      }
    }

    return false;
  }

  /**
   * returns array of all fields added
   * @return array
   */
  public function getPostFields() {
    return $this->fields;
  }


  /**
   * Set package weight in ounces
   *
   */
  public function setWeightOunces($weight) {
  	$this->setField('WeightInOunces', $weight);
  	return $this;
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
   * Add missing required elements
   * @return void
   */
  public function addMissingRequired() {
	  /*
	'45:Container' => 'FLAT RATE ENVELOPE',
  		'43:CommercialPrice' => 'false',
  		'44:InsuredAmount' => '',
  		'46:Size' => 'REGULAR',
  		'47:Width' => '',
  		'48:Length' => '',
  		'49:Height' => '',
  		'50:Girth' => ''
	    		'42:HoldForManifest' => '',
  		'36:LabelDate' => '',
  		'37:CustomerRefNo' => '',
  		'38:SenderName' => '',
  		'39:SenderEMail' => '',
  		'40:RecipientName' => '',
  		'41:RecipientEMail' => '',
	  */
  	$required = array(
  		'Option' => '',
  		'Revision' => 0,
  		'EMCAAccount' => '',
  		'EMCAPassword' => '',
  		'ImageParameters' => '');
	  /*,
  		'26:FlatRate' => '',
  		'27:SundayHolidayDelivery' => '',
  		'28:StandardizeAddress' => '',
  		'29:WaiverOfSignature' => '',
  		'30:NoHoliday' => '',
  		'31:NoWeekend' => '',
  		'32:SeparateReceiptPage' => '',
  		'33:POZipCode' => '',
  		'34:FacilityType' => 'DDU',
  		'35:ImageType' => 'PDF',
		*/

  	foreach($required as $item => $value) {
		$this->setField($item, $value);
  	}
  }

}
