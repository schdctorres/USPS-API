<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class USPSOpenDistributeLabel extends USPSBase {
  /**
   * @var string - the api version used for this type of call
   */
  protected $apiVersion = 'OpenDistributePriorityV2';
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
  		if(isset($response[$this->getResponseApiName()]['OpenDistributePriorityNumber'])) {
  			return $response[$this->getResponseApiName()]['OpenDistributePriorityNumber'];
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
  		if(isset($response[$this->getResponseApiName()]['OpenDistributePriorityLabel'])) {
  			return $response[$this->getResponseApiName()]['OpenDistributePriorityLabel'];
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
	//public function setToAddress($firstName, $lastName, $company, $address, $city, $state, $zip, $address2=null, $zip4=null, $phone=null, $pobox=null) {
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
   * Set package weight in ounces
   *
   */
  public function setWeightPounds($weight) {
  	$this->setField(37, 'WeightInPounds', $weight);
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
  		'1:Revision' => '',
  		'2:ImageParameters' => '',
  		'2:PermitNumber' => '',
  		'4:PermitIssuingPOZip5' => '',
  		'14:POZipCode' => '',

  		'34:FacilityType' => 'DDU',
  		'35:MailClassEnclosed' => 'Other',
  		'36:MailClassOther' => 'Free Samples',
  		'37:WeightInPounds' => '22',
  		'38:WeightInOunces' => '10',
  		'39:ImageType' => 'PDF',
  		'40:SeparateReceiptPage' => 'false',
  		'41:AllowNonCleansedFacilityAddr' => 'false',
  		'42:HoldForManifest' => 'N',
  		'43:CommercialPrice' => 'N',
  		);
	  */
	  foreach($required as $item => $value) {
		  $this->setField($item, $value);
	  }
  }

}
