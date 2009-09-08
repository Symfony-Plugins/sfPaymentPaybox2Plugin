<?php

/**
 * sfPaymentPaybox Class
 *
 * This provides support for Paybox to sfPaymentPlugin..
 *
 * @package   sfPaymentPaybox
 * @category  Library
 * @author    Johnny Lattouf <johnny.lattouf@letscod.com>
 * @author    Antoine Leclercq <antoine.leclercq@letscod.com>
 * @link      http://wiki.github.com/letscod/sfPaymentPlugin
 */


class sfPaymentPayboxDirect extends sfPaymentGatewayInterface {
	public $response = array();
	
	public function __construct() {
		parent::__construct();
		
		/**
		 * Translation table: mandatory fields
		 */
		// Paybox identifier 1 to 9 numbers
		$this->addFieldTranslation('Identifier',          'IDENTIFIANT');
		
		// site number 7 numbers
		$this->addFieldTranslation('Site',            'SITE');
		
		// rank number 2 numbers
    $this->addFieldTranslation('Rank',            'RANG');
    
    // transaction currency code following the ISO 4217 (978 for euro, 8410 for USD) 3 numbers
		$this->addFieldTranslation('Currency',        'DEVISE');
		
		// total amount of purchase in centimes without commas or decimal points 3 to 10 numbers
    $this->addFieldTranslation('Amount',          'MONTANT');
    
    // version
    $this->addFieldTranslation('Version',         'VERSION');
    
    // your order reference 1 to 250 characters
    $this->addFieldTranslation('Cmd',             'REFERENCE');
    
    // date of the question
    $this->addFieldTranslation('Date',            'DATEQ');
    
    // type of request concerning the transaction : 
    // 1 = authorization, 2 = debit, 3 = authorization + debit, 4 = credit, 5 = cancellation, 
    // 11= Checking of the existence of a transaction, 12 = transaction without request for authorization, 
    // 13 = Modification of the amount of a transaction, 14 = Refund.
    $this->addFieldTranslation('Type',            'TYPE');
    
    /**
     * Translation table: optional fields
     */
    // single request identifier which prevents confusion over replies in the case of multiple and simultaneous questions
    $this->addFieldTranslation('QuestionNum',     'NUMQUESTION');
    
    // key activated only with the version 00103
    $this->addFieldTranslation('Password',        'CLE');
    
    // cardholder (customer) card number.
    $this->addFieldTranslation('CardNum',         'PORTEUR');

    // expiry date of the cardholder's card in format MMYY.
    $this->addFieldTranslation('ExpiryDate',      'DATEVAL');
    
    // visual cryptogram located on the back on the bank card.
    $this->addFieldTranslation('Cvv',             'CVV');
    
    // electronic commerce indicator (ECI) enabling the provenance of the various electronic money movements to be distinguished
    $this->addFieldTranslation('Eci',             'ACTIVITE');
    
    // filing reference given to your bank. It should be unique and can allow to your bank to supply you an information in case of chargeback.
    $this->addFieldTranslation('Filing',          'ARCHIVAGE');
    
    // a number of days before to send the transaction at your bank in order to credit your bank account.
    $this->addFieldTranslation('Diff',            'DIFFERE');
    
    // number entered by Paybox in the REPONSE frame : this field must be filled in on the next QUESTION frame 
    // if it concerns a request for capture or cancellation. For other types of request (1, 3 or 4), this field remains empty. 
    // field mandatory for type 2, 5 et 13. Use for the SQL requests for type 2, 5 et 13
    $this->addFieldTranslation('CallNum',         'NUMAPPEL');
    
    // number entered by Paybox in the REPONSE frame when handling a payment likely to be sent to the bank : 
    // this field must be filled in on the next „QUESTION‟ frame if it concerns a request for capture or cancellation. 
    // for other types of request (1, 3 or 4), this field remains empty. Field mandatory for type
    $this->addFieldTranslation('TransNum',        'NUMTRANS');
    
    // number of authorization provided by the merchant following a phonic call near its bank.
    $this->addFieldTranslation('Authorization',   'AUTORISATION');
    
    
    /**
     * Translation table: default values
     */
    // gateway url = cgi module
		$this->gatewayUrl = url_for("sfPaymentPaybox/pay");
		
		// language
		$this->setVersion("00103");
		
		// date
		$this->setDate(date("dmY"));
		
		// question number
		$this->setQuestionNum(time());
		
    // ECI
    // 020 : non specified, 
    // 021 : request by telephone, 
    // 022 : request by correspondence, 
    // 023 : request by minitel, 
    // 024 : request by internet, 
    // 027 : regular payment.
    $this->setEci("024");
    
    // filing
    $this->setFiling("AXZ130968CT2");
    
    // diff
    $this->setDiff("000");
    
    // call number
    $this->setCallNum("");
    
    // transaction number
    $this->setTransNum("");
    
    // authorization
    $this->setAuthorization("");
    
		if(sfConfig::get('app_sf_payment_paybox_plugin_identifier'))
		  $this->setIdentifier(sfConfig::get('app_sf_payment_paybox_plugin_identifier'));
		else
		  throw new sfException('No identifier paybox acccount referenced in app.yml.<br />Please check the README file.');
		  
		if(sfConfig::get('app_sf_payment_paybox_plugin_site'))
      $this->setSite(sfConfig::get('app_sf_payment_paybox_plugin_site'));
    else
      throw new sfException('No paybox site referenced in app.yml.<br />Please check the README file.');
      
    if(sfConfig::get('app_sf_payment_paybox_plugin_rank'))
      $this->setRank(sfConfig::get('app_sf_payment_paybox_plugin_rank'));
    else
      throw new sfException('No paybox rank referenced in app.yml.<br />Please check the README file.');

    if(sfConfig::get('app_sf_payment_paybox_plugin_password'))
      $this->setPassword(sfConfig::get('app_sf_payment_paybox_plugin_password'));
    else
      throw new sfException('No paybox password referenced in app.yml.<br />Please check the README file.');
	}
	
	/**
	 * Enables test mode
	 *
	 * @param none
	 * @return none
	 */
	public function enableTestMode()
  {
  	$this->testMode = true;
    
    $test = sfConfig::get('app_sf_payment_paybox_plugin_test');
    
    if(isset($test['identifier']))
      $this->setIdentifier($test['identifier']);
    else
      throw new sfException('No identifier paybox acccount referenced in app.yml.<br />Please check the README file.');
      
    if(isset($test['site']))
      $this->setSite($test['site']);
    else
      throw new sfException('No paybox site referenced in app.yml.<br />Please check the README file.');
      
    if(isset($test['rank']))
      $this->setRank($test['rank']);
    else
      throw new sfException('No paybox rank referenced in app.yml.<br />Please check the README file.');
      
    if(isset($test['password']))
      $this->setPassword($test['password']);
    else
      throw new sfException('No paybox password referenced in app.yml.<br />Please check the README file.');
  }
    
 	/**
	 * Validate the IPN notification
	 *
	 * @param none
	 * @return boolean
	 */
	public function validateIpn($params = array())
	{
		// merging the month and year to DATEVAL
		if(is_array($params['DATEVAL']) && isset($params['DATEVAL']['month']) && isset($params['DATEVAL']['year']))
      $params['DATEVAL'] = $params['DATEVAL']['month'].substr($params['DATEVAL']['year'],2);

    // getting the response code from Paybox
    $this->getResponse($params);
	}
	
	/**
	 * Set currency convert code to number 
	 * USD to 840
	 *
	 * @param string $cur
	 */
	public function setCurrencyCode($cur) {
		switch($cur) {
			case "USD":
				$this->setCurrency(840);
				break;
			case "EUR":
        $this->setCurrency(978);
        break;
      case "LBP":
        $this->setCurrency(422);
        break;
      case "GBP":
        $this->setCurrency(826);
        break;
			default:
				$this->setCurrency($cur);
				break;
		}
	}
	
	/**
	 * Get the response error message
	 *
	 * @param string $code
	 * @return response error message
	 */
	public function getResponseErrorMsg() {
		if(isset($this->response['CODEREPONSE']))
		  $code = $this->response['CODEREPONSE'];
		else
		  $code = "";
		  
	 switch($code) {
      case "00000":
        $error_msg = "Operation successful.";
        break;
      case "00001":
        $error_msg = "When using a version other than 00101, a reply code of 00001 means that the connection to the authorization centre has failed. In this case, you may make another attempt using the backup servers ppps1.paybox.com and ppps2.paybox.com.";
        break;
      case "00002":
        $error_msg = "An error in coherence has occurred.";
        break;
      case "00003":
        $error_msg = "Paybox error.";
        break;
      case "00004":
        $error_msg = "Invalid cardholder number.";
        break;
      case "00005":
        $error_msg = "Invalid question number.";
        break;
      case "00006":
        $error_msg = "Access refused or site/rank incorrect.";
        break;
      case "00007":
        $error_msg = "Invalid date.";
        break;
      case "00008":
        $error_msg = "Incorrect expiry date.";
        break;
      case "00009":
        $error_msg = "Invalid type of operation.";
        break;
      case "00010":
        $error_msg = "Currency unknown";
        break;
      case "00011":
        $error_msg = "Incorrect amount.";
        break;
      case "00012":
        $error_msg = "Invalid order reference.";
        break;
      case "00013":
        $error_msg = "This version is no longer upheld.";
        break;
      case "00014":
        $error_msg = "Incoherent frame received.";
        break;
      case "00015":
        $error_msg = "Error in access to previously referenced data.";
        break;
      case "00018":
        $error_msg = "Transaction not found (type of request 11)";
        break;
      case "00019":
        $error_msg = "Reserved";
        break;
      case "00020":
        $error_msg = "CVV not present";
        break;
      case "00021":
        $error_msg = "Not authorized bin card.";
        break;
      case "00022":
        $error_msg = "Reserved";
        break;
      case "00023":
        $error_msg = "Reserved";
        break;
      case "00024":
        $error_msg = "Error loading of the key : Reserved Future Usage.";
        break;
      case "00025":
        $error_msg = "Missing signature : Reserved Future Usage.";
        break;
      case "00026":
        $error_msg = "Missing key but the signature is present : Reserved Future Usage.";
        break;
      case "00027":
        $error_msg = "Error OpenSSL during the checking of the signature : Reserved Future Usage.";
        break;
      case "00028":
        $error_msg = "Unchecked signature : Reserved Future Usage.";
        break;
      case "00097":
        $error_msg = "Timeout of connection ended.";
        break;
      case "00098":
        $error_msg = "Error of internal connection.";
        break;
      case "00099":
        $error_msg = "Incoherence between the question and the answer. Retry later.";
        break;
      default:
        $error_msg = "Unknown error";
        break;
    }
    
    return $error_msg;
	}

	
	/**
	 * Get the response from Paybox Services for a transaction
	 *
	 * @param array $params
	 * @return the response from Paybox Services for a transaction
	 */
	public function getResponse($params) {
		$action = "https://ppps.paybox.com/PPPS.php";

    $action_backup = "https://ppps1.paybox.com/PPPS.php";
    
    // delete the cookies if exist
		if(is_file($file = sfConfig::get('sf_data_dir').'/sfWebBrowserPlugin/sfCurlAdapter/cookies.txt'))
		  unlink($file);
		
		// sfWebBrowser using sfCurlAdapter, cookies enabled for sandbox authentication
		$web_browser = new sfWebBrowser(array(),"sfCurlAdapter", array('cookies' => true));
		
		
		do {
			$params['NUMQUESTION'] = time();

  		$web_browser->post($action,$params);

	   	$return = $web_browser->getResponseText();
		
		  $arr = explode("&",$return);
		
			$vars = array();
			
			foreach($arr as $field) {
			  $tmp = explode("=",$field);
			  $vars[$tmp[0]] = $tmp[1];
			}
		} while($vars['CODEREPONSE'] == "00005");
		
		$this->response = $vars;
	}
	
	public function isCompleted() {
		if($this->response['CODEREPONSE'] == "00000")
		  return true;
		else 
		  return false;
	}
	
	public function getResponseTransNum() {
		if(isset($this->response['NUMTRANS']))
		  return $this->response['NUMTRANS'];
	}
}