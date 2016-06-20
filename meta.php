<?
require 'vendor/autoload.php';

use NetSuite\NetSuiteService;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => "https://webservices.netsuite.com",
   "email"    => $_GET['email'],
   "password" => $_GET['password'],
   "role"     => "3",
   "account"  => $_GET['account'],
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
$obj->recordType = $_GET['object'];
$obj->field = $_GET['field'];

$request = new getSelectValueRequest();
$request->fieldDescription = $obj;
$request->pageIndex = 0;
 
$getResponse = $service->getSelectValue($request);

if ( ! $getResponse->getSelectValueResult->status->isSuccess) {
    echo "GET ERROR";
} else {
    print json_encode($getResponse->getSelectValueResult->baseRefList->baseRef);
}

?>