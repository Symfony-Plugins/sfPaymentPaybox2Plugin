<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../lib/sfPaymentPayboxTestTools.class.php';

// lime test
$t = new lime_test(8, new lime_output_color());

/**
 * Test config
 */
$t->comment('Loading configuration file test.yml');
$test_file = dirname(__FILE__).'/../../config/test.yml';
if(!is_file($test_file)) {
  $t->fail('no test.yml file, please check the README file');
  return; 
}
else 
  $t->pass("Configuration loaded");
  
// open the test.yml file
$file = fopen($test_file,"r");
$yaml = stream_get_contents($file);
// loading the data
$sf_payment_paybox_test = sfYaml::load($yaml);

// check application url
if(!isset($sf_payment_paybox_test['application_url']) || !$sf_payment_paybox_test['application_url']) {
  $t->fail('no test application url set in test.yml, please check the README file');
  return; 
}

// delete the cookies if exist
if(is_file($file = sfConfig::get('sf_data_dir').'/sfWebBrowserPlugin/sfCurlAdapter/cookies.txt'))
  unlink($file);

// sfWebBrowser using sfCurlAdapter, cookies enabled for sandbox authentication
$web_browser = new sfWebBrowser(array(),"sfCurlAdapter", array('cookies' => true));

/**
 * Login to sandbox
 */
$t->comment('Going to sfPaymentPaybox/sample');
$web_browser->get($sf_payment_paybox_test['application_url'].'/sfPaymentPaybox/sample');
$t->comment('Clicking button "Pay with Paybox"');
$web_browser->click('Pay with Paybox');
$t->pass("Fields posted to the cgi module");
$t->comment('Submitting form 1 after cgi encryption');
$uri = sfPaymentPayboxTestTools::getFormUri($web_browser->getResponseDom());

$t->comment('Gathering fields and post it to "'.$uri.'"');
$params = sfPaymentPayboxTestTools::getFormParams($web_browser->getResponseDom());

if($params) 
  $t->pass("Getting the fields to post it to payment method");
else {
  $t->fail("Failed to get the fields to post it to payment method");
  return;
}

foreach($params as $field => $value) {
  $t->comment("\"".$field."\" = ".$value);
}
$web_browser->post($uri,$params);
$t->pass('Payment posted to form 1');

$t->comment('Submitting form 2 after payment method');
$uri = sfPaymentPayboxTestTools::getFormUri($web_browser->getResponseDom());

$t->comment('Gathering fields and post it to "'.$uri.'"');
$params = sfPaymentPayboxTestTools::getFormParams($web_browser->getResponseDom());

if($params) 
  $t->pass("Getting the fields to post it to payment");
else {
  $t->fail("Failed to get the fields to post it to payment");
  return;
}

foreach($params as $field => $value) {
  $t->comment("\"".$field."\" = ".$value);
}
$web_browser->post($uri,$params);
$t->pass('Payment posted to form 2');

/**
 * Validate payment
 */
$validation_url = "https://tpeweb.paybox.com/cgi/MYtraitetransaction.cgi";
$t->comment('Gathering fields and post it to "'.$validation_url.'"');
$params = sfPaymentPayboxTestTools::getFormParams($web_browser->getResponseDom());

if($params) 
  $t->pass("Getting the fields to pay");
else {
  $t->fail("Failed to get the fields to pay");
  return;
}

$params["NUMERO_CARTE"] = "1111222233334444";
$params["MOIS_VALIDITE"] = "01";
$params["AN_VALIDITE"] = "10";
$params["CVVX"] = "123";

foreach($params as $field => $value) {
  $t->comment("\"".$field."\" = ".$value);
}

$web_browser->post($validation_url,$params);

$dom = $web_browser->getResponseDom();
$xpath = new DomXpath($dom);
$metas = $xpath->query('//meta');

foreach($metas as $meta)
  break;

$completed_url_arr = explode("URL=",$meta->getAttribute("content"));

$completed_url = $completed_url_arr[1];

if(substr_count($completed_url,$sf_payment_paybox_test['application_url'].'/sfPaymentPaybox/completed'))
  $t->pass("Transaction completed successfully with the following fields");
else
  $t->fail("Transaction could not complete");
  
$web_browser->get($completed_url);

$parse_url = parse_url($completed_url);

parse_str($parse_url['query'],$params);

foreach($params as $field => $value) {
  $t->comment("\"".$field."\" = ".$value);
}