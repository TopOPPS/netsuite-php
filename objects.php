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
* OR
* data: {"custom_field": {"type": "boolean", "scriptId": "custbody10", "value": false }}
*
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
$service->setSearchPreferences(false, 50);

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

print json_encode($result);

?>

<?

use NetSuite\Classes\Account;
use NetSuite\Classes\Task;
use NetSuite\Classes\Contact;
use NetSuite\Classes\Current;
use NetSuite\Classes\Customer;
use NetSuite\Classes\CustomFieldList;
use NetSuite\Classes\EntityCustomField;
use NetSuite\Classes\Employee;
use NetSuite\Classes\Opportunity;
use NetSuite\Classes\CustomerStatus;
use NetSuite\Classes\OpportunityItem;
use NetSuite\Classes\Record;
use NetSuite\Classes\StringCustomFieldRef;
use NetSuite\Classes\LongCustomFieldRef;
use NetSuite\Classes\DoubleCustomFieldRef;
use NetSuite\Classes\DateCustomFieldRef;
use NetSuite\Classes\BooleanCustomFieldRef;

function entity_map($name){

  switch($name){
    case "account":
      return new Account();
    case "task":
      return new Task();
    case "contact":
      return new Contact();
    case "currency":
      return new Currency();
    case "customer":
      return new Customer();
    case "custom_field":
      return new TransactionBodyCustomField();
    case "user":
      return new Employee();
    case "opportunity":
      return new Opportunity();
    case "stage":
      return new CustomerStatus();
    case "item":
      return new Item();
    default:
      return new Record();
  }

}

function custom_field_map($name){

  switch($name){
    case "amount":
      return new DoubleCustomFieldRef();
    case "boolean":
      return new BooleanCustomFieldRef();
    case "datetime":
      return new DateCustomFieldRef();
    case "int":
      return new DoubleCustomFieldRef();
    case "percent":
      return new DoubleCustomFieldRef();
    case "phone":
      return new LongCustomFieldRef();
    case "textarea":
      return new StringCustomFieldRef();
    case "url":
      return new StringCustomFieldRef();
    default:
      return new CustomFieldRef();
  }

}


function map_from_data($entity, $data){
  /**
  *  Form corresponding object based off of entity definition.
  *  Set an attributes value based on provided data parameters.
  **/
  $el = entity_map($entity['name']);
  $el->internalId = $entity['id'];
  if(array_key_exists('custom_field', $data)){
    $field = custom_field_map($data['custom_field']['type']);
    $field->scriptId = $data['custom_field']['internalId'];
    $field->value = $data['custom_field']['value'];

    $customFieldList = new customFieldList();
    $customFieldList->customField[] = $field;
    $el->customFieldList = $customFieldList;
    return $el;
  }

  foreach($data as $key => $value)
  {
    $el->$key = $value;
  }
  return $el;

}

function GET($service)
/**
*
* Get a single object.
*
* ex.
*  type : GET
*  entity : { 'id' : 0, 'name': 'Opportunity'}
*
**/
{
  $entity = json_decode($_POST['entity'], true);
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
/**
*
* Post a new object.
*
* ex.
*  type : POST
*  entity : {'name': 'Opportunity'}
*  data: {'amount' : 5000, 'title': 'Export Solutions'}
*
**/
{
  $entity = json_decode($_POST['entity'], true);
  $data = json_decode($_POST['data'], true);
  $request = new AddRequest();
  $request->record = map_from_data($entity, $data);
  $addResponse = $service->add($request);
  if ( ! $addResponse->writeResponse->status->isSuccess) {
    http_response_code(500);
    print_r($addResponse->writeResponse->status->statusDetail[0]->message);
  } else {
      $request = new GetRequest();
      $request->baseRef = $addResponse->writeResponse->baseRef;
      $getResponse = $service->get($request);
      return $getResponse->readResponse->record;
  }
}

function PUT($service)
/**
*
* Update fields on an object. Does not apply to record refs and custom fields
*
* ex.
*  type : PUT
*  entity : {'id': 0, 'name': 'Opportunity'}
*  data: {'amount' : 5500 }
*
**/
{
  $entity = json_decode($_POST['entity'], true);
  $data = json_decode($_POST['data'], true);
  $request = new UpdateRequest();
  $request->record = map_from_data($entity, $data);
  $updateResponse = $service->update($request);
  if ( ! $updateResponse->writeResponse->status->isSuccess) {
      http_response_code(500);
      print_r($updateResponse->writeResponse->status->statusDetail[0]->message);
  } else {
      return $updateResponse->writeResponse;
  }
}

function DELETE()
/**
*
* Delete an object.
*
* ex.
*  type : DELTE
*  entity : {'id': 0, 'name': 'Opportunity'}
*
**/
{

  $entity = json_decode($_POST['entity'], true);
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

function MAP()
{

}

?>
