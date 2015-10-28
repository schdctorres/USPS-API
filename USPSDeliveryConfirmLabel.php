<?php
/**
 * Load required classes
 */
require_once('USPSBase.php');

/**
 */
class USPSDeliveryConfirmLabel extends USPSBase {

    /**
     * @var string - the api version used for this type of call
     */
    protected $apiVersion = 'USPSDeliveryConfirmLabel';
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

}
