<?php

namespace BRI\DirectDebit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface DirectDebitInterface {
  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string;
  public function paymentStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string;
  public function refundPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string;
}

class DirectDebit implements DirectDebitInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathPayment = '/snap/v2.0/debit/payment-host-to-host';
  private string $pathPaymentStatus = '/snap/v2.0/debit/status';
  private string $pathrefundPayment = '/snap/v2.0/debit/refund';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string {
    if (
      !isset($body['partnerReferenceNo']) || !isset($body['urlParam']) || !isset($body['amount']) || !isset($body['chargeToken']) || !isset($body['bankCardToken']) || !isset($body['additionalInfo'])) {
      throw new InvalidArgumentException('invalid body');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPayment",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function paymentStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string {
    if (
      !isset($body['originalPartnerReferenceNo']) || !isset($body['originalReferenceNo']) || !isset($body['serviceCode'])) {
      throw new InvalidArgumentException('invalid body');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathPaymentStatus,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPaymentStatus",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function refundPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    array $body
  ): string {
    if (
      !isset($body['originalPartnerReferenceNo']) || !isset($body['originalReferenceNo']) || !isset($body['partnerRefundNo']) || !isset($body['refundAmount']) || !isset($body['reason']) || !isset($body['additionalInfo'])) {
      throw new InvalidArgumentException('invalid body');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathrefundPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathrefundPayment",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
