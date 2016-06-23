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

print "1";
$service = new NetSuiteService($config);
print "2";

use NetSuite\Classes\GetRequest;
use NetSuite\Classes\RecordRef;

$request = new GetRequest();
$request->baseRef = new RecordRef();
$request->baseRef->internalId = $_GET['opp_id'];
$request->baseRef->type = "opportunity";

$getResponse = $service->get($request);

if ( ! $getResponse->readResponse->status->isSuccess) {
    print "4";
    echo "GET ERROR";
} else {
    print "5";
    $customer = $getResponse->readResponse->record;
}

print json_encode($customer);

?>