<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class USPSPriorityLabel extends USPSBase {
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
	//public function setFromAddress($firstName, $lastName, $company, $address, $city, $state, $zip, $address2=null, $zip4=null, $phone=null) {
	public function setFromAddress($firstName=null, $lastName=null, $company=null, $address=null, $city=null, $state=null, $zip=null, $address2=null, $zip4=null, $phone=null) {
		if(!$firstName){
			$this->setField('FromName', $company);
		}else{
			$this->setField('FromName', $firstName + ' ' + $lastName);
			$this->setField('FromFirm', $company);
		}

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
	 * @param string $pobox
	 * @return object
	 */
	//public function setToAddress($firstName, $lastName, $company, $address, $city, $state, $zip, $address2=null, $zip4=null, $phone=null, $pobox=null) {
	//public function setToAddress($firstName, $lastName, $company, $address, $city, $state, $zip, $address2=null, $zip4=null, $phone=null, $pobox=null) {
	public function setToAddress($firstName=null, $lastName=null, $company=null, $address=null, $city=null, $state=null, $zip=null, $address2=null, $zip4=null, $phone=null, $pobox=null) {
		if(!$firstName){
			$this->setField('ToName', $company);
		}else{
			$this->setField('ToName', $firstName + ' ' + $lastName);
			$this->setField('ToFirm', $company);
		}
		$this->setField('ToPOBoxFlag', $pobox);
		$this->setField('ToLastName', $lastName);
		$this->setField('ToAddress1', $address2);
		$this->setField('ToAddress2', $address);
		$this->setField('ToCity', $city);
		$this->setField('ToState', $state);
		$this->setField('ToZip5', $zip);
		$this->setField('ToZip4', $zip4);
		$this->setField('ToPhone', $phone);

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
	  $required = array(
		  'Option' => '',
		  'Revision' => '2',
		  'ImageParameters' => '');
	  /*
  	$required = array(
  		'1:Option' => '',
  		'1.1:Revision' => '2',
  		'2:EMCAAccount' => '',
  		'3:EMCAPassword' => '',
  		'4:ImageParameters' => '',
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
  		'36:LabelDate' => '',
  		'37:CustomerRefNo' => '',
  		'38:SenderName' => '',
  		'39:SenderEMail' => '',
  		'40:RecipientName' => '',
  		'41:RecipientEMail' => '',
  		'42:HoldForManifest' => '',
  		'43:CommercialPrice' => 'false',
  		'44:InsuredAmount' => '',
  		'45:Container' => 'FLAT RATE ENVELOPE',
  		'46:Size' => 'REGULAR',
  		'47:Width' => '',
  		'48:Length' => '',
  		'49:Height' => '',
  		'50:Girth' => '',
  	);
	  */
  	foreach($required as $item => $value) {
		$this->setField($item, $value);
  	}
  }

}
