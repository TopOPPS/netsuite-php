<?
require 'vendor/autoload.php';

use NetSuite\NetSuiteService;

$config = array(
   // required -------------------------------------
   "endpoint" => "2016_1",
   "host"     => "https://webservices.netsuite.com",
   "email"    => $_SERVER['email'],
   "password" => $_SERVER['password'],
   "role"     => $_SERVER['role'],
   "account"  => $_SERVER['account'],
   "app_id"   => "4AD027CA-88B3-46EC-9D3E-41C6E6A325E2",
   // optional -------------------------------------
   "logging"  => true,
   "log_path" => ""
);

$service = new NetSuiteService($config);

use NetSuite\Classes\SearchStringField;
use NetSuite\Classes\OpportunitySearchBasic;
use NetSuite\Classes\SearchRequest;

$service->setSearchPreferences(false, 20);

$oppSearchField = new SearchStringField();
$oppSearchField->operator = "startsWith";
$oppSearchField->searchValue = "zzzzzzz";

$search = new OpportunitySearchBasic();
$search->title = $oppSearchField;

$request = new SearchRequest();
$request->searchRecord = $search;

try {    
    $searchResponse = $service->search($request);
    http_response_code(200);
    print "Valid credentials.";
} catch (Exception $e) {
    http_response_code(401);
    print "Invalid credentials.";
}
?>