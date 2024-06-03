<?php

use BRI\Util\GenerateDate;
use BRI\Util\GetAccessToken;
use BRI\Util\VarNumber;
use BRI\VirtualAccount\BrivaOnline;

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

$getAccessToken = new GetAccessToken();

[$accessToken, $timestamp] = $getAccessToken->get(
  $clientId,
  $pKeyId,
  $baseUrl
);

$brivaOnline = new BrivaOnline();

$partnerServiceId = '   55888';
$customerNo = (new VarNumber())->generateVar(10);
$inquiryRequestId = 'e3bcb9a2-e253-40c6-aa77-d72cc138b744';
$value = '100000.00';
$currency = 'IDR';
$trxDateInit = (new GenerateDate())->generate();
$channelCode = 1;
$sourceBankCode = '002';
$passApp = 'b7aee423dc7489dfa868426e5c950c677925';
$idApp = 'TEST';

$response = $brivaOnline->payment(
  $clientSecret,
  $partnerId,
  $baseUrl,
  $accessToken,
  $channelId,
  $timestamp,
  $partnerServiceId,
  $customerNo,
  $inquiryRequestId,
  $value,
  $currency,
  $trxDateInit,
  $channelCode,
  $sourceBankCode,
  $passApp,
  $idApp
);

echo "response: \n $response \n";

