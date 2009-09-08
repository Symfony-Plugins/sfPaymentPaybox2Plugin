<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../lib/sfPaymentPayboxTestTools.class.php';

// lime test
$t = new lime_test(2, new lime_output_color());

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

// check application url & application
if(!isset($sf_payment_paybox_test['application_url']) || !$sf_payment_paybox_test['application']) {
  $t->fail('no test application url or application set in test.yml, please check the README file');
  return; 
}

/**
 * Getting application configuration
 */
$app = $sf_payment_paybox_test['application'];

require_once dirname(__FILE__).'/../../../../apps/'.$app.'/config/'.$app.'Configuration.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

// remove all cache
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

// delete the cookies if exist
if(is_file($file = sfConfig::get('sf_data_dir').'/sfWebBrowserPlugin/sfCurlAdapter/cookies.txt'))
  unlink($file);

// sfWebBrowser using sfCurlAdapter, cookies enabled for sandbox authentication
$web_browser = new sfWebBrowser(array(),"sfCurlAdapter", array('cookies' => true));

/**
 * Paybox Direct Test
 */

$test = sfConfig::get('app_sf_payment_paybox_plugin_test');
    
if(!isset($test['identifier'])) {
  $t->fail('No test identifier paybox acccount referenced in app.yml.<br />Please check the README file.');
  return;
}
      
if(!isset($test['site'])) {
  $t->fail('No test paybox site referenced in app.yml.<br />Please check the README file.');
  return;
}
      
if(!isset($test['rank'])) {
  $t->fail('No test paybox rank referenced in app.yml.<br />Please check the README file.');
  return;
}
      
if(!isset($test['password'])) {
  $t->fail('No test paybox password referenced in app.yml.<br />Please check the README file.');
  return;
}

// params to be posted to Paybox server
$params = array(
  "DATEQ" => date("dmY"),
  "TYPE" => "00003",
  "NUMQUESTION" => time(),
  "MONTANT" => rand(1, 100) * 100,
  "SITE" => $test['site'],
  "RANG" => $test['rank'],
  "REFERENCE" => "XXX_".rand(1000,9999),
  "VERSION" => "00103",
  "CLE" => $test['password'],
  "IDENTIFIANT" => "",
  "DEVISE" => "978",
  "PORTEUR" => "1111222233334444",
  "DATEVAL" => "1010",
  "CVV" => "123",
  "ACTIVITE" => "024",
  "ARCHIVAGE" => "AXZ130968CT2",
  "DIFFERE" => "000",
  "NUMAPPEL" => "",
  "NUMTRANS" => "",
  "AUTORISATION" => ""
);

// create paybox library instance
$gateway = new sfPaymentPayboxDirect();
          
// instanciate transaction
$transaction = new sfPaymentTransaction($gateway);

// validate the payment with Paybox server
$transaction->validateIpn($params);

if($transaction->isCompleted())
  $t->pass($transaction->getResponseErrorMsg());
else
  $t->fail($transaction->getResponseErrorMsg());