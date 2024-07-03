<?php

namespace BRI\Util;

use BRI\Signature\Signature;
use BRI\Token\AccessToken;
use DateTime;
use DateTimeZone;

class GetAccessToken {
  private $accessTokenPath = '/snap/v1.0/access-token/b2b'; //access token path
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
      $timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:s.000P');

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
}
