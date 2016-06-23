<?
/**
* /objects.php
* Allows CRUD operations on a single object in NetSuite
*
* -- HEADERS:
* email: (ex. php@netsuite.com)
* password: (ex. ******)
* account: (ex. KSLADSDSJDNSLAKDMS)
*
* -- POST:
* type: (ex. POST)
* entity: (ex. {"name": "Test Entity", "id": 100})
* data: ({"amount": 12345}) --optional
*
**/

require 'vendor/autoload.php';

use NetSuite\NetSuiteService;
use NetSuite\Classes\DeleteRequest;
use NetSuite\Classes\GetRequest;
use NetSuite\Classes\RecordRef;
use NetSuite\Classes\UpdateRequest;
use NetSuite\Classes\AddRequest;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => "https://webservices.netsuite.com",
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
switch($_POST['type']){
  case 'POST':
    $result = POST($service);
    break;
  case 'DELETE':
    $result = DELETE($service);
    break;
  case 'PUT':
    $result = PUT($service);
    break;
  default:
    $result = GET($service);
    break;
}

print_r($result);

?>

<?

use NetSuite\Classes\Account;
use NetSuite\Classes\Contact;
use NetSuite\Classes\Current;
use NetSuite\Classes\Customer;
use NetSuite\Classes\EntityCustomField;
use NetSuite\Classes\Employee;
use NetSuite\Classes\Opportunity;
use NetSuite\Classes\CustomerStatus;
use NetSuite\Classes\Record;

function entity_map($name){

  switch($name){
    case "account":
      return new Account();
    case "contact":
      return new Contact();
    case "currency":
      return new Currency();
    case "customer":
      return new Customer();
    case "custom_field":
      return new EntityCustomField();
    case "user":
      return new Employee();
    case "opportunity":
      return new Opportunity();
    case 'stage':
      return new CustomerStatus();
    default:
      return new Record();
  }

}


function map_from_data($entity, $data){
  /**
  *  Form corresponding object based off of entity definition.
  *  Set an attributes value based on provided data parameters.
  **/
  $el = entity_map($entity->name);
  $el->internalId = $entity->id;

  foreach($data as $key => $value)
  {
    $el->$key = $value;
  }
  return $el;
}

function GET($service)
{
  $entity = json_decode($_POST['entity']);
  $request = new GetRequest();
  $request->baseRef = new RecordRef();
  $request->baseRef->internalId = $entity->id;
  $request->baseRef->type = $entity->name;
  $getResponse = $service->get($request);
  if ( ! $getResponse->readResponse->status->isSuccess) {
    http_response_code(500);
    print_r($getResponse->writeResponse->status->statusDetail[0]->message);
  } else {
      return $getResponse->readResponse->record;
  }
  return $getResponse;
}

function POST($service)
{
  $entity = json_decode($_POST['entity']);
  $data = json_decode($_POST['data']);
  $request = new UpsertRequest();
  $request->record = map_from_data($entity, $data);
  $addResponse = $service->add($request);
  if ( ! $addResponse->writeResponse->status->isSuccess) {
    http_response_code(500);
    print_r($updateResponse->writeResponse->status->statusDetail[0]->message);
  } else {
      $request = new GetRequest();
      $request->baseRef = $addResponse->writeResponse->baseRef;
      $getResponse = $service->get($request);
      return $getResponse->readResponse->record;
  }
}

function PUT($service)
{
  $entity = json_decode($_POST['entity']);
  $data = json_decode($_POST['data']);
  $request = new UpdateRequest();
  $request->record = map_from_data($entity, $data);
  $updateResponse = $service->update($request);
  if ( ! $updateResponse->writeResponse->status->isSuccess) {
      http_response_code(500);
      print_r($updateResponse->writeResponse->status->statusDetail[0]->message);
  } else {
    $request = new GetRequest();
    $request->baseRef = $updateResponse->writeResponse->baseRef;
    $getResponse = $service->get($request);
    return $getResponse->readResponse->record;
  }
}

function DELETE()
{
  $entity = json_decode($_POST['entity']);
  $request = new DeleteRequest();
  $request->baseRef = new RecordRef();
  $request->baseRef->internalId = $entity->id;
  $request->baseRef->type = $entity->name;
  $deleteResponse = $service->delete($request);
  if ( ! $deleteResponse->writeResponse->status->isSuccess) {
      http_response_code(500);
      print_r($deleteResponse->writeResponse->status->statusDetail[0]->message);
  } else {
    $request = new GetRequest();
    $request->baseRef = $deleteResponse->writeResponse->baseRef;
    $getResponse = $service->get($request);
    return $getResponse->readResponse->record;
  }
}

?>
