<?
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

use NetSuite\Classes\GetDataCenterUrlsRequest;
use NetSuite\Classes\LoginRequest;
use NetSuite\Classes\Passport;

$data_center_urls_request = new GetDataCenterUrlsRequest();
$data_center_urls_request->account = $config["account"];
$response = $service->getDataCenterUrls($data_center_urls_request);
$urlsResult = $response->getDataCenterUrlsResult->dataCenterUrls;


$login_request = new LoginRequest();
$passport = new Passport();
$passport->email = $config['email'];
$passport->password = $config['password'];
$passport->account = $config['account'];

$login_request->passport = $passport;
$response = $service->login($login_request);
$idResult = $response->sessionResponse->userId->internalId;

$result = array(
  'url' => $urlsResult->systemDomain,
  'id' => $idResult
);

print json_encode($result);
?>
