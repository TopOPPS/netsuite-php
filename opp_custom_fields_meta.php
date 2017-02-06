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
   "host"     => "https://webservices.netsuite.com",
   "email"    => $_SERVER['HTTP_EMAIL'],
   "password" => $_SERVER['HTTP_PASSWORD'],
   "role"     => $_SERVER['HTTP_ROLE'],
   "account"  => $_SERVER['HTTP_ACCOUNT'],
   "app_id"   => "9DB49F44-9854-44E9-8527-115AE98823A5",
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
