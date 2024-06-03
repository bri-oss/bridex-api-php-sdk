<?php

namespace BRI\Util;

use BRI\Signature\Signature;

class PrepareRequest {
  private const METHOD = 'POST';
  private const CONTENT_TYPE = 'application/json';

  public function VABrivaOnline(
    string $clientSecret,
    string $partnerId,
    string $path,
    string $accessToken,
    string $channelId,
    string $externalId,
    string $timestamp,
    ?string $additionalBody = null
  ) {
    // Signature request
    $signatureRequest = (new Signature())->generateRequest($clientSecret, self::METHOD, $timestamp, $accessToken, $additionalBody, $path);

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
}