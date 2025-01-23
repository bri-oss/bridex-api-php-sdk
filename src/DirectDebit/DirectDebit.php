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

  private function processRequest(
      string $type,
      array $bodyValidationKeys,
      callable $prepareRequestMethod,
      array $prepareArgs,
      string $baseUrl
  ): string {
      foreach ($bodyValidationKeys as $key) {
          if (!isset($prepareArgs[0]['body'][$key])) {
              throw new InvalidArgumentException("Missing required key: $key in body");
          }
      }

      $path = $this->getPath($type);
      $prepareArgs[] = $path; // Add the path to the prepareArgs for dynamic preparation
      list($bodyRequest, $headersRequest) = call_user_func_array($prepareRequestMethod, $prepareArgs);

      try {
          $response = $this->executeCurlRequest->execute(
              "$baseUrl$path",
              $headersRequest,
              $bodyRequest
          );
      } catch (\Exception $e) {
          throw new \RuntimeException("Error processing $type: " . $e->getMessage());
      }

      return $response;
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
    return $this->processRequest(
      'payment',
      ['partnerReferenceNo', 'urlParam', 'amount', 'chargeToken', 'bankCardToken', 'additionalInfo'],
      [$this->prepareRequest, 'DirectDebit'],
      [
          'body' => $body,
          $clientSecret,
          $partnerId,
          $accessToken,
          $channelId,
          $this->externalId,
          $timestamp,
          json_encode($body, JSON_UNESCAPED_SLASHES)
      ],
      $baseUrl
    );
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
    return $this->processRequest(
      'paymentStatus',
      ['originalPartnerReferenceNo', 'originalReferenceNo', 'serviceCode'],
      [$this->prepareRequest, 'DirectDebit'],
      [
          'body' => $body,
          $clientSecret,
          $partnerId,
          $accessToken,
          $channelId,
          $this->externalId,
          $timestamp,
          json_encode($body, JSON_UNESCAPED_SLASHES)
      ],
      $baseUrl
    );
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
    return $this->processRequest(
      'refundPayment',
      ['originalPartnerReferenceNo', 'originalReferenceNo', 'partnerRefundNo', 'refundAmount', 'reason', 'additionalInfo'],
      [$this->prepareRequest, 'DirectDebit'],
      [
          'body' => $body,
          $clientSecret,
          $partnerId,
          $accessToken,
          $channelId,
          $this->externalId,
          $timestamp,
          json_encode($body, JSON_UNESCAPED_SLASHES)
      ],
      $baseUrl
    );
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

    return $this->processRequest(
        'paymentNotify',
        [],
        [$this->prepareRequest, 'DirectDebitOutbound'],
        [$clientId, $clientSecret, $accessToken, json_encode($additionalBody, true)],
        $baseUrl
    );
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

    return $this->processRequest(
      'refundNotify',
      [],
      [$this->prepareRequest, 'DirectDebitOutbound'],
      [$clientId, $clientSecret, $accessToken, json_encode($additionalBody, true)],
      $baseUrl
    );
  }
}
