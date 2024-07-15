<?php

namespace BRI\Valas;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface ValasInterface {
  public function infoKursCounter(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
}

class Valas implements ValasInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInfoKursCounter = '/v2.0/valas-info/kurs-counter';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function infoKursCounter(string $clientSecret, string $partnerId, string $baseUrl, string $accessToken, string $channelId, string $timestamp): string
  {
    $additionalBody = [
      'dealtCurrency' => 'USD',
      'counterCurrency' => 'IDR',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInfoKursCounter,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInfoKursCounter",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
