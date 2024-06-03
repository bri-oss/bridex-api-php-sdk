<?php

include 'util.php';
require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..' . '')->load();

require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\Util\GenerateDate;
use BRI\Util\GenerateRandomString;
use BRI\Util\GetAccessToken;
use BRI\Util\VarNumber;
use BRI\VirtualAccount\BrivaWS;

// env values
$clientId = $_ENV['CONSUMER_KEY']; // customer key
$clientSecret = $_ENV['CONSUMER_SECRET']; // customer secret
$pKeyId = $_ENV['PRIVATE_KEY']; // private key

// url path values
$baseUrl = 'https://sandbox.partner.api.bri.co.id'; //base url

// change variables accordingly
$account = '111231271284142'; // account number
$partnerId = 'feedloop'; //partner id
$channelId = '12345'; // channel id

$partnerServiceId = '   55888'; // partner service id
$customerNo = (new VarNumber())->generateVar(10); // customer no
$virtualAccountName = 'John Doe'; // virtual account name
$total = 10000.00; // total
$expiredDate = (new GenerateDate())->generate('+1 days');
$trxId = (new GenerateRandomString())->generate();
$description = 'terangkanlah';

$getAccessToken = new GetAccessToken();

[$accessToken, $timestamp] = $getAccessToken->get(
  $clientId,
  $pKeyId,
  $baseUrl
);

$brivaWs = new BrivaWS();

// $response = $brivaWs->update(
//   $clientSecret = $clientSecret, 
//   $partnerId = $partnerId, 
//   $baseUrl,
//   $accessToken, 
//   $channelId,
//   $timestamp,
//   $partnerServiceId,
//   $customerNo = '4498466302',
//   $virtualAccountName,
//   $total,
//   $expiredDate,
//   $trxId = 'lvirQR',
//   $description // optional
// );

// $response = $brivaWs->updateStatus(
//   $clientSecret = $clientSecret, 
//   $partnerId = $partnerId, 
//   $baseUrl,
//   $accessToken, 
//   $channelId,
//   $timestamp,
//   $partnerServiceId,
//   $customerNo = '4498466302',
//   $trxId = 'lvirQR',
//   $statusPaid = 'Y'
// );

// $response = $brivaWs->inquiry(
//   $clientSecret = $clientSecret, 
//   $partnerId = $partnerId, 
//   $baseUrl,
//   $accessToken, 
//   $channelId,
//   $timestamp,
//   $partnerServiceId,
//   $customerNo = '4498466302',
//   $trxId = 'lvirQR',
// );

// $response = $brivaWs->delete(
//   $clientSecret = $clientSecret, 
//   $partnerId = $partnerId, 
//   $baseUrl,
//   $accessToken, 
//   $channelId,
//   $timestamp,
//   $partnerServiceId,
//   $customerNo = '4498466302',
//   $trxId = 'lvirQR',
// );

// $response = $brivaWs->getReport(
//   $clientSecret = $clientSecret, 
//   $partnerId = $partnerId, 
//   $baseUrl,
//   $accessToken, 
//   $channelId,
//   $timestamp,
//   $partnerServiceId,
//   $startDate = (new GenerateDate())->generate($modify = '+1 days', $format = 'Y-m-d'),//'2024-01-19',
//   $startTIme = (new GenerateDate())->generate($modify = null, $format = 'H:i:sP', 0, 0),
//   $endTime = (new GenerateDate())->generate($modify = null, $format = 'H:i:sP', 23, 59),
// );

$response = $brivaWs->inquiryStatus(
  $clientSecret = $clientSecret, 
  $partnerId = $partnerId, 
  $baseUrl,
  $accessToken, 
  $channelId,
  $timestamp,
  $partnerServiceId,
  $customerNo,
  $inquiryRequestId = (new GenerateRandomString())->generate(5),
);

echo $response;
