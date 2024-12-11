<?php

namespace BRI\Util;

use BRI\Signature\Signature;
use DateTime;
use DateTimeZone;

class PrepareRequest {
  private const METHOD = 'POST';
  private const CONTENT_TYPE = 'application/json';

  public function VABrivaOnline(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $accessToken,
    string $url,
    ?string $method = 'POST',
    ?string $additionalBody = null
  ) {
    $date = new DateTime('now', new DateTimeZone('UTC'));

    $timestamp = $date->format("Y-m-d\TH:i:s.v\Z");

    $bodyHash = hash("sha256", $additionalBody);

    $requestPath = parse_url($url, PHP_URL_PATH);

    $stringToSign = "$method:$requestPath:$accessToken:$bodyHash:$timestamp";

    $signature = hash_hmac("sha512", $stringToSign, $clientSecret);

    // Header request
    $headersRequest = [
      "Content-Type: " . self::CONTENT_TYPE,
      "Authorization: Bearer $accessToken",
      "x-partner-id: $partnerId",
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signature",
      "X-CLIENT-KEY: $clientId"
    ];

    return [$additionalBody, $headersRequest];
  }

  public function VABrivaWS(
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . $channelId,
      "X-EXTERNAL-ID: $externalId",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function TransferCredit(
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . $channelId,
      "X-EXTERNAL-ID: $externalId",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function DirectDebit(
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . $channelId,
      "X-EXTERNAL-ID: $externalId",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function Brizzi(
    string $clientSecret,
    string $path,
    string $accessToken,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateBRIAPI($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "BRI-Timestamp: $timestamp",
      "BRI-Signature: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "BRI-External-Id: $externalId",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function Valas(
    string $clientSecret,
    string $path,
    string $accessToken,
    string $timestamp,
    string $partnerCode,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateBRIAPI($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "BRI-Timestamp: $timestamp",
      "BRI-Signature: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "partnerCode: $partnerCode",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function QrisMPMDynamic(
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null,
    ?string $method = 'POST'
  ): array {
    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, $method, $timestamp, $accessToken, $additionalBody, $path);

    // Header request
    $headersRequest = [
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureRequest",
      "Content-Type: " . self::CONTENT_TYPE,
      "X-PARTNER-ID: $partnerId",
      "CHANNEL-ID: " . $channelId,
      "X-EXTERNAL-ID: $externalId",
      "Authorization: Bearer " . $accessToken,
    ];

    return [$additionalBody, $headersRequest];
  }

  public function CardlessCashWithdrawal(
    string $clientId,
    string $secretKey,
    string $accessToken,
    string $url,
    ?string $method = 'POST',
    ?string $additionalBody = null
  ) {
    $date = new DateTime('now', new DateTimeZone('UTC'));

    $timestamp = $date->format("Y-m-d\TH:i:s.v\Z");

    $bodyHash = hash("sha256", $additionalBody);

    $requestPath = parse_url($url, PHP_URL_PATH);

    $stringToSign = "$method:$requestPath:$accessToken:$bodyHash:$timestamp";

    $signature = hash_hmac("sha512", $stringToSign, $secretKey);

    // Header request
    $headersRequest = [
      "Content-Type: " . self::CONTENT_TYPE,
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signature",
      "X-CLIENT-KEY: $clientId",
      "Authorization: Bearer $accessToken"
    ];

    return [$additionalBody, $headersRequest];
  }

  public function QrisMPMDynamicNotification(
    string $clientId,
    string $secretKey,
    string $externalId,
    string $ipAddress,
    string $deviceId,
    string $latitude,
    string $longitude,
    string $channelId,
    string $origin,
    string $accessToken,
    string $url,
    ?string $method = 'POST',
    ?string $additionalBody = null
  ): array {
    $date = new DateTime('now', new DateTimeZone('UTC'));

    $timestamp = $date->format("Y-m-d\TH:i:s.v\Z");

    $bodyHash = hash("sha256", $additionalBody);

    $requestPath = parse_url($url, PHP_URL_PATH);

    $stringToSign = "$method:$requestPath:$accessToken:$bodyHash:$timestamp";

    $signature = hash_hmac("sha512", $stringToSign, $secretKey);

    // Header request
    $headersRequest = [
      "Content-Type: " . self::CONTENT_TYPE,
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signature",
      "X-CLIENT-KEY: $clientId",
      "x-partner-id: $clientId",
      "x-external-id: $externalId",
      "x-ip-address: $ipAddress",
      "x-device-id: $deviceId",
      "x-latitude: $latitude",
      "x-longitude: $longitude",
      "channel-id: $channelId",
      "origin: $origin",
      "Authorization: Bearer $accessToken",
    ];

    return [$additionalBody, $headersRequest];
  }

  public function DirectDebitOutbound(
    string $clientId,
    string $clientSecret,
    string $accessToken,
    ?string $additionalBody = null
  ): array {
    $timestamp = (new DateTime('now'))->format('Y-m-d H:i:s');

    $stringToSign = "{$clientId}|{$timestamp}";
    $signatureToken = hash_hmac("sha256", $stringToSign, $clientSecret);

    // Header request
    $headersRequest = [
      "Content-Type: " . self::CONTENT_TYPE,
      "X-TIMESTAMP: $timestamp",
      "X-SIGNATURE: $signatureToken",
      "X-CLIENT-KEY: $clientId",
      "x-partner-id: $clientId",
      "Authorization: $accessToken",
    ];

    return [$additionalBody, $headersRequest];
  }
}