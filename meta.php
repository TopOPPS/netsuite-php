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

use NetSuite\Classes\getSelectValueRequest;
use NetSuite\Classes\GetSelectValueFieldDescription;

$recordType = "opportunity";
$field = "entityStatus";

$obj = new GetSelectValueFieldDescription();
$obj->recordType = $recordType;
$obj->field = $field;

$request = new getSelectValueRequest();
$request->fieldDescription = $obj;
$request->pageIndex = 0;
 
$getResponse = $service->getSelectValue($request);

print $getResponse->readResponse;
print "2";
print $getResponse->readResponse->record;
/*
if ( ! $getResponse->readResponse->status->isSuccess) {
    echo "GET ERROR";
} else {
    $customer = $getResponse->readResponse->record;
}

print json_encode($customer);
*/

/*
 *var methods = new Hashtable();
var shipMethodFieldDesc = new GetSelectValueFieldDescription()
{
    field = "shipmethod",
    recordType = RecordType.estimate,
    recordTypeSpecified = true
};

// make connection.    

var result = connection.Service.getSelectValue(shipMethodFieldDesc, 0);
if (result.status.isSuccess)
{
    for (var i = 0; i < result.totalRecords; i++)
    {
       // cast to RecordRef 
       var itemRef = (RecordRef)result.baseRefList[i];

       methods.Add(itemRef.internalId, itemRef.name);
    }
}
*/

?>