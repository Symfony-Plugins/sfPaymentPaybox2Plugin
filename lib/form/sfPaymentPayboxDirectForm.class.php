<?php

class sfPaymentPayboxDirectForm extends sfForm
{
  public function setup()
  {
  	$params = $this->getDefault('params');
  	
    foreach($params as $key => $val) {
    	$this->widgetSchema[$key] = new sfWidgetFormInputHidden(array(), array("value" => $val));
    	$this->validatorSchema[$key] = new sfValidatorPass();
    }
    
    // widgets
    $this->widgetSchema['PORTEUR'] = new sfWidgetFormInput(array("label" => "Card number"));
    $this->widgetSchema['DATEVAL'] = new sfWidgetFormDate(
      self::returnDateFormatOptions(range(date('Y'), date('Y') + 15)));
    $this->widgetSchema['CVV'] = new sfWidgetFormInput(array("label" => "Cryptogram : the last3 digits on the back of your card."), array("size" => 3));

    // validators
    $this->validatorSchema['PORTEUR'] = new sfValidatorString(array("min_length" => 16, "max_length" => 16));
    $this->validatorSchema['DATEVAL'] = new sfValidatorDate();
    $this->validatorSchema['CVV'] = new sfValidatorNumber (array("min" => 100, "max" => 999));
    
    $this->widgetSchema->setNameFormat('%s');
    
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    
    parent::setup();
  }
  public function configure() {

  }
  
  static function returnDateFormatOptions($years)
  {
    return array(
        'format' => '%month%%year%', 
        'months' => self::getMonthChoices(), 
        'years' => array_combine($years, $years)
      ); 
  }
  
  /**
   * Month choices from 01 to 12
   *
   * @return array month choices
   */
  static public function getMonthChoices() {
    $choices = array();
    for($i=1;$i<=12;$i++) {
      if($i<10)
       $choices["0".$i] = "0".$i;
      else
       $choices[$i] = $i;
    }
    return $choices;
  }
  
}