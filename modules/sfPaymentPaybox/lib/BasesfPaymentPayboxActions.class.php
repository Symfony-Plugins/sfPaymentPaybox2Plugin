<?php
abstract class BasesfPaymentPayboxActions extends sfActions
{
	/**
	 * A sample action to demonstrate the Paybox System product
	 *
	 * @param sfWebRequest $request
	 */
  public function executeSample(sfWebRequest $request)
  {
    // create paybox library instance
    $gateway = new sfPaymentPaybox();
    
    // instanciate transaction
    $this->transaction = new sfPaymentTransaction($gateway);
    
    // enable test mode if needed
    $this->transaction->enableTestMode();
    
    // define transaction information :
    
    // billing email
    $this->transaction->setBillingEmail("john@example.com");
    
    // currency
    $this->transaction->setCurrencyCode("EUR");
    
    // reference
    $this->transaction->setCmd("XXX_".rand(1000,9999));
    
    // amount
    $this->transaction->setAmount(rand(1, 100) * 100);
  }
  
  /**
   * A sample action to demonstrate the Pyabox Direct product
   *
   * @param sfWebRequest $request
   */
  public function executeSampleDirect(sfWebRequest $request)
  {
    // create paybox library instance
    $gateway = new sfPaymentPayboxDirect();
    
    // instanciate transaction
    $this->transaction = new sfPaymentTransaction($gateway);
    
    // enable test mode if needed
    $this->transaction->enableTestMode();
    
    // define transaction information :
    
    // type
    $this->transaction->setType("00003");
    
    // currency
    $this->transaction->setCurrencyCode("EUR");
    
    // reference
    $this->transaction->setCmd("XXX_".rand(1000,9999));
    
    // amount
    $this->transaction->setAmount(rand(1, 100) * 100);
  }
  
  /**
   * Validate your card using sfWebBrowser to communicate between servers
   *
   * @param sfWebRequest $request
   */
  public function executePay(sfWebRequest $request) {
  	if($request->isMethod('post')) {
  		$params = $request->getPostParameters();
  	
		  $this->form = new sfPaymentPayboxDirectForm(array("params" => $params));
		  
		  $this->amount = $params['MONTANT'];
		  $this->reference = $params['REFERENCE'];

	  	if(isset($params['PORTEUR'])) {
	  		$params['DATEVAL']['day'] = '01';
	      $this->form->bind($params);
	      if ($this->form->isValid())
	      {
	      	// create paybox library instance
			    $gateway = new sfPaymentPayboxDirect();
			    
			    // instanciate transaction
			    $this->transaction = new sfPaymentTransaction($gateway);
			    
			    // enable test mode if needed
			    $this->transaction->enableTestMode();
			    
			    // validate the payment with Paybox server
			    $this->transaction->validateIpn($params);
	      	
			    $response_params = array("transaction" => $this->transaction->getResponseTransNum(), "amount" => $params['MONTANT'], "reference" => $params['REFERENCE']);
	      	// if success, redirection to completed action 
	      	if($this->transaction->isCompleted()) {
	      		$this->transactionCompleted($response_params);
	      	  $this->redirect("sfPaymentPaybox/completed?transaction=".$this->transaction->getResponseTransNum()."&amount=".$params['MONTANT']."&reference=".$params['REFERENCE']);
	      	}
	      	else {
	      		$this->transactionFailed($response_params);
	      	  $this->getUser()->setFlash("error", $this->transaction->getResponseErrorMsg());
	      	}
	      }
	  	}
  	}
  	else
  	 $this->forward404("error");
  }
  
  /**
   * Validate the transaction this action is called on every transaction
   *
   * @param sfWebRequest $request
   */
  public function executeIpn(sfWebRequest $request) {
  	if($request->getParameter('error') == "00000") {
  		$this->transactionCompleted($this->getResponseParams($request));
  	}
  	else {
  		$this->transactionFailed($this->getResponseParams($request));
  	}
  }
  
  /**
   * Transaction completed successfully
   *
   * @param sfWebRequest $request
   */
  public function executeCompleted(sfWebRequest $request) {
    $this->getResponse()->setTitle(sfConfig::get('app_sf_payment_paybox_plugin_success_title', "Payment Gateway Tests - Paybox Success"), false);
    
    $this->transaction_number = $request->getParameter('transaction');
    $this->amount = $request->getParameter('amount');
    $this->reference = $request->getParameter('reference');
  }
  
  /**
   * Transaction failed
   *
   * @param sfWebRequest $request
   */
  public function executeFail(sfWebRequest $request) {
    $this->getResponse()->setTitle(sfConfig::get('app_sf_payment_paybox_plugin_failure_title', "Payment Gateway Tests - Paybox Failure"), false);
  }
  
  /**
   * Transaction cancelled
   *
   * @param sfWebRequest $request
   */
  public function executeCancel(sfWebRequest $request) {
    $this->getResponse()->setTitle(sfConfig::get('app_sf_payment_paybox_plugin_failure_title', "Payment Gateway Tests - Paybox Cancelled"), false);
    
    $this->transactionCanceled($this->getResponseParams($request));
  }
  
  /**
   * Error posting the variables to the cgi module
   *
   * @param sfWebRequest $request
   */
  public function executeError(sfWebRequest $request) {
    $this->getResponse()->setTitle(sfConfig::get('app_sf_payment_paybox_plugin_failure_title', "Payment Gateway Tests - Error"), false);
    
    $error = $request->getParameter("NUMERR");
    
    $this->error_msg = sfPaymentPaybox::getCgiErrorMsg($error);
  }
  
  public function getResponseParams(sfWebRequest $request) {
  	$response_params = array();

  	$response_params['reference'] = $request->getParameter('reference');
    $response_params['amount'] = $request->getParameter('amount');
    $response_params['transaction'] = $request->getParameter('transaction');
    
    return $response_params;
  }
  
  /**
   * Transaction verified and completed
   *
   * @param array $response_params
   */
  abstract public function transactionCompleted($response_params);
  
  /**
   * Transaction failed
   *
   * @param array $response_params
   */
  abstract public function transactionFailed($response_params);
  
  
  /**
   * Transaction canceled (explicitly by user)
   *
   * @param array $response_params
   */
  abstract public function transactionCanceled($response_params);
}
