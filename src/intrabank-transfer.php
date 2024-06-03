<?php
include 'util.php';
require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..' . '')->load();

require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\TransferCredit\IntrabankTransfer;
use BRI\Util\GenerateDate;
use BRI\Util\GetAccessToken;

$intrabankTransfer = new IntrabankTransfer();

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

$beneficiaryAccountNo = '888801000157508';
$deviceId = '12345679237';
$channel = 'mobilephone';

$getAccessToken = new GetAccessToken();

[$accessToken, $timestamp] = $getAccessToken->get(
  $clientId,
  $pKeyId,
  $baseUrl
);

// $response = $intrabankTransfer->inquiry(
//   $clientSecret,
//   $partnerId,
//   $baseUrl,
//   $accessToken,
//   $channelId,
//   $timestamp,
//   $beneficiaryAccountNo,
//   $deviceId,
//   $channel
// );

// echo "inquiry $response \n";

$partnerReferenceNo = '2021112500000000000001';
$beneficiaryAccountNo = '888801000157508';
$sourceAccountNo = '888801000157610';
$feeType = 'BEN';
$remark = 'remark test';
$customerReference = '10052031';
$transactionDate = (new GenerateDate())->generate();

$response = $intrabankTransfer->transfer(
  $clientSecret,
  $partnerId,
  $baseUrl,
  $accessToken,
  $channelId,
  $timestamp,
  $partnerReferenceNo,
  $value = "10000.00",
  $beneficiaryAccountNo,
  $sourceAccountNo,
  $feeType,
  $remark,
  $transactionDate,
  $currency = 'IDR',
  $customerReference,
  $deviceId,
  $channel
);

echo "transfer $response \n";