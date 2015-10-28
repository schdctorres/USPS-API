<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class USPSInternationalLabel extends USPSBase {
  /**
   * @var string - the api version used for this type of call
   */
  protected $apiVersion = 'ExpressMailIntl';
  /**
   * @var array - route added so far.
   */
  protected $fields = array();

  protected $contents = array();


  /**
   * Return the USPS confirmation/tracking number if we have one
   * @return string|bool
   */
  public function getConfirmationNumber() {
  	$response = $this->getArrayResponse();
  	// Check to make sure we have it
  	if(isset($response[$this->getResponseApiName()])) {
  		if(isset($response[$this->getResponseApiName()]['BarcodeNumber'])) {
  			return $response[$this->getResponseApiName()]['BarcodeNumber'];
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
      if(isset($response[$this->getResponseApiName()]['LabelImage'])) {
        return $response[$this->getResponseApiName()]['LabelImage'];
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
   * Add shipping contents
   * 
   * @return object
   */
  public function addContent($description, $value, $pounds, $ounces, $quantity=1, $tarrifNumber=null, $countryOfOrigin=null) {
	  	$this->contents['ItemDetail'][] = array(
	  		'Description' => $description,
	  		'Quantity' => $quantity,
	  		'Value' => $value,
	  		'NetPounds' => $pounds,
	  		'NetOunces' => $ounces,
	  		'HSTariffNumber' => $tarrifNumber,
	  		'CountryOfOrigin' => $countryOfOrigin,
	  	);

	  	return $this;
  }



  /**
   * Set package weight in ounces
   *
   */
  public function setWeightPounds($weight) {
  	$this->setField(32, 'GrossPounds', $weight);
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
  		'30:Container' => 'NONRECTANGULAR',
  		'32:GrossPounds' => '',
  		'33:GrossOunces' => '',
  		'34:ContentType' => 'Documents',
  		'35:Agreement' => 'Y',
  		'36:ImageType' => 'PDF',
  		'37:ImageLayout' => 'ALLINONEFILE',
  		'38:POZipCode' => '',
  		'39:LabelDate' => '',
  		'40:HoldForManifest' => 'N',
  		'41:Size' => 'LARGE',
  		'42:Length' => '',
  		'43:Width' => '',
  		'44:Height' => 'false',
  		'45:Girth' => '',
  	);
  		*/


  	// We need to add additional fields based on api we are using
  	if($this->apiVersion == 'ExpressMailIntl') {
  		$required = array_merge($required, array(
  			'NonDeliveryOption' => 'Return',
  		));
  	} elseif($this->apiVersion == 'PriorityMailIntl') {
  		$required = array_merge($required, array(
  			'NonDeliveryOption' => 'Return',
  			'Insured' => 'N',
  			'EELPFC' => '',
  		));
  	} elseif($this->apiVersion == 'FirstClassMailIntl') {
  		$required = array_merge($required, array(
  			'FirstClassMailType' => 'PARCEL',
  			'Insured' => 'N',
  			'Machinable' => 'false',
  			'EELPFC' => '',
  		));
  	}

	  foreach($required as $item => $value) {
		  $this->setField($item, $value);
	  }
  }

}
