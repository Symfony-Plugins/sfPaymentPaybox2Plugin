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

class sfPaymentPaybox extends sfPaymentGatewayInterface {
	
	public function __construct() {
		parent::__construct();
		
		/**
		 * Translation table: mandatory fields
		 */
		// Paybox identifier 1 to 9 numbers
		$this->addFieldTranslation('Identifier',          'PBX_IDENTIFIANT');
		
		// site number 7 numbers
		$this->addFieldTranslation('Site',            'PBX_SITE');
		
		// rank number 2 numbers
    $this->addFieldTranslation('Rank',            'PBX_RANG');
    
    // transaction currency code following the ISO 4217 (978 for euro, 8410 for USD) 3 numbers
		$this->addFieldTranslation('Currency',        'PBX_DEVISE');
		
		// total amount of purchase in centimes without commas or decimal points 3 to 10 numbers
    $this->addFieldTranslation('Amount',          'PBX_TOTAL');
    
    // email address of purchaser
    $this->addFieldTranslation('BillingEmail',    'PBX_PORTEUR');
    
    // your order reference 1 to 250 characters
    $this->addFieldTranslation('Cmd',             'PBX_CMD');
    
    // mode of retrieval of information values (1,2,3, or 4)
    // 1 HTML
    // 2 local file
    // 3 command line
    // 4 environment variable 
    $this->addFieldTranslation('Mode',            'PBX_MODE');
    
    // variables sent by Paybox (amount, order reference, transaction number, subscription number and authorization number)
    $this->addFieldTranslation('Return',          'PBX_RETOUR');
    
    /**
     * Translation table: optional fields
     */
    // return page from Paybox to your site after payment has been accepted
    $this->addFieldTranslation('ReturnSuccess',   'PBX_EFFECTUE');
    
    // return page from Paybox to your site after payment has been cancelled
    $this->addFieldTranslation('CancelReturn',    'PBX_ANNULE');
    
    // return page from Paybox to your site after payment has been refused
    $this->addFieldTranslation('ReturnFail',      'PBX_REFUSE');
    
    // screen background of the intermediary page
    $this->addFieldTranslation('Background',      'PBX_BKGD');

    // delay in displaying the intermediary page, value in milliseconds
    $this->addFieldTranslation('Wait',            'PBX_WAIT');
    
    // text able to be displayed on the intermediary page instead of the default text
    $this->addFieldTranslation('Txt',             'PBX_TXT');
    
    // name given to the button of the intermediary page "nul" for the removal of this button
    $this->addFieldTranslation('Boutpi',          'PBX_BOUTPI');
    
    // language used by Paybox for displaying the payment page.
    // FRA GBR ESP ITA DEU NLD SWE
    $this->addFieldTranslation('Language',        'PBX_LANGUE');
    
    // name of local file when mode = 3
    $this->addFieldTranslation('Opt',             'PBX_OPT');
    
    // the URL of your site which enables the display of descriptions of possible errors which could occur when displaying the payment page
    $this->addFieldTranslation('Error',           'PBX_ERREUR');
    
    // management mode of the intermediary page. Possible values A, B, C, D and E
    $this->addFieldTranslation('Output',          'PBX_OUTPUT');
    
    /**
     * Translation table: default values
     */
    // gateway url = cgi module
		$this->gatewayUrl = '/cgi-bin/modulev2.cgi';

		// html mode
		$this->setMode(1);
		
		// language
		$this->setLanguage("GBR");
		
		// return values
		$this->setReturn(sfConfig::get('app_sf_payment_paybox_plugin_return'));
		
		// cancel action
		$this->setCancelReturn(url_for('sfPaymentPaybox/cancel',true));
		
		// success action
		$this->setReturnSuccess(url_for('sfPaymentPaybox/completed',true));
		
		// failure action
		$this->setReturnFail(url_for('sfPaymentPaybox/fail',true));
		
		// error action
    $this->setError(url_for('sfPaymentPaybox/error',true));
    
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
      
    /**
     * Translation table: intermediary page
     */
    $intermediary_page = sfConfig::get('app_sf_payment_paybox_plugin_intermediary_page');
    // 0 milliseconds wait
    if(isset($intermediary_page['wait']))
      $this->setWait($intermediary_page['wait']);
    
    // keep the default text
    if(isset($intermediary_page['txt']))
      $this->setTxt($intermediary_page['txt']);
    
    // background
    if(isset($intermediary_page['background']))
      $this->setBackground($intermediary_page['background']);
    
    // submit
    if(isset($intermediary_page['button']))
      $this->setBoutpi($intermediary_page['button']);
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
  }
    
 	/**
	 * Validate the IPN notification
	 *
	 * @param none
	 * @return boolean
	 */
	public function validateIpn($parameters = array())
	{
		
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
	 * Get the error message from the cgi module
	 *
	 * @param int $code
	 * @return the error message from the cgi module
	 */
	static public function getCgiErrorMsg($code) {
	 switch($code) {
      case "-1" :
        $error_msg = "error in reading the parameters via stdin (POST method) (error in http reception).";
        break;
      case "-2" :
        $error_msg = "Error in memory allocation. Not enough memory available on the trader's server.";
        break;
      case "-3" :
        $error_msg = "Error in reading the parameters QUERY_STRING or CONTENT_LENGTH. (http error).";
        break;
      case "-4" :
        $error_msg = "PBX_RETOUR, PBX_ANNULE, PBX_REFUSE or PBX_EFFECTUE are too long (<150 characters).";
        break;
      case "-5" :
        $error_msg = "Error in opening the file (if PBX_MODE contains 3) : local file non-existent, not found or access error.";
        break;
      case "-6" :
        $error_msg = "Error in file format (if PBX_MODE contains 3) : local file badly formed, empty or lines are badly formatted.";
        break;
      case "-7" :
        $error_msg = "A compulsory variable is missing (PBX_SITE, PBX_RANG, PBX_IDENTIFIANT, PBX_TOTAL, PBX_CMD, etc.)";
        break;
      case "-8" :
        $error_msg = "One of the numerical variables contains a non-numerical character (site, rank, identifier, amount, currency etc. )";
        break;
      case "-9" :
        $error_msg = "PBX_SITE contains a site number which does not consist of exactly 7 characters.";
        break;
      case "-10" :
        $error_msg = "PBX_RANG contains a rank number which does not consist of exactly 2 characters.";
        break;
      case "-11" :
        $error_msg = "PBX_TOTAL has more than 10 or fewer than 3 numerical characters.";
        break;
      case "-12" :
        $error_msg = "PBX_LANGUE or PBX_DEVISE contains a code which does not contain exactly 3 characters.";
        break;
      case "-13" :
        $error_msg = "PBX_CMD is empty or contains a reference longer than 250 characters.";
        break;
      case "-14" :
        $error_msg = "Not used";
        break;
      case "-15" :
        $error_msg = "Not used";
        break;
      case "-16" :
        $error_msg = "PBX_PORTEUR does not contain a valid e-mail address.";
        break;
      case "-17" :
        $error_msg = "Error of coherence (multi-baskets) : Reserved Future Usage";
        break;
      default :
        $error_msg = "Unknown error";
        break;
    }
    
    return $error_msg;
	}
	
	
}