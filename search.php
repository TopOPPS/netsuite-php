<?
/**
* /search.php
* Allows paginated queries of NetSuite objects.
*
* -- HEADERS:
* email: (ex. php@netsuite.com)
* password: (ex. ******)
* account: (ex. KSLADSDSJDNSLAKDMS)
*
* -- POST:
* object: (ex. opportunity)
* page: (ex. 3) --optional
* searchId: (ex. WEBSERVICES_TSTDRV1496099_0621201619530099331396424930_e8b15) --optional
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

use NetSuite\Classes\SearchStringField;
use NetSuite\Classes\SearchRequest;
use NetSuite\Classes\SearchMoreWithIdRequest;

use NetSuite\Classes\CustomerSearchBasic;
use NetSuite\Classes\ContactSearchBasic;
use NetSuite\Classes\ContactRoleSearchBasic;
use NetSuite\Classes\CustomListSearchBasic;
use NetSuite\Classes\CustomRecordSearchBasic;
use NetSuite\Classes\TransactionSearchBasic;


use NetSuite\Classes\EntitySearchBasic;
use NetSuite\Classes\ItemSearchBasic;
use NetSuite\Classes\TaskSearchBasic;
use NetSuite\Classes\EmployeeSearchBasic;
use NetSuite\Classes\OpportunitySearchBasic;
use NetSuite\Classes\CustomerStatusSearchBasic;




/*
 Example:

127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&title_starts_with=
^Returns all opps

 */

$service->setSearchPreferences(false, 20);

try{

  $search = get_basic_search($_POST['object']);
} catch ( Exception $e ){
  http_response_code(500);
  var_dump($_POST);
}

if(array_key_exists('page', $_POST) && $_POST['page'] > 1)
{
  $request = new SearchMoreWithIdRequest();
  $request->searchId = $_POST['searchId'];
  $request->pageIndex = $_POST['page'];
  $request->pageSize = array_key_exists('pageSize', $_POST) ? $_POST['pageSize'] : 20;
  $searchMoreWithIdResponse = $service->searchMoreWithId($request);
  $result = $searchMoreWithIdResponse->searchResult;
} else {
  $request = new SearchRequest();
  $request->searchRecord = $search;
  $request->pageSize = array_key_exists('pageSize', $_POST) ? $_POST['pageSize'] : 20;
  $searchResponse = $service->search($request);
  $result = $searchResponse->searchResult;
}

if (!$result->status->isSuccess) {
    http_response_code(408);
    $error->code = $result->status->statusDetail[0]->code;
    $error->message = $result->status->statusDetail[0]->message;
    print json_encode($error);
} else {
    $count = $result->totalRecords;
    $records = $result->recordList;
    print json_encode($result);
}

?>

<?

function get_basic_search($object)
{
  switch($object)
  {
    case 'account':
      return new CustomerSearchBasic();
    case 'contact':
      return new ContactSearchBasic();
    case 'contactrole':
      return new ContactRoleSearchBasic();
    case 'customfield':
      return new CustomRecordSearch();
    case 'entity':
      return new EntitySearchBasic();
    case 'task':
      return new TaskSearchBasic();
    case 'note':
      return new NoteSearchBasic();
    case 'user':
      return new EmployeeSearchBasic();
    case 'opportunity':
      return new OpportunitySearchBasic();
    case 'stage':
      return new CustomerStatusSearchBasic();
    case 'item':
      return new ItemSearchBasic();
    default:
      throw new Exception("Object not found");
  }
}

?>
