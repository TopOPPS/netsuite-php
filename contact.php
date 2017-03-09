<?
/**
* /contact.php
* Allows paginated queries of a companies contacts.
*
* -- HEADERS:
* email: (ex. php@netsuite.com)
* password: (ex. ******)
* account: (ex. KSLADSDSJDNSLAKDMS)
*
* -- POST:
* company: {'id': 0}
*
**/

require 'vendor/autoload.php';

use NetSuite\NetSuiteService;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => $_SERVER['HTTP_NETSUITE_HOST'] ?: "https://webservices.netsuite.com",
   "email"    => $_SERVER['HTTP_EMAIL'],
   "password" => $_SERVER['HTTP_PASSWORD'],
   "role"     => $_SERVER['HTTP_ROLE'],
   "account"  => $_SERVER['HTTP_ACCOUNT'],
   "app_id"   => "9DB49F44-9854-44E9-8527-115AE98823A5",
   // optional -------------------------------------
   "logging"  => true,
   "log_path" => ""
);

use NetSuite\Classes\SearchMultiSelectField;
use NetSuite\Classes\SearchRequest;

use NetSuite\Classes\ContactSearchBasic;
use NetSuite\Classes\Contact;

use NetSuite\Classes\RecordRef;



// /*
//  Example:
//
// 127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&title_starts_with=
// ^Returns all opps
//
//  */

$service = new NetSuiteService($config);
$service->setSearchPreferences(false, 50);

$rr = new RecordRef();
$rr->internalId = $_POST['id'];
$rr->type = new Contact();

$searchValues = new SearchMultiSelectField();
$searchValues->searchValue = $rr;
$searchValues->operator = 'anyOf';

$search = new ContactSearchBasic();
$search->company = $searchValues;

$request = new SearchRequest();
$request->searchRecord = $search;


$searchResponse = $service->search($request);
$result = $searchResponse->searchResult;

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
