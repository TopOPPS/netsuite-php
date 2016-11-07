<?
/**
* /meta.php
* Gives meta information of References fields in
*
* -- HEADERS:
* email: (ex. php@netsuite.com)
* password: (ex. ******)
* account: (ex. KSLADSDSJDNSLAKDMS)
*
* -- POST:
* object: (ex. opportunity)
* field: (ex. entityStatus)
*
**/


require 'vendor/autoload.php';

use NetSuite\NetSuiteService;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => $_SERVER['HTTP_HOST'] ?: "https://webservices.netsuite.com",
   "email"    => $_SERVER['HTTP_EMAIL'],
   "password" => $_SERVER['HTTP_PASSWORD'],
   "role"     => $_SERVER['HTTP_ROLE'],
   "account"  => $_SERVER['HTTP_ACCOUNT'],
   "app_id"   => "4AD027CA-88B3-46EC-9D3E-41C6E6A325E2",
   // optional -------------------------------------
   "logging"  => true,
   "log_path" => ""
);

$service = new NetSuiteService($config);

use NetSuite\Classes\getSelectValueRequest;
use NetSuite\Classes\GetSelectValueFieldDescription;
use NetSuite\Classes\CustomizationType;
use NetSuite\Classes\GetCustomizationIdRequest;

//Example:
// 127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&object=opportunity&field=entityStatus


$cT = new CustomizationType();  
$cT->getCustomizationType = "transactionBodyCustomField";  // or itemCustomField or whatever

$gcIdR = new GetCustomizationIdRequest();
$gcIdR->customizationType = $cT;
$gcIdR->includeInactives = false;

$readResp = $service->getCustomizationId($gcIdR);
print json_encode($readResp);


?>
