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
  public function paymentNotify(
    string $baseUrl,
    string $clientId,
    string $clientSecret,
    string $accessToken
  ): string;
  public function refundNotify(
    string $baseUrl,
    string $clientId,
    string $clientSecret,
    string $accessToken
  ): string;
}

class DirectDebit implements DirectDebitInterface {
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
      'payment' => '/snap/v2.0/debit/payment-host-to-host',
      'paymentStatus' => '/snap/v2.0/debit/status',
      'refundPayment' => '/snap/v2.0/debit/refund',
      'paymentNotify' => '/snap/v2.0/debit/notify',
      'refundNotify' => '/snap/v2.0/debit/notify/refund'
    ];

    if (!array_key_exists($type, $paths)) {
      throw new InvalidArgumentException("Invalid request type: $type");
    }

    return $paths[$type];
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

    $path = $this->getPath('payment');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    try {
      $response = $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest
      );
    } catch (\Exception $e) {
      // Log the exception, handle retry mechanisms, etc.
      throw new \RuntimeException('Error executing direct debit payment: ' . $e->getMessage());
    }

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

    $path = $this->getPath('paymentStatus');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    try {
      $response = $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest
      );
    } catch (\Exception $e) {
      // Log the exception, handle retry mechanisms, etc.
      throw new \RuntimeException('Error executing direct debit payment status: ' . $e->getMessage());
    }

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

    $path = $this->getPath('refundPayment');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($body, JSON_UNESCAPED_SLASHES)
    );

    try {
      $response = $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest
      );
    } catch (\Exception $e) {
      // Log the exception, handle retry mechanisms, etc.
      throw new \RuntimeException('Error executing direct debit refund payment: ' . $e->getMessage());
    }

    return $response;
  }

  public function paymentNotify(
    string $baseUrl,
    string $clientId,
    string $clientSecret,
    string $accessToken
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '202010290000000000056',
      'originalReferenceNo' => '2020102900000000000009',
      'amount' => (object) [
        'value' => '10000.00',
        'currency' => 'IDR'
      ],
      'latestTransactionStatus' => '00',
      'transactionStatusDesc' => 'success',
      'additionalInfo' => (object) [
        'merchantTrxid' => '30220107504',
        'remarks' => ''
      ]
    ];

    $path = $this->getPath('paymentNotify');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebitOutbound(
      $clientId,
      $clientSecret,
      $accessToken,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }

  public function refundNotify(
    string $baseUrl,
    string $clientId,
    string $clientSecret,
    string $accessToken
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '202010290000000000056',
      'originalReferenceNo' => '2020102900000000000009',
      'amount' => (object) [
        'value' => '10000.00',
        'currency' => 'IDR'
      ],
      'latestTransactionStatus' => '00',
      'transactionStatusDescription' => 'success',
      'additionalInfo' => (object) [
        'refundId' => '528786398613',
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebitOutbound(
      $clientId,
      $clientSecret,
      $accessToken,
      json_encode($additionalBody, true)
    );

    $path = $this->getPath('refundNotify');

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
