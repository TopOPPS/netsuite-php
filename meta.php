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
   "role"     => $_SERVER['role'],
   "account"  => $_SERVER['HTTP_ACCOUNT'],
   "app_id"   => "4AD027CA-88B3-46EC-9D3E-41C6E6A325E2",
   // optional -------------------------------------
   "logging"  => true,
   "log_path" => ""
);

$service = new NetSuiteService($config);

use NetSuite\Classes\getSelectValueRequest;
use NetSuite\Classes\GetSelectValueFieldDescription;

//Example:
// 127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&object=opportunity&field=entityStatus

$obj = new GetSelectValueFieldDescription();
$obj->recordType = $_POST['object'];
$obj->field = $_POST['field'];

$request = new getSelectValueRequest();
$request->fieldDescription = $obj;
$request->pageIndex = 0;

$getResponse = $service->getSelectValue($request);

if ( ! $getResponse->getSelectValueResult->status->isSuccess) {
    http_response_code(500);
    print_r($getResponse->getSelectValueResult->status->statusDetail[0]->message);
} else {
    print json_encode($getResponse->getSelectValueResult->baseRefList->baseRef);
}

?>
