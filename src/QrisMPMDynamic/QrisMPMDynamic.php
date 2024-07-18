<?php

namespace BRI\QrisMPMDynamic;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface QrisMPMDynamicInterface {
  public function generateQR(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function inquiryPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
}

class QrisMPMDynamic implements QrisMPMDynamicInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathGenerateQR = '/v1.1/qr-dynamic-mpm/qr-mpm-generate-qr';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function generateQR(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'partnerReferenceNo' => '1234567890133',
      'amount' => (object) [
        'value' => '123456.00',
        'currency' => 'IDR',
      ],
      'merchantId' => '00007100010926',
      'terminalId' => '213141251124'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $this->pathGenerateQR,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathGenerateQR",
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
    string $timestamp
  ): string {
    $additionalBody = [
      'partnerReferenceNo' => '1234567890133',
      'serviceCode' => '17',
      'additionalInfo' => (object) [
        'terminalId' => '100492'
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $this->pathGenerateQR,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathGenerateQR",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
