<?php

namespace BRI\Balance;

use BRI\Signature\Signature;
use DateTime;
use DateTimeZone;

class Balance
{
  private const METHOD = 'POST';
  private const CONTENT_TYPE = 'application/json';

  private function prepareRequest(
    string $account,
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timezone,
    ?string $additionalBody = null
  ) {
    // Body request
    $dataRequest = ['accountNo' => $account];
    if ($additionalBody !== null) {
      $dataRequest = array_merge($dataRequest, json_decode($additionalBody, true));
    }
    $bodyRequest = json_encode($dataRequest, true);

    // Generate random number for X-External-id and timestamp
    $timestamp = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d\TH:i:s.000P');

    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, self::METHOD, $timestamp, $accessToken, $bodyRequest, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . $channelId,
      "X-EXTERNAL-ID: $externalId",
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
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timezone = 'Asia/Jakarta',
  ) {
    list($bodyRequest, $headersRequest) = $this->prepareRequest(
      $account,
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $externalId,
      $timezone,
    );

    return $this->executeCurlRequest("$baseUrl$path", $headersRequest, $bodyRequest);
  }

  public function statement(
    string $account,
    string $startDate,
    string $endDate,
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timezone = 'Asia/Jakarta',
  ) {
    $dataRequest = [
      'accountNo' => $account,
      'fromDateTime' => $startDate,
      'toDateTime' => $endDate
    ];
    $bodyRequest = json_encode($dataRequest, true);

    list(, $headersRequest) = $this->prepareRequest(
      $account,
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $externalId,
      $timezone,
      $bodyRequest,
    );

    return $this->executeCurlRequest("$baseUrl$path", $headersRequest, $bodyRequest);
  }
}
