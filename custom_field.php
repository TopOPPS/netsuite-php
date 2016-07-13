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
   "app_id"   => "4AD027CA-88B3-46EC-9D3E-41C6E6A325E2",
   // optional -------------------------------------
   "logging"  => true,
   "log_path" => ""
);

$service = new NetSuiteService($config);

use NetSuite\Classes\CustomListSearch;
use NetSuite\Classes\CustomListSearchBasic;
use NetSuite\Classes\SearchMultiSelectField;
use NetSuite\Classes\RecordRef;
use NetSuite\Classes\EmployeeSearchBasic;
use NetSuite\Classes\SearchRequest;




/*
 Example:

127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&title_starts_with=
^Returns all opps

 */

$service->setSearchPreferences(false, 50);

$search = new CustomListSearch();


$rr = new RecordRef();
$rr->type = 'transactionBodyCustomField';

$userRR = new RecordRef();
$userRR->type = 'employee';
$userRR->internalId = -5;

$msField = new SearchMultiSelectField();
$msField->operator = 'anyOf';
$msField->searchValue = $userRR;

$basic = new CustomListSearchBasic();
$basic->owner = $msField;

$search->basic = $basic;


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
    http_response_code(500);
    $error->code = $result->status->statusDetail[0]->code;
    $error->message = $result->status->statusDetail[0]->message;
    print json_encode($error);
} else {
    $count = $result->totalRecords;
    $records = $result->recordList;
    print json_encode($result);
}

?>
