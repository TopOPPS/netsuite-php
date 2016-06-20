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

use NetSuite\Classes\SearchStringField;
use NetSuite\Classes\OpportunitySearchBasic;
use NetSuite\Classes\SearchRequest;

/*
 Example:

127.0.0.1:8888/search.php?email=<username>&password=<password>&account=<accountidhere>&title_starts_with=
^Returns all opps
 
 */

$service->setSearchPreferences(false, 20);

$oppSearchField = new SearchStringField();
$oppSearchField->operator = "startsWith";
$oppSearchField->searchValue = $_GET['title_starts_with'];

$search = new OpportunitySearchBasic();
$search->title = $oppSearchField;

$request = new SearchRequest();
$request->searchRecord = $search;

$searchResponse = $service->search($request);

if (!$searchResponse->searchResult->status->isSuccess) {
    echo "SEARCH ERROR";
} else {
    $result = $searchResponse->searchResult;
    $count = $result->totalRecords;
    $records = $result->recordList;

    print json_encode($result);
}

?>