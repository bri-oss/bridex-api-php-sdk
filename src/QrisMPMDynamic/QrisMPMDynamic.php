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
  private string $pathGenerateQR = '/v1.1/qr-dynamic-mpm/qr-mpm-generate-qr';
  private string $pathInquiryPayment = '/v1.1/qr-dynamic-mpm/qr-mpm-query';

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
    string $timestamp,
    array $body
  ): string {
    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $this->pathGenerateQR,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

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
    string $timestamp,
    array $body
  ): string {
    

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamic(
      $clientSecret,
      $partnerId,
      $this->pathInquiryPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiryPayment",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
