<?php

use BRI\TransferCredit\TransactionStatusInquiry;
use BRI\Util\GenerateDate;
use BRI\Util\GetAccessToken;

include 'util.php';
require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..' . '')->load();

require __DIR__ . '/../briapi-sdk/autoload.php';


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

$transactionStatusInquiry = new TransactionStatusInquiry();

$response = $transactionStatusInquiry->inquiry(
  $clientSecret,
  $partnerId,
  $baseUrl,
  $accessToken,
  $channelId,
  $timestamp,
  $originalPartnerReferenceNo = '20201029000000000000',
  $serviceCode = '18',
  $transactionDate = (new GenerateDate())->generate(),
  $deviceId,
  $channel
);

echo "transfer status inquiry $response \n";
