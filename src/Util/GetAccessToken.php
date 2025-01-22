<?php

namespace BRI\Util;

use BRI\Signature\Signature;
use BRI\Token\AccessToken;
use BRI\Token\AccessTokenBRIAPI;
use BRI\Token\AccessTokenMockOutbound;
use DateTime;
use DateTimeZone;

class GetAccessToken {
  private $accessTokenPath = '/snap/v1.0/access-token/b2b'; //access token path
  private $accessTokenBRIAPIPath = '/oauth/client_credential/accesstoken?grant_type=client_credentials'; // path access token bri api
  private $accessTokenMockOutbound = '/snap/v1.1/access-token/b2b';
  private $minutes = 15;

  public function setMinutes($minutes){
    $this->minutes = $minutes;
  }

  private function isTokenExpired($timestampFile)
  {
    $currentTime = time();
    $storedTimestamp = strtotime(trim(file_get_contents($timestampFile)));

    // Check minutes passed since the stored timestamp
    $minutesPassed = ($currentTime - $storedTimestamp) / 60;

    return $minutesPassed >= $this->minutes;
  }

  public function get(
    string $clientId,
    string $pKeyId,
    string $baseUrl
  ) {
    // fetches a new access token every specified minute with a maximum of 15 minutes

    if (!file_exists('accessToken.txt') || $this->isTokenExpired('timestamp.txt')) {
      //timestamp
      // $timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:s.000P');
      $timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:sP');

      //access token
      $accessToken = (new AccessToken(new Signature()))->getAccessToken(
        $clientId,
        $pKeyId,
        $timestamp,
        $baseUrl,
        $this->accessTokenPath,
      );

      file_put_contents('accessToken.txt', $accessToken);
      file_put_contents('timestamp.txt', $timestamp);

      // echo "New Token is created\n";
    } else {
      $accessToken = trim(file_get_contents('accessToken.txt'));
      $timestamp = trim(file_get_contents('timestamp.txt'));
      // echo "Used Token\n";
    }

    return [$accessToken, $timestamp];
  }

  public function getBRIAPI(
    string $clientId,
    string $clientSecret,
    string $baseUrl
  ): string {
    $accessToken = (new AccessTokenBRIAPI())->getAccessToken(
      $clientId,
      $clientSecret,
      $baseUrl,
      $this->accessTokenBRIAPIPath,
    );

    file_put_contents('accessToken.txt', $accessToken);

    return $accessToken;
  }

  public function getMockOutbound(
    string $clientId,
    string $baseUrl,
    string $privateKey
  ): string {
    $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));

    // Tambahkan milidetik secara manual
    $milliseconds = sprintf('%03d', round(microtime(true) * 1000) % 1000);

    // Format ke ISO 8601 dengan menambahkan milidetik
    $timestamp = $date->format("Y-m-d\TH:i:s") . ".$milliseconds" . $date->format("P");

    $accessToken = (new AccessTokenMockOutbound())->getAccessToken(
      $clientId,
      $timestamp,
      $baseUrl,
      $this->accessTokenMockOutbound,
      $privateKey
    );

    file_put_contents('accessToken.txt', $accessToken);

    return $accessToken;
  }
}
