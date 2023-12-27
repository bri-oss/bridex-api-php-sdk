<?php

namespace BRI\Balance;

use BRI\Token\AccessToken;
use BRI\Signature\Signature;
use BRI\Util\RandomNumber;
use DateTime;
use DateTimeZone;

class Balance
{
  private const METHOD = 'POST';
  private const CONTENT_TYPE = 'application/json';
  private const CHANNEL_ID = 'SNPBI';

  private function prepareRequest(
    string $account,
    string $clientId,
    string $clientSecret,
    string $pKeyId,
    string $partnerId,
    string $baseUrl,
    string $path,
    string $accessTokenPath,
    string $timezone = 'Asia/Jakarta',
    int $randomLength = 9
  ) {
    // Body request
    $dataRequest = ['accountNo' => $account];
    $bodyRequest = json_encode($dataRequest, true);

    // Generate random number for X-External-id and timestamp
    $randomNumber = (new RandomNumber())->generateRandomNumber($randomLength);
    $timestamp = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d\TH:i:s.000P');

    // Access Token
    $accessToken = (new AccessToken(new Signature()))->getAccessToken(
      $clientId,
      $pKeyId,
      $timestamp,
      $baseUrl,
      $accessTokenPath,
    );

    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, self::METHOD, $timestamp, $accessToken, $bodyRequest, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . self::CHANNEL_ID,
      "X-EXTERNAL-ID: $randomNumber",
      "Authorization: Bearer $accessToken",
    ];

    return [$bodyRequest, $headersRequest];
  }

  private function executeCurlRequest(string $url, array $headers, string $body)
  {
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => self::METHOD,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

  public function inquiry(
    string $account,
    string $clientId,
    string $clientSecret,
    string $pKeyId,
    string $partnerId,
    string $baseUrl,
    string $path,
    string $accessTokenPath,
    string $timezone = 'Asia/Jakarta',
    int $randomLength = 9
  ) {
    list($bodyRequest, $headersRequest) = $this->prepareRequest(
      $account,
      $clientId,
      $clientSecret,
      $pKeyId,
      $partnerId,
      $baseUrl,
      $path,
      $accessTokenPath,
      $timezone,
      $randomLength
    );

    return $this->executeCurlRequest("$baseUrl$path", $headersRequest, $bodyRequest);
  }

  public function statement(
    string $account,
    string $startDate,
    string $endDate,
    string $clientId,
    string $clientSecret,
    string $pKeyId,
    string $partnerId,
    string $baseUrl,
    string $path,
    string $accessTokenPath,
    string $timezone = 'Asia/Jakarta',
    int $randomLength = 9
  ) {
    $dataRequest = [
      'accountNo' => $account,
      'fromDateTime' => $startDate,
      'toDateTime' => $endDate
    ];
    $bodyRequest = json_encode($dataRequest, true);

    list(, $headersRequest) = $this->prepareRequest(
      $account,
      $clientId,
      $clientSecret,
      $pKeyId,
      $partnerId,
      $baseUrl,
      $path,
      $accessTokenPath,
      $timezone,
      $randomLength
    );

    return $this->executeCurlRequest("$baseUrl$path", $headersRequest, $bodyRequest);
  }
}
