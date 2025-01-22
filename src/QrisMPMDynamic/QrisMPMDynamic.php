<?php

namespace BRI\QrisMPMDynamic;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface QrisMPMDynamicInterface {
  public function generateQR(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string;
  public function inquiryPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string;
}

class QrisMPMDynamic implements QrisMPMDynamicInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
    $this->externalId = (new VarNumber())->generateVar(9);
  }

  private function getPath(string $type): string {
    $paths = [
      'generateQR' => '/v1.1/qr-dynamic-mpm/qr-mpm-generate-qr',
      'inquiryPayment' => '/v1.1/qr-dynamic-mpm/qr-mpm-query'
    ];

    if (!array_key_exists($type, $paths)) {
      throw new InvalidArgumentException("Invalid request type: $type");
    }

    return $paths[$type];
  }

  public function generateQR(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string {
    $path = $this->getPath('generateQR');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function inquiryPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string {

    $path = $this->getPath('generateQR');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
