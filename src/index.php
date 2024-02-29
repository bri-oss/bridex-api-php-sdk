<?php

include 'util.php';
require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..' . '')->load();

require __DIR__ . '/../briapi-sdk/autoload.php';
use BRI\Balance\Balance;
use BRI\Token\AccessToken;
use BRI\Util\VarNumber;
use BRI\Signature\Signature;

// env values
$clientId = $_ENV['CONSUMER_KEY']; // customer key
$clientSecret = $_ENV['CONSUMER_SECRET']; // customer secret
$pKeyId = $_ENV['PRIVATE_KEY']; // private key

// url path values
$baseUrl = 'https://sandbox.partner.api.bri.co.id'; //base url
$path = '/snap/v1.0/balance-inquiry'; //informasi rekening api path
$accessTokenPath = '/snap/v1.0/access-token/b2b'; //access token path

// change variables accordingly
$account = '111231271284142'; // account number
$partnerId = ''; //partner id
$channelId = ''; // channel id

//external id
$externalId = (new VarNumber())->generateVar(9);

// fetches a new access token every specified minute with a maximum of 15 minutes
$minutes = 15;

if (!file_exists('accessToken.txt') || isTokenExpired('timestamp.txt', $minutes)) {
  //timestamp
  $timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:s.000P');

  //access token
  $accessToken = (new AccessToken(new Signature()))->getAccessToken(
    $clientId,
    $pKeyId,
    $timestamp,
    $baseUrl,
    $accessTokenPath,
  );

  file_put_contents('accessToken.txt', $accessToken);
  file_put_contents('timestamp.txt', $timestamp);

  echo "New Token is created\n";
} else {
  $accessToken = trim(file_get_contents('accessToken.txt'));
  $timestamp = trim(file_get_contents('timestamp.txt'));
  echo "Used Token\n";
}

echo (new Balance())->inquiry($account, $clientSecret, $partnerId, $baseUrl, $path, $accessToken, $channelId, $externalId, $timestamp);
