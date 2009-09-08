<?php
require_once(sfConfig::get('sf_plugins_dir'). '/sfPaymentPaybox2Plugin/modules/sfPaymentPaybox/lib/BasesfPaymentPayboxActions.class.php');

class sfPaymentPayboxActions extends BasesfPaymentPayboxActions
{
	/**
   * Transaction completed successfully
   *
   * @param array $response_params
   */
  public function transactionCompleted($response_params)
  {

  }
  
  /**
   * Transaction failed
   *
   * @param array $response_params
   */
  public function transactionFailed($response_params)
  {

  }
  
  /**
   * Transaction canceled (explicitly by user)
   *
   * @param array $response_params
   */
  public function transactionCanceled($response_params)
  {

  }
}