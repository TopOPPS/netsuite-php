<?

require 'vendor/autoload.php';

use NetSuite\NetSuiteService;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => $_SERVER['HTTP_HOST'] ?: "https://webservices.sandbox.netsuite.com",
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
$service->setSearchPreferences(false, 50);

use NetSuite\Classes\AttachContactReference;
use NetSuite\Classes\AttachRequest;
use NetSuite\Classes\RecordRef;
use NetSuite\Classes\GetRequest;

$get = new GetRequest;

$contact = new RecordRef();
$contact->type = 'contact';
$contact->internalId =  $_POST['contact'];

$get->baseRef = $contact;
$response = $service->get($get);
if ( ! $response->readResponse->status->isSuccess) {
  http_response_code(500);
  print_r('(Contact Error): ');
  print_r($response->readResponse->status->statusDetail[0]->message);
  print_r('\n');
}

$role = new RecordRef();
$role->type = 'contactRole';
$role->internalId = $_POST['role'];

$get->baseRef = $role;
$response = $service->get($get);
if ( ! $response->readResponse->status->isSuccess) {
  http_response_code(500);
  print_r('(Role Error): ');
  print_r($response->readResponse->status->statusDetail[0]->message);
  print_r('\n');
}

$opportunity = new RecordRef();
$opportunity->type = 'opportunity';
$opportunity->internalId = $_POST['opportunity'];

$get->baseRef = $opportunity;
$response = $service->get($get);
if ( ! $response->readResponse->status->isSuccess) {
  http_response_code(500);
  print_r('(Opportunity Error): ');
  print_r($response->readResponse->status->statusDetail[0]->message);
  print_r('\n');
}

$attachRef = new AttachContactReference();
$attachRef->attachTo = $opportunity;
$attachRef->contact = $contact;
$attachRef->contactRole = $role;

$request = new AttachRequest();
$request->attachReference = $attachRef;


$response = $service->attach($request);

if ( ! $response->writeResponse->status->isSuccess) {
    http_response_code(500);
    print_r($response->writeResponse->status->statusDetail[0]->message);
} else {
    print json_encode($response->writeResponse);
}
